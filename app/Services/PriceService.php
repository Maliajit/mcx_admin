<?php

namespace App\Services;

use App\Models\TradingSetting;
use App\Models\ProductRow;
use App\Models\Coin;
use App\Services\LiveRates\LiveRatesService;
use Illuminate\Support\Facades\Cache;

class PriceService
{
    public function __construct(
        private readonly LiveRatesService $liveRatesService
    ) {}

    /**
     * Get all dynamically calculated config data for Flutter App.
     */
    public function getConfig(): array
    {
        $settings = $this->getSettings();
        $basePrices = $this->getBasePrices($settings);
        
        return [
            'base_prices' => $basePrices,
            'taxes' => [
                'gst' => (float) ($settings['gst_percentage'] ?? 3.0),
                'tds' => (float) ($settings['tds_percentage'] ?? 1.0),
            ],
            'theme' => [
                'primary_color' => $settings['primary_color'] ?? '#FFAA00',
                'secondary_color' => $settings['secondary_color'] ?? '#000000',
            ],
            'products' => $this->getCalculatedProducts($basePrices),
            'coins' => $this->getCalculatedCoins($basePrices),
        ];
    }

    /**
     * Get associative array of all TradingSettings.
     */
    public function getSettings(): array
    {
        return TradingSetting::all()->pluck('value', 'key')->toArray();
    }

    /**
     * Get live base prices for Gold and Silver.
     */
    public function getBasePrices(array $settings = null): array
    {
        $settings = $settings ?? $this->getSettings();
        $source = $settings['price_source'] ?? 'manual';
        
        $goldPrice = (float) ($settings['gold_base_price'] ?? 0);
        $silverPrice = (float) ($settings['silver_base_price'] ?? 0);

        if ($source === 'api') {
            $liveResult = $this->liveRatesService->getLiveRates();
            if ($liveResult['success']) {
                $items = collect($liveResult['data']['items'] ?? []);
                
                $liveGold = $items->firstWhere('symbol', 'GOLD') ?? $items->firstWhere('name', 'GOLD');
                if ($liveGold) {
                    $goldPrice = (float) $liveGold['bid'];
                }
                
                $liveSilver = $items->firstWhere('symbol', 'SILVER') ?? $items->firstWhere('name', 'SILVER');
                if ($liveSilver) {
                    $silverPrice = (float) $liveSilver['bid'];
                }
            }
        }

        return [
            'gold' => $goldPrice,
            'silver' => $silverPrice,
        ];
    }

    public function getCalculatedProducts(array $basePrices = null): array
    {
        $basePrices = $basePrices ?? $this->getBasePrices();
        $products = ProductRow::where('is_active', true)->get();

        $calculated = [];
        foreach ($products as $product) {
            $base = $basePrices[$product->type] ?? 0;
            // Intermediate price = base_price + margin + adjustment
            $intermediatePrice = $base + $product->margin + $product->adjustment;
            
            $calculated[] = [
                'id' => $product->id,
                'name' => $product->name,
                'type' => $product->type,
                'base_price' => (float) $base,
                'margin' => (float) $product->margin,
                'adjustment' => (float) $product->adjustment,
                'final_price' => max(0, $intermediatePrice), 
            ];
        }

        return $calculated;
    }

    public function getCalculatedCoins(array $basePrices = null): array
    {
        $basePrices = $basePrices ?? $this->getBasePrices();
        $coins = Coin::where('is_active', true)->get();

        $calculated = [];
        foreach ($coins as $coin) {
            $base = $basePrices[$coin->type] ?? 0;
            $pricePerGram = $coin->type === 'gold' ? ($base / 10) : ($base / 1000); 
            
            $coinBasePrice = $pricePerGram * $coin->weight_in_grams;
            // Intermediate price = base_price + margin
            $intermediatePrice = $coinBasePrice + $coin->margin;

            $calculated[] = [
                'id' => $coin->id,
                'name' => $coin->name,
                'type' => $coin->type,
                'base_price' => (float) $coinBasePrice,
                'weight_in_grams' => (float) $coin->weight_in_grams,
                'margin' => (float) $coin->margin,
                'adjustment' => 0,
                'final_price' => max(0, $intermediatePrice),
            ];
        }

        return $calculated;
    }
    
    /**
     * Compute total cost and taxes for an order
     */
    public function calculateOrderTaxes(float $intermediatePrice, float $quantity): array
    {
        $settings = $this->getSettings();
        $gstPercent = (float) ($settings['gst_percentage'] ?? 0);
        $tdsPercent = (float) ($settings['tds_percentage'] ?? 0);

        $subtotal = $intermediatePrice * $quantity;
        $gstAmount = $subtotal * ($gstPercent / 100);
        $tdsAmount = $subtotal * ($tdsPercent / 100);

        return [
            'unit_rate' => $intermediatePrice,
            'quantity' => $quantity,
            'subtotal' => $subtotal,
            'gst_percent' => $gstPercent,
            'gst_amount' => $gstAmount,
            'tds_percent' => $tdsPercent,
            'tds_amount' => $tdsAmount,
            'total_tax' => $gstAmount + $tdsAmount,
            'grand_total' => $subtotal + $gstAmount + $tdsAmount,
        ];
    }
}

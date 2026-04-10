<?php

namespace App\Services;

use App\Models\Coin;
use App\Models\Order;
use App\Models\User;
use Exception;

class OrderLimitService
{
    public function getMetalType(string $assetName): string
    {
        return str_contains(strtolower($assetName), 'silver') ? 'silver' : 'gold';
    }

    public function resolveLimitUsageInGrams(
        string $productType,
        int $productId,
        float $quantity
    ): float {
        if ($productType !== 'coin') {
            return $quantity;
        }

        $coin = Coin::query()->find($productId);

        if (!$coin) {
            throw new Exception('Selected coin was not found.');
        }

        return $quantity * (float) $coin->weight_in_grams;
    }

    public function assertWithinRemainingLimit(
        User $user,
        string $assetName,
        string $productType,
        int $productId,
        float $quantity
    ): float {
        $verifiedUser = $user->verifiedUser;

        if (!$verifiedUser) {
            throw new Exception('KYC documentation not submitted.');
        }

        $gramsToConsume = $this->resolveLimitUsageInGrams($productType, $productId, $quantity);
        $metalType = $this->getMetalType($assetName);
        $remainingLimit = $metalType === 'silver'
            ? (float) $verifiedUser->silver_limit
            : (float) $verifiedUser->gold_limit;

        if ($gramsToConsume > $remainingLimit) {
            $label = ucfirst($metalType);
            throw new Exception("Requested quantity exceeds your remaining {$label} trading limit of {$remainingLimit}g.");
        }

        return $gramsToConsume;
    }

    public function consumeRemainingLimit(Order $order): void
    {
        if ($order->status !== 'confirmed') {
            return;
        }

        $user = $order->user;
        $verifiedUser = $user?->verifiedUser;

        if (!$user || !$verifiedUser) {
            return;
        }

        $gramsToConsume = $this->resolveLimitUsageInGrams(
            (string) $order->product_type,
            (int) $order->product_id,
            (float) $order->quantity
        );

        $metalType = $this->getMetalType((string) $order->asset);

        if ($metalType === 'silver') {
            $verifiedUser->silver_limit = max(0, (float) $verifiedUser->silver_limit - $gramsToConsume);
        } else {
            $verifiedUser->gold_limit = max(0, (float) $verifiedUser->gold_limit - $gramsToConsume);
        }

        $verifiedUser->save();
    }
}

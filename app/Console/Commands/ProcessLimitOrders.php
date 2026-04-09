<?php

namespace App\Console\Commands;

use App\Services\OrderProcessingService;
use Illuminate\Console\Command;

class ProcessLimitOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:process-limits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending limit orders based on live base prices';

    /**
     * Execute the console command.
     */
    public function handle(OrderProcessingService $service): void
    {
        $this->info('Scanning for limit order hits...');
        
        $processed = $service->processPendingLimits();
        
        if ($processed > 0) {
            $this->info("Successfully processed {$processed} limit orders.");
        } else {
            $this->line('No hits detected.');
        }
    }
}

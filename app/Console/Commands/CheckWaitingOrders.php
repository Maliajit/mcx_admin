<?php

namespace App\Console\Commands;

use App\Services\PriceCheckService;

class CheckWaitingOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-waiting-orders';

    protected $description = 'Check waiting limit orders against live prices';

    public function __construct(
        private readonly PriceCheckService $priceCheckService,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking waiting orders...');
        $this->priceCheckService->checkWaitingOrders();
        $this->info('Done.');
    }
}

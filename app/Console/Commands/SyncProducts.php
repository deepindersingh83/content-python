<?php

namespace App\Console\Commands;

use App\Services\ProductSyncService;
use Illuminate\Console\Command;

class SyncProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync products from supplier tables into the master products table';

    /**
     * Execute the console command.
     */
    public function handle(ProductSyncService $syncService)
    {
        $this->info('Starting product synchronization...');

        $result = $syncService->syncAllProducts();

        if ($result['success']) {
            $this->info($result['message']);
        } else {
            $this->error($result['message']);
        }

        return $result['success'] ? 0 : 1;
    }
}

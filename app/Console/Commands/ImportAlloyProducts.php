<?php

namespace App\Console\Commands;

use App\Services\AlloyImportService;
use Illuminate\Console\Command;

class ImportAlloyProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alloy:import {file : Path to the Alloy CSV file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Alloy (Supplier 2) products from CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');

        $this->info('Starting Alloy product import...');
        $this->info('File: ' . $filePath);

        $importService = new AlloyImportService();
        $result = $importService->importCsv($filePath);

        if ($result['success']) {
            $this->info('✓ ' . $result['message']);

            if (isset($result['statistics'])) {
                $stats = $result['statistics'];
                $this->newLine();
                $this->info('Statistics:');
                $this->line('  • New products imported: ' . $stats['imported']);
                $this->line('  • Existing products updated: ' . $stats['updated']);
                $this->line('  • Rows skipped: ' . $stats['skipped']);
                $this->line('  • Total rows processed: ' . $stats['total_rows']);
            }

            if (!empty($result['errors'])) {
                $this->newLine();
                $this->warn('Errors encountered:');
                foreach ($result['errors'] as $error) {
                    $this->line('  • ' . $error);
                }
            }

            $this->newLine();
            $this->info('Run "php artisan products:sync" to sync these products to the master products table.');

            return Command::SUCCESS;
        } else {
            $this->error('✗ ' . $result['message']);
            return Command::FAILURE;
        }
    }
}

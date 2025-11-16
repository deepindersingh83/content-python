<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\SupplierMappingService;

class ImportSupplierData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supplier:import
                            {supplier : The supplier key (e.g., ls, supplier1, supplier2)}
                            {file : Path to the CSV or JSON file}
                            {--format=csv : File format (csv or json)}
                            {--truncate : Truncate supplier table before import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import supplier product data from CSV or JSON file';

    protected SupplierMappingService $mappingService;

    public function __construct(SupplierMappingService $mappingService)
    {
        parent::__construct();
        $this->mappingService = $mappingService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $supplierKey = $this->argument('supplier');
        $filePath = $this->argument('file');
        $format = $this->option('format');
        $truncate = $this->option('truncate');

        // Validate supplier configuration
        $config = $this->mappingService->getSupplierConfig($supplierKey);

        if (!$config) {
            $this->error("Supplier '{$supplierKey}' not found in configuration.");
            return 1;
        }

        if (!($config['enabled'] ?? false)) {
            $this->error("Supplier '{$supplierKey}' is not enabled.");
            return 1;
        }

        // Validate file exists
        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        // Get model class
        $modelClass = $config['model'] ?? null;

        if (!$modelClass || !class_exists($modelClass)) {
            $this->error("Model class not found for supplier: {$supplierKey}");
            return 1;
        }

        $this->info("Importing data for supplier: {$config['name']}");
        $this->info("File: {$filePath}");
        $this->info("Format: {$format}");

        // Truncate table if requested
        if ($truncate) {
            if ($this->confirm('Are you sure you want to truncate the supplier table?')) {
                DB::table($config['table'])->truncate();
                $this->info("Table '{$config['table']}' truncated.");
            }
        }

        // Import data based on format
        try {
            $importedCount = match ($format) {
                'csv' => $this->importFromCsv($filePath, $modelClass, $supplierKey),
                'json' => $this->importFromJson($filePath, $modelClass, $supplierKey),
                default => throw new \Exception("Unsupported format: {$format}")
            };

            $this->info("Successfully imported {$importedCount} products.");
            return 0;
        } catch (\Exception $e) {
            $this->error("Import failed: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Import data from CSV file
     */
    protected function importFromCsv(string $filePath, string $modelClass, string $supplierKey): int
    {
        $handle = fopen($filePath, 'r');

        if (!$handle) {
            throw new \Exception("Cannot open file: {$filePath}");
        }

        // Read header row
        $headers = fgetcsv($handle);

        if (!$headers) {
            throw new \Exception("CSV file has no headers");
        }

        $importedCount = 0;
        $bar = $this->output->createProgressBar();
        $bar->start();

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) !== count($headers)) {
                    continue; // Skip malformed rows
                }

                // Combine headers with row data
                $data = array_combine($headers, $row);

                // Map to internal format
                $mappedData = $this->mappingService->mapToInternal($supplierKey, $data);

                // Create or update
                $modelClass::updateOrCreate(
                    ['supplier_code' => $mappedData['supplier_code'] ?? null],
                    $mappedData
                );

                $importedCount++;
                $bar->advance();
            }

            DB::commit();
            $bar->finish();
            $this->newLine();

            fclose($handle);

            return $importedCount;
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            throw $e;
        }
    }

    /**
     * Import data from JSON file
     */
    protected function importFromJson(string $filePath, string $modelClass, string $supplierKey): int
    {
        $content = file_get_contents($filePath);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid JSON: " . json_last_error_msg());
        }

        if (!is_array($data)) {
            throw new \Exception("JSON must contain an array of products");
        }

        $importedCount = 0;
        $bar = $this->output->createProgressBar(count($data));
        $bar->start();

        DB::beginTransaction();

        try {
            foreach ($data as $item) {
                // Map to internal format
                $mappedData = $this->mappingService->mapToInternal($supplierKey, $item);

                // Create or update
                $modelClass::updateOrCreate(
                    ['supplier_code' => $mappedData['supplier_code'] ?? null],
                    $mappedData
                );

                $importedCount++;
                $bar->advance();
            }

            DB::commit();
            $bar->finish();
            $this->newLine();

            return $importedCount;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

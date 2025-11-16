<?php

namespace App\Services;

class SupplierMappingService
{
    /**
     * Get all enabled suppliers sorted by priority
     *
     * @return array
     */
    public function getEnabledSuppliers(): array
    {
        $suppliers = config('suppliers.suppliers', []);

        // Filter enabled suppliers
        $enabled = array_filter($suppliers, function ($supplier) {
            return $supplier['enabled'] ?? false;
        });

        // Sort by priority (lower number = higher priority)
        uasort($enabled, function ($a, $b) {
            return ($a['priority'] ?? 999) <=> ($b['priority'] ?? 999);
        });

        return $enabled;
    }

    /**
     * Get supplier configuration by key
     *
     * @param string $supplierKey
     * @return array|null
     */
    public function getSupplierConfig(string $supplierKey): ?array
    {
        return config("suppliers.suppliers.{$supplierKey}");
    }

    /**
     * Map supplier data to internal format
     *
     * @param string $supplierKey
     * @param array $supplierData
     * @return array
     */
    public function mapToInternal(string $supplierKey, array $supplierData): array
    {
        $config = $this->getSupplierConfig($supplierKey);

        if (!$config) {
            throw new \InvalidArgumentException("Supplier '{$supplierKey}' not found in configuration");
        }

        $mappings = $config['mappings'] ?? [];
        $mapped = [];

        foreach ($mappings as $supplierField => $internalField) {
            // Get the value from supplier data (case-insensitive)
            $value = $this->getValueCaseInsensitive($supplierData, $supplierField);

            if ($value !== null) {
                $mapped[$internalField] = $value;
            }
        }

        return $mapped;
    }

    /**
     * Map internal data to supplier format
     *
     * @param string $supplierKey
     * @param array $internalData
     * @return array
     */
    public function mapToSupplier(string $supplierKey, array $internalData): array
    {
        $config = $this->getSupplierConfig($supplierKey);

        if (!$config) {
            throw new \InvalidArgumentException("Supplier '{$supplierKey}' not found in configuration");
        }

        $mappings = $config['mappings'] ?? [];
        $mapped = [];

        foreach ($mappings as $supplierField => $internalField) {
            if (isset($internalData[$internalField])) {
                $mapped[$supplierField] = $internalData[$internalField];
            }
        }

        return $mapped;
    }

    /**
     * Merge data from multiple suppliers based on priority
     *
     * @param array $supplierDataSets Array of ['supplier_key' => data]
     * @return array
     */
    public function mergeSupplierData(array $supplierDataSets): array
    {
        $suppliers = $this->getEnabledSuppliers();
        $merged = [];

        // Get all possible internal fields from all suppliers
        $allFields = [];
        foreach ($suppliers as $key => $config) {
            $allFields = array_merge($allFields, array_values($config['mappings'] ?? []));
        }
        $allFields = array_unique($allFields);

        // Merge data field by field, respecting priority
        foreach ($allFields as $field) {
            foreach ($suppliers as $supplierKey => $config) {
                if (isset($supplierDataSets[$supplierKey][$field])) {
                    $value = $supplierDataSets[$supplierKey][$field];

                    // Use the first non-empty value based on priority
                    if (!isset($merged[$field]) || $this->isEmpty($merged[$field])) {
                        $merged[$field] = $value;
                    }
                }
            }
        }

        return $merged;
    }

    /**
     * Get value from array with case-insensitive key lookup
     *
     * @param array $data
     * @param string $key
     * @return mixed|null
     */
    private function getValueCaseInsensitive(array $data, string $key): mixed
    {
        // Try exact match first
        if (array_key_exists($key, $data)) {
            return $data[$key];
        }

        // Try case-insensitive match
        $lowerKey = strtolower($key);
        foreach ($data as $k => $v) {
            if (strtolower($k) === $lowerKey) {
                return $v;
            }
        }

        return null;
    }

    /**
     * Check if a value is considered empty for merging purposes
     *
     * @param mixed $value
     * @return bool
     */
    private function isEmpty(mixed $value): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        if (is_numeric($value) && $value == 0) {
            return false; // 0 is a valid value
        }

        return empty($value);
    }

    /**
     * Get unique identifier from product data
     *
     * @param array $data
     * @return string|null
     */
    public function getUniqueIdentifier(array $data): ?string
    {
        // Priority order for unique identifiers
        $identifierFields = [
            'supplier_code',
            'sku',
            'brand_sku',
            'barcode',
            'ean',
            'upc',
            'asin',
            'isbn',
        ];

        foreach ($identifierFields as $field) {
            if (!empty($data[$field])) {
                return $data[$field];
            }
        }

        return null;
    }

    /**
     * Validate that required fields are present
     *
     * @param array $data
     * @return bool
     */
    public function validateRequiredFields(array $data): bool
    {
        $required = config('suppliers.required_fields', []);

        foreach ($required as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }

        return true;
    }
}

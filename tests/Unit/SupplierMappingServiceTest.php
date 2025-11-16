<?php

namespace Tests\Unit;

use App\Services\SupplierMappingService;
use Tests\TestCase;

class SupplierMappingServiceTest extends TestCase
{
    protected SupplierMappingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SupplierMappingService();
    }

    public function test_can_get_alloy_supplier_config(): void
    {
        $config = $this->service->getSupplierConfig('alloy');

        $this->assertNotNull($config);
        $this->assertEquals('Alloy', $config['name']);
        $this->assertTrue($config['enabled']);
        $this->assertEquals(3, $config['priority']);
    }

    public function test_can_map_alloy_data_to_internal_format(): void
    {
        $alloyData = [
            'PartNumber' => 'ALLOY-12345',
            'Name' => 'Test Product',
            'Manufacturer' => 'Test Brand',
            'PriceCostEx' => '100.00',
            'PriceRetailEx' => '150.00',
            'Quantity' => '50',
            'EAN' => '1234567890123',
            'CategoryName' => 'Electronics',
            'Description' => 'Test product description',
            'Weight' => '1.5',
            'Height' => '10',
            'Width' => '20',
            'Depth' => '30',
            'Qty_SYD' => '10',
            'Qty_MEL' => '15',
            'Qty_BNE' => '12',
            'Qty_ADL' => '13',
        ];

        $mapped = $this->service->mapToInternal('alloy', $alloyData);

        $this->assertEquals('ALLOY-12345', $mapped['supplier_code']);
        $this->assertEquals('Test Product', $mapped['name']);
        $this->assertEquals('Test Brand', $mapped['brand_name']);
        $this->assertEquals('100.00', $mapped['cost_price']);
        $this->assertEquals('150.00', $mapped['retail_price']);
        $this->assertEquals('50', $mapped['stock_total']);
        $this->assertEquals('1234567890123', $mapped['barcode']);
        $this->assertEquals('Electronics', $mapped['category_name']);
        $this->assertEquals('Test product description', $mapped['description']);
        $this->assertEquals('1.5', $mapped['weight']);
        $this->assertEquals('10', $mapped['height']);
        $this->assertEquals('20', $mapped['width']);
        $this->assertEquals('30', $mapped['length']);
        $this->assertEquals('10', $mapped['stock_warehouse_syd']);
        $this->assertEquals('15', $mapped['stock_warehouse_mel']);
        $this->assertEquals('12', $mapped['stock_warehouse_bne']);
        $this->assertEquals('13', $mapped['stock_warehouse_adl']);
    }

    public function test_can_map_internal_data_to_alloy_format(): void
    {
        $internalData = [
            'supplier_code' => 'ALLOY-12345',
            'name' => 'Test Product',
            'brand_name' => 'Test Brand',
            'cost_price' => '100.00',
            'retail_price' => '150.00',
            'stock_total' => '50',
            'barcode' => '1234567890123',
            'category_name' => 'Electronics',
        ];

        $mapped = $this->service->mapToSupplier('alloy', $internalData);

        $this->assertEquals('ALLOY-12345', $mapped['PartNumber']);
        $this->assertEquals('Test Product', $mapped['Name']);
        $this->assertEquals('Test Brand', $mapped['Manufacturer']);
        $this->assertEquals('100.00', $mapped['PriceCostEx']);
        $this->assertEquals('150.00', $mapped['PriceRetailEx']);
        $this->assertEquals('50', $mapped['Quantity']);
        $this->assertEquals('1234567890123', $mapped['EAN']);
        $this->assertEquals('Electronics', $mapped['CategoryName']);
    }

    public function test_alloy_mappings_are_complete(): void
    {
        $config = $this->service->getSupplierConfig('alloy');
        $mappings = $config['mappings'];

        // Verify all expected Alloy fields are mapped
        $expectedAlloyFields = [
            'PartNumber',
            'Name',
            'Manufacturer',
            'PriceCostEx',
            'PriceRetailEx',
            'Quantity',
            'EAN',
            'CategoryName',
            'Description',
            'Weight',
            'Height',
            'Width',
            'Depth',
            'Qty_SYD',
            'Qty_MEL',
            'Qty_BNE',
            'Qty_ADL',
            'ManufacPrefix',
            'SupplierPartNumber',
            'Category',
            'Group',
            'HTMLDescription',
            'FeaturesBenefits',
            'MarketingComments',
            'GeneralComments',
            'TaxType',
            'TaxRate',
            'Unit',
            'Warranty',
            'ProductSpecificURL',
            'image_thumbnail',
            'PDF_Available',
            'ETADate',
            'ETAStatus',
            'StockRecordUpdated',
        ];

        foreach ($expectedAlloyFields as $field) {
            $this->assertArrayHasKey(
                $field,
                $mappings,
                "Field '{$field}' is not mapped in Alloy configuration"
            );
        }
    }

    public function test_alloy_is_included_in_enabled_suppliers(): void
    {
        $suppliers = $this->service->getEnabledSuppliers();

        $this->assertArrayHasKey('alloy', $suppliers);
        $this->assertEquals('Alloy', $suppliers['alloy']['name']);
    }
}

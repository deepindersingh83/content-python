# Alloy Supplier (Supplier 2) Field Mapping Documentation

This document describes the field mapping configuration for Alloy, the second supplier in the multi-supplier product management system.

## Table of Contents
- [Overview](#overview)
- [Field Mappings](#field-mappings)
- [Import Process](#import-process)
- [Usage Examples](#usage-examples)
- [Technical Details](#technical-details)

---

## Overview

**Alloy** is configured as **Supplier 2** in the Laravel Product Management System. The system provides:

1. **Automatic field mapping** from Alloy CSV format to standardized database fields
2. **Extended field storage** for Alloy-specific data (manufacturer info, warehouse quantities, etc.)
3. **CSV import service** with validation and error handling
4. **Multi-supplier sync** to merge Alloy products with other suppliers into a master products table

---

## Field Mappings

### Standard Field Mappings

These Alloy fields are mapped to the standard product database schema:

| Alloy CSV Column | Database Field | Description |
|-----------------|----------------|-------------|
| **PartNumber** | `sku` | Product SKU (primary identifier) |
| **EAN** | `ean` | European Article Number barcode |
| **SupplierPartNumber** | `upc` | Universal Product Code |
| **Name** | `name` | Product name |
| **Description** | `shortdescription` | Short product description |
| **HTMLDescription** | `longdescription` | Full HTML product description |
| **Category** | `category1` | Primary category |
| **CategoryName** | `category2` | Secondary category |
| **Group** | `category3` | Product group (tertiary category) |
| **PriceCostEx** | `costprice` | Cost price (ex-tax) |
| **PriceRetailEx** | `saleprice` | Retail price (ex-tax) |
| **Quantity** | `quantity` | Total stock quantity |
| **Width** | `width` | Product width |
| **Height** | `height` | Product height |
| **Depth** | `length` | Product depth (mapped to length) |
| **Weight** | `weight` | Product weight |
| **image_thumbnail** | `imagesrc` | Product image URL |

### Extended Alloy-Specific Fields

These fields are unique to Alloy and stored in `supplier2_products` table only:

| Alloy CSV Column | Database Field | Data Type | Description |
|-----------------|----------------|-----------|-------------|
| **ManufacPrefix** | `manufac_prefix` | String | Manufacturer prefix code |
| **Manufacturer** | `manufacturer` | String | Manufacturer name |
| **Unit** | `unit` | String | Unit of measurement |
| **TaxType** | `tax_type` | String | Tax classification |
| **TaxRate** | `tax_rate` | Decimal(5,2) | Tax rate percentage |
| **FeaturesBenefits** | `features_benefits` | Text | Product features and benefits |
| **MarketingComments** | `marketing_comments` | Text | Marketing copy |
| **GeneralComments** | `general_comments` | Text | General notes |
| **ProductSpecificURL** | `product_specific_url` | String | Product page URL |
| **Warranty** | `warranty` | String | Warranty information |
| **PDF_Available** | `pdf_available` | Boolean | PDF documentation available flag |
| **StockRecordUpdated** | `stock_record_updated` | Timestamp | Last stock update time |
| **ETADate** | `eta_date` | Date | Estimated arrival date |
| **ETAStatus** | `eta_status` | String | Estimated arrival status |
| **Qty_ADL** | `qty_adl` | Integer | Adelaide warehouse quantity |
| **Qty_BNE** | `qty_bne` | Integer | Brisbane warehouse quantity |
| **Qty_MEL** | `qty_mel` | Integer | Melbourne warehouse quantity |
| **Qty_SYD** | `qty_syd` | Integer | Sydney warehouse quantity |

### Warehouse Quantity Calculation

If the `Quantity` field is empty in the Alloy CSV, the system automatically calculates it by summing the warehouse quantities:

```
Total Quantity = Qty_ADL + Qty_BNE + Qty_MEL + Qty_SYD
```

---

## Import Process

### Step 1: Import Alloy CSV to Supplier2 Table

Use the Artisan command to import Alloy products:

```bash
php artisan alloy:import /path/to/alloy_products.csv
```

This command:
- Validates each row for required fields (PartNumber or EAN)
- Maps Alloy CSV columns to database fields
- Creates new products or updates existing ones (matched by SKU or EAN)
- Provides detailed statistics and error reporting

**Example Output:**
```
Starting Alloy product import...
File: /path/to/alloy_products.csv

✓ Import completed successfully

Statistics:
  • New products imported: 150
  • Existing products updated: 50
  • Rows skipped: 3
  • Total rows processed: 203

Run "php artisan products:sync" to sync these products to the master products table.
```

### Step 2: Sync to Master Products Table

After importing Alloy products, sync them to the master products table:

```bash
php artisan products:sync
```

This merges products from all suppliers (Supplier 1 + Alloy) into the master `products` table using intelligent fallback logic.

---

## Usage Examples

### Example 1: Command-Line Import

```bash
# Import Alloy CSV file
php artisan alloy:import storage/imports/alloy_march_2025.csv

# Sync all suppliers to master table
php artisan products:sync
```

### Example 2: Programmatic Import (Controller)

```php
use App\Services\AlloyImportService;

class ImportController extends Controller
{
    public function importAlloy(Request $request)
    {
        $filePath = $request->file('csv')->getRealPath();

        $importService = new AlloyImportService();
        $result = $importService->importCsv($filePath);

        if ($result['success']) {
            return response()->json([
                'message' => 'Import successful',
                'statistics' => $result['statistics']
            ]);
        }

        return response()->json([
            'message' => $result['message']
        ], 500);
    }
}
```

### Example 3: API Data Import

```php
use App\Services\AlloyImportService;

// Import from API response (array of products)
$alloyProducts = [
    [
        'PartNumber' => 'ALLOY-123',
        'Name' => 'Widget Pro',
        'EAN' => '1234567890123',
        'PriceCostEx' => 50.00,
        'PriceRetailEx' => 99.99,
        'Qty_ADL' => 10,
        'Qty_BNE' => 5,
        // ... other fields
    ],
    // ... more products
];

$importService = new AlloyImportService();
$result = $importService->importFromArray($alloyProducts);
```

---

## Technical Details

### Service Architecture

```
┌─────────────────────────────────────┐
│   AlloyImportService                │
│   • Handles CSV/array imports       │
│   • Orchestrates mapping & storage  │
└──────────────┬──────────────────────┘
               │
               │ uses
               ▼
┌─────────────────────────────────────┐
│   FieldMappingService               │
│   • Defines field mappings          │
│   • Validates Alloy data            │
│   • Maps CSV columns to DB fields   │
└──────────────┬──────────────────────┘
               │
               │ stores to
               ▼
┌─────────────────────────────────────┐
│   Supplier2Product Model            │
│   • supplier2_products table        │
│   • Standard + extended fields      │
└─────────────────────────────────────┘
               │
               │ synced by
               ▼
┌─────────────────────────────────────┐
│   ProductSyncService                │
│   • Merges all suppliers            │
│   • Writes to master products table │
└─────────────────────────────────────┘
```

### File Locations

| Component | File Path |
|-----------|-----------|
| **Field Mapping Service** | `app/Services/FieldMappingService.php` |
| **Import Service** | `app/Services/AlloyImportService.php` |
| **Supplier2 Model** | `app/Models/Supplier2Product.php` |
| **Migration (Extended Fields)** | `database/migrations/2025_11_16_000000_add_alloy_specific_fields_to_supplier2_products_table.php` |
| **Artisan Command** | `app/Console/Commands/ImportAlloyProducts.php` |

### Data Validation Rules

1. **Required Fields**: At least one identifier must be present:
   - `PartNumber` (SKU), OR
   - `EAN`

2. **Duplicate Handling**: Products are matched by:
   - Primary: `sku` (PartNumber)
   - Fallback: `ean` (EAN)
   - If match found → Update existing
   - If no match → Create new

3. **Data Type Conversions**:
   - `PDF_Available`: Converted to boolean (accepts: yes/true/1/y)
   - `TaxRate`: Converted to float
   - Warehouse quantities: Converted to integers
   - Dates: Parsed and formatted to MySQL date/timestamp

---

## Field Mapping Reference Card

### Quick Lookup Table

```
Alloy Field              → Database Field        → Data Type
─────────────────────────────────────────────────────────────
PartNumber               → sku                   → String
EAN                      → ean                   → String
Name                     → name                  → String
Description              → shortdescription      → Text
HTMLDescription          → longdescription       → Text
Category                 → category1             → String
CategoryName             → category2             → String
Group                    → category3             → String
PriceCostEx              → costprice             → Decimal
PriceRetailEx            → saleprice             → Decimal
Quantity                 → quantity              → Integer
Width                    → width                 → Decimal
Height                   → height                → Decimal
Depth                    → length                → Decimal
Weight                   → weight                → Decimal
image_thumbnail          → imagesrc              → String
Manufacturer             → manufacturer          → String
Qty_ADL/BNE/MEL/SYD      → qty_adl/bne/mel/syd   → Integer
```

---

## Troubleshooting

### Common Issues

**Issue**: CSV import fails with "Missing required fields"
- **Solution**: Ensure each row has either `PartNumber` or `EAN` populated

**Issue**: Products not appearing in master table after import
- **Solution**: Run `php artisan products:sync` after importing

**Issue**: Warehouse quantities not summing correctly
- **Solution**: Check that `Qty_ADL`, `Qty_BNE`, `Qty_MEL`, `Qty_SYD` contain numeric values

**Issue**: Date fields showing NULL
- **Solution**: Ensure date format is parseable by PHP `strtotime()` (e.g., 'Y-m-d', 'd/m/Y')

---

## Version History

- **v1.0** (2025-11-16): Initial Alloy mapping implementation
  - Complete field mapping for 32 Alloy CSV columns
  - CSV import service with validation
  - Artisan command for imports
  - Multi-warehouse quantity support

---

## Support

For questions or issues with Alloy integration:
1. Check this documentation first
2. Review error logs in storage/logs
3. Verify CSV format matches expected column names
4. Test with a small sample CSV before full import

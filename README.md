# Product Management System

A Laravel 12 application for managing products with automatic data synchronization from multiple supplier tables using flexible field mapping.

## Features

- Master Product table with comprehensive product information
- Multiple supplier tables (Supplier1, Supplier2, Supplier LS)
- **Flexible supplier field mapping system** - easily configure how supplier data maps to your internal schema
- **Priority-based data synchronization** - merge data from multiple suppliers based on configurable priorities
- **Multi-warehouse stock tracking** - track inventory across multiple warehouses with ETA dates
- Full CRUD operations for products
- Web interface for product management
- Artisan commands for syncing and importing supplier data
- Support for CSV and JSON data imports

## Database Schema

### Master Product Table
The master `products` table contains comprehensive product information from all suppliers:

**Identification & Classification:**
- id (primary key)
- sku, asin, ean, isbn, upc (legacy identifiers)
- supplier_code (primary supplier identifier)
- category_code, category1-4, subcategory_name
- brand_name, brand_sku
- barcode

**Product Description:**
- name
- shortdescription, longdescription, description
- imagesrc

**Pricing:**
- costprice, saleprice, retail_price

**Physical Specifications:**
- length, width, height, weight

**Product Details:**
- warranty
- alternative_skus (alternative/replacement SKUs)
- accessory_skus (compatible accessories)

**Stock Levels:**
- quantity (legacy)
- stock_total (total across all warehouses)
- stock_warehouse_a through stock_warehouse_e (individual warehouse stock)

**ETA Dates:**
- eta_warehouse_a through eta_warehouse_e (expected arrival dates per warehouse)

- timestamps (created_at, updated_at)

### Supplier Tables

#### Supplier LS (supplier_ls_products)
Comprehensive supplier table with multi-warehouse support and extended product data.

#### Supplier 1 & 2 (supplier1_products, supplier2_products)
Legacy supplier tables with basic product information.

## Supplier Mapping Configuration

The system uses a flexible configuration-based mapping system defined in `config/suppliers.php`.

### How It Works

1. **Field Mappings**: Each supplier has a mapping that defines how their fields correspond to your internal schema
2. **Priority System**: Suppliers are assigned priorities (lower number = higher priority)
3. **Merge Strategy**: When multiple suppliers provide the same product, data is merged based on priority
4. **Automatic Transformation**: Supplier data is automatically transformed using the mappings

### Example: Supplier LS Configuration

```php
'ls' => [
    'name' => 'Supplier A (LS)',
    'enabled' => true,
    'priority' => 1, // Highest priority
    'model' => \App\Models\SupplierLsProduct::class,
    'table' => 'supplier_ls_products',

    'mappings' => [
        'STOCK CODE' => 'supplier_code',
        'SHORT DESCRIPTION' => 'name',
        'LONG DESCRIPTION' => 'description',
        'DBP' => 'cost_price',
        'RRP' => 'retail_price',
        'AT' => 'stock_total',
        'AA' => 'stock_warehouse_a',
        // ... more mappings
    ],
],
```

### Supplier LS (Supplier A) Field Mappings

**Identification & Classification:**
- `STOCK CODE` → Supplier code
- `CATEGORY CODE`, `CATEGORY NAME` → Category information
- `SUBCATEGORY NAME` → Subcategory
- `MANUFACTURER` → Brand name
- `MANUFACTURER SKU` → Brand SKU

**Product Description:**
- `SHORT DESCRIPTION` → Product name
- `LONG DESCRIPTION` → Product description
- `BAR CODE` → Barcode
- `IMAGE` → Image URL

**Pricing:**
- `DBP` → Supplier LS cost price
- `RRP` → Recommended Retail Price

**Physical Specifications:**
- `WEIGHT`, `LENGTH`, `WIDTH`, `HEIGHT`

**Product Details:**
- `WARRANTY`
- `ALTERNATIVE REPLACEMENTS` → Alternative SKUs
- `OPTIONAL ACCESSORIES` → Accessory SKUs

**Stock Levels:**
- `AT` → Total stock across all warehouses
- `AA` → Stock in Warehouse A
- `AQ` → Stock in Warehouse B
- `AN` → Stock in Warehouse C
- `AV` → Stock in Warehouse D
- `AW` → Stock in Warehouse E

**ETA (Expected Time of Arrival) Dates:**
- `ETAA` → ETA for Warehouse A
- `ETAQ` → ETA for Warehouse B
- `ETAN` → ETA for Warehouse C
- `ETAV` → ETA for Warehouse D
- `ETAW` → ETA for Warehouse E

## Data Synchronization Logic

The system uses a priority-based merge mechanism:

1. **Load Products**: Retrieve all products from all enabled suppliers
2. **Transform Data**: Map each supplier's fields to the internal schema using configured mappings
3. **Group Products**: Group products by unique identifier across suppliers
4. **Merge Data**: For each field, use the value from the highest priority supplier that has a non-empty value
5. **Save**: Update or create products in the master table

### Example:
```
Supplier LS (Priority 1):
- STOCK CODE: "ABC123"
- SHORT DESCRIPTION: "Premium Widget"
- DBP: 50.00
- LONG DESCRIPTION: "" (empty)

Supplier 1 (Priority 2):
- sku: "ABC123"
- name: "" (empty)
- longdescription: "A detailed description"
- costprice: 55.00

Master Product Result:
- supplier_code: "ABC123" (from LS)
- name: "Premium Widget" (from LS - higher priority)
- description: "A detailed description" (from Supplier 1 - LS was empty)
- cost_price: 50.00 (from LS - higher priority)
```

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- PostgreSQL or MySQL database
- Node.js and npm (optional, for asset compilation)

### Setup Steps

1. Clone the repository
```bash
git clone <repository-url>
cd content-python
```

2. Install dependencies
```bash
composer install
```

3. Configure environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure database in `.env`
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=laravel_products
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

5. Run migrations
```bash
php artisan migrate
```

6. Start the development server
```bash
php artisan serve
```

Visit http://localhost:8000 to access the application.

## Usage

### Web Interface

1. **View Products**: Navigate to the home page to see all products
2. **Add Product**: Click "Add Product" button to create a new product
3. **Edit Product**: Click "Edit" next to any product to modify it
4. **Delete Product**: Click "Delete" to remove a product
5. **Sync from Suppliers**: Click "Sync from Suppliers" to update the master table from supplier data

### Artisan Commands

#### Sync All Suppliers

Sync products from all enabled supplier tables:
```bash
php artisan products:sync
```

This command will:
- Read all products from all enabled suppliers (LS, Supplier 1, Supplier 2)
- Transform data using configured field mappings
- Apply priority-based merge logic
- Update or create products in the master products table

#### Import Supplier Data

Import product data from CSV or JSON files:

**Import from CSV:**
```bash
php artisan supplier:import ls /path/to/supplier-data.csv --format=csv
```

**Import from JSON:**
```bash
php artisan supplier:import ls /path/to/supplier-data.json --format=json
```

**Import with table truncation (replace all data):**
```bash
php artisan supplier:import ls /path/to/data.csv --format=csv --truncate
```

Available options:
- `supplier`: The supplier key (ls, supplier1, supplier2)
- `file`: Path to the data file
- `--format`: File format (csv or json), default: csv
- `--truncate`: Clear existing supplier data before import

#### Sync Specific Supplier

Sync products from a single supplier only:
```bash
php artisan products:sync ls
```

### Adding Sample Data

To test the synchronization, you can manually add data to supplier tables:

```sql
-- Add sample data to supplier1_products
INSERT INTO supplier1_products (sku, name, category1, costprice, saleprice, quantity)
VALUES ('SKU001', 'Product A', 'Electronics', 50.00, 75.00, 100);

-- Add sample data to supplier2_products (with different/complementary data)
INSERT INTO supplier2_products (sku, shortdescription, longdescription, imagesrc)
VALUES ('SKU001', 'Great electronic device', 'This is a detailed description...', 'https://example.com/image.jpg');

-- Run sync
php artisan products:sync
```

After syncing, the master products table will have a combined record with data from both suppliers.

## Application Structure

### Models
- `App\Models\Product` - Master product model with all fields
- `App\Models\SupplierLsProduct` - Supplier LS products (multi-warehouse support)
- `App\Models\Supplier1Product` - Supplier 1 products (legacy)
- `App\Models\Supplier2Product` - Supplier 2 products (legacy)

### Controllers
- `App\Http\Controllers\ProductController` - Handles product CRUD operations and sync

### Services
- `App\Services\ProductSyncService` - Orchestrates synchronization from all suppliers
- `App\Services\SupplierMappingService` - Handles field mapping, merging, and validation

### Commands
- `App\Console\Commands\SyncProducts` - Sync products from all or specific suppliers
- `App\Console\Commands\ImportSupplierData` - Import supplier data from CSV/JSON files

### Configuration
- `config/suppliers.php` - Supplier definitions, field mappings, and priorities

### Views
- `resources/views/products/index.blade.php` - Product listing
- `resources/views/products/show.blade.php` - Product detail view
- `resources/views/products/create.blade.php` - Create product form
- `resources/views/products/edit.blade.php` - Edit product form
- `resources/views/products/form.blade.php` - Shared form partial

## Routes

- `GET /` - Redirects to products listing
- `GET /products` - List all products
- `GET /products/create` - Show create form
- `POST /products` - Store new product
- `GET /products/{product}` - Show product details
- `GET /products/{product}/edit` - Show edit form
- `PUT /products/{product}` - Update product
- `DELETE /products/{product}` - Delete product
- `POST /products/sync` - Sync products from suppliers

## Extending the System

### Adding More Suppliers

The new configuration-based system makes adding suppliers simple:

1. **Create a migration** for the supplier table:
```bash
php artisan make:migration create_supplier3_products_table
```

2. **Create a model**:
```bash
php artisan make:model Supplier3Product
```

3. **Add configuration** to `config/suppliers.php`:
```php
'supplier3' => [
    'name' => 'Supplier 3',
    'enabled' => true,
    'priority' => 4,
    'model' => \App\Models\Supplier3Product::class,
    'table' => 'supplier3_products',
    'mappings' => [
        'their_sku_field' => 'supplier_code',
        'their_name_field' => 'name',
        'their_price_field' => 'cost_price',
        // ... map all their fields to your internal schema
    ],
],
```

That's it! The sync system will automatically include the new supplier.

### Customizing Field Mappings

Edit `config/suppliers.php` to adjust how fields are mapped:

```php
'mappings' => [
    'SUPPLIER_FIELD_NAME' => 'your_internal_field_name',
    // Add more mappings as needed
],
```

### Adjusting Supplier Priority

Change the `priority` value in the config (lower number = higher priority):

```php
'ls' => [
    'priority' => 1, // Highest priority - values used first
],
'supplier1' => [
    'priority' => 2, // Second priority - used if LS is empty
],
```

### Customizing Sync Logic

The core sync logic is in:
- `app/Services/ProductSyncService.php` - Orchestration and sync process
- `app/Services/SupplierMappingService.php` - Field mapping and merge logic

You can extend these services to add custom business rules, validation, or transformation logic.

## Testing

To test the application:

1. Add sample data to supplier tables
2. Run the sync command: `php artisan products:sync`
3. Visit the web interface to view the merged results
4. Test CRUD operations through the web interface

## License

This project is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).

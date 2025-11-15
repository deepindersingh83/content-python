# Product Management System

A Laravel 12 application for managing products with automatic data synchronization from multiple supplier tables.

## Features

- Master Product table with comprehensive product information
- Multiple supplier tables (Supplier1, Supplier2)
- Automatic data synchronization with fallback logic
- Full CRUD operations for products
- Web interface for product management
- Artisan command for syncing products from suppliers

## Database Schema

### Master Product Table
The master `products` table contains the following fields:

- id (primary key)
- sku
- asin
- ean
- isbn
- upc
- name
- shortdescription
- longdescription
- category1, category2, category3, category4
- costprice
- saleprice
- quantity
- length, width, height
- weight
- imagesrc
- timestamps (created_at, updated_at)

### Supplier Tables
Two supplier tables (`supplier1_products` and `supplier2_products`) with identical structure to the products table.

## Data Synchronization Logic

The system uses a smart fallback mechanism to populate the master products table:

1. For each field, if Supplier 1 has a non-empty value, it's used
2. If Supplier 1's value is empty/null, the system falls back to Supplier 2's value
3. Products are matched by SKU (with fallback to ASIN, EAN, ISBN, or UPC)
4. The sync process updates existing products or creates new ones

### Example:
```
Product in Supplier 1:
- name: "Product A"
- description: "" (empty)
- price: 100

Product in Supplier 2:
- name: "" (empty)
- description: "Great product"
- price: 95

Master Product Result:
- name: "Product A" (from Supplier 1)
- description: "Great product" (from Supplier 2)
- price: 100 (from Supplier 1)
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

### Artisan Command

Sync products from supplier tables:
```bash
php artisan products:sync
```

This command will:
- Read all products from supplier1_products and supplier2_products
- Apply the fallback logic to merge data
- Update or create products in the master products table

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
- `App\Models\Product` - Master product model
- `App\Models\Supplier1Product` - Supplier 1 products
- `App\Models\Supplier2Product` - Supplier 2 products

### Controllers
- `App\Http\Controllers\ProductController` - Handles product CRUD operations and sync

### Services
- `App\Services\ProductSyncService` - Contains the synchronization logic

### Commands
- `App\Console\Commands\SyncProducts` - Artisan command for syncing products

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

1. Create a new migration for the supplier table:
```bash
php artisan make:migration create_supplier3_products_table
```

2. Create a model:
```bash
php artisan make:model Supplier3Product
```

3. Update `ProductSyncService` to include the new supplier in the sync logic

### Customizing Sync Logic

Edit `app/Services/ProductSyncService.php` to modify the fallback behavior or add custom business rules.

## Testing

To test the application:

1. Add sample data to supplier tables
2. Run the sync command: `php artisan products:sync`
3. Visit the web interface to view the merged results
4. Test CRUD operations through the web interface

## License

This project is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).

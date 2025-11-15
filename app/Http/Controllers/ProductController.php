<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductSyncService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $syncService;

    public function __construct(ProductSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::paginate(20);
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'nullable|string|max:255',
            'asin' => 'nullable|string|max:255',
            'ean' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:255',
            'upc' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'shortdescription' => 'nullable|string',
            'longdescription' => 'nullable|string',
            'category1' => 'nullable|string|max:255',
            'category2' => 'nullable|string|max:255',
            'category3' => 'nullable|string|max:255',
            'category4' => 'nullable|string|max:255',
            'costprice' => 'nullable|numeric',
            'saleprice' => 'nullable|numeric',
            'quantity' => 'nullable|integer',
            'length' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'imagesrc' => 'nullable|string|max:255',
        ]);

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku' => 'nullable|string|max:255',
            'asin' => 'nullable|string|max:255',
            'ean' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:255',
            'upc' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'shortdescription' => 'nullable|string',
            'longdescription' => 'nullable|string',
            'category1' => 'nullable|string|max:255',
            'category2' => 'nullable|string|max:255',
            'category3' => 'nullable|string|max:255',
            'category4' => 'nullable|string|max:255',
            'costprice' => 'nullable|numeric',
            'saleprice' => 'nullable|numeric',
            'quantity' => 'nullable|integer',
            'length' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'imagesrc' => 'nullable|string|max:255',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    /**
     * Sync products from suppliers
     */
    public function sync()
    {
        $result = $this->syncService->syncAllProducts();

        if ($result['success']) {
            return redirect()->route('products.index')->with('success', $result['message']);
        } else {
            return redirect()->route('products.index')->with('error', $result['message']);
        }
    }
}

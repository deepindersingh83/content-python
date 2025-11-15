<div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Product Information</h3>
            <p class="mt-1 text-sm text-gray-500">Basic product details and identifiers.</p>
        </div>
        <div class="mt-5 md:mt-0 md:col-span-2">
            <div class="grid grid-cols-6 gap-6">
                <div class="col-span-6 sm:col-span-3">
                    <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
                    <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku ?? '') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div class="col-span-6 sm:col-span-3">
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $product->name ?? '') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div class="col-span-6 sm:col-span-3">
                    <label for="asin" class="block text-sm font-medium text-gray-700">ASIN</label>
                    <input type="text" name="asin" id="asin" value="{{ old('asin', $product->asin ?? '') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div class="col-span-6 sm:col-span-3">
                    <label for="ean" class="block text-sm font-medium text-gray-700">EAN</label>
                    <input type="text" name="ean" id="ean" value="{{ old('ean', $product->ean ?? '') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div class="col-span-6 sm:col-span-3">
                    <label for="isbn" class="block text-sm font-medium text-gray-700">ISBN</label>
                    <input type="text" name="isbn" id="isbn" value="{{ old('isbn', $product->isbn ?? '') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div class="col-span-6 sm:col-span-3">
                    <label for="upc" class="block text-sm font-medium text-gray-700">UPC</label>
                    <input type="text" name="upc" id="upc" value="{{ old('upc', $product->upc ?? '') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div class="col-span-6">
                    <label for="shortdescription" class="block text-sm font-medium text-gray-700">Short Description</label>
                    <textarea name="shortdescription" id="shortdescription" rows="2" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('shortdescription', $product->shortdescription ?? '') }}</textarea>
                </div>

                <div class="col-span-6">
                    <label for="longdescription" class="block text-sm font-medium text-gray-700">Long Description</label>
                    <textarea name="longdescription" id="longdescription" rows="4" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('longdescription', $product->longdescription ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Categories</h3>
            <p class="mt-1 text-sm text-gray-500">Product category hierarchy.</p>
        </div>
        <div class="mt-5 md:mt-0 md:col-span-2">
            <div class="grid grid-cols-6 gap-6">
                <div class="col-span-6 sm:col-span-3">
                    <label for="category1" class="block text-sm font-medium text-gray-700">Category 1</label>
                    <input type="text" name="category1" id="category1" value="{{ old('category1', $product->category1 ?? '') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div class="col-span-6 sm:col-span-3">
                    <label for="category2" class="block text-sm font-medium text-gray-700">Category 2</label>
                    <input type="text" name="category2" id="category2" value="{{ old('category2', $product->category2 ?? '') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div class="col-span-6 sm:col-span-3">
                    <label for="category3" class="block text-sm font-medium text-gray-700">Category 3</label>
                    <input type="text" name="category3" id="category3" value="{{ old('category3', $product->category3 ?? '') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div class="col-span-6 sm:col-span-3">
                    <label for="category4" class="block text-sm font-medium text-gray-700">Category 4</label>
                    <input type="text" name="category4" id="category4" value="{{ old('category4', $product->category4 ?? '') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Pricing & Inventory</h3>
            <p class="mt-1 text-sm text-gray-500">Pricing and stock information.</p>
        </div>
        <div class="mt-5 md:mt-0 md:col-span-2">
            <div class="grid grid-cols-6 gap-6">
                <div class="col-span-6 sm:col-span-2">
                    <label for="costprice" class="block text-sm font-medium text-gray-700">Cost Price</label>
                    <input type="number" step="0.01" name="costprice" id="costprice" value="{{ old('costprice', $product->costprice ?? '') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <label for="saleprice" class="block text-sm font-medium text-gray-700">Sale Price</label>
                    <input type="number" step="0.01" name="saleprice" id="saleprice" value="{{ old('saleprice', $product->saleprice ?? '') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $product->quantity ?? 0) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Dimensions & Weight</h3>
            <p class="mt-1 text-sm text-gray-500">Physical product specifications.</p>
        </div>
        <div class="mt-5 md:mt-0 md:col-span-2">
            <div class="grid grid-cols-6 gap-6">
                <div class="col-span-6 sm:col-span-2">
                    <label for="length" class="block text-sm font-medium text-gray-700">Length</label>
                    <input type="number" step="0.01" name="length" id="length" value="{{ old('length', $product->length ?? '') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <label for="width" class="block text-sm font-medium text-gray-700">Width</label>
                    <input type="number" step="0.01" name="width" id="width" value="{{ old('width', $product->width ?? '') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <label for="height" class="block text-sm font-medium text-gray-700">Height</label>
                    <input type="number" step="0.01" name="height" id="height" value="{{ old('height', $product->height ?? '') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div class="col-span-6 sm:col-span-3">
                    <label for="weight" class="block text-sm font-medium text-gray-700">Weight</label>
                    <input type="number" step="0.01" name="weight" id="weight" value="{{ old('weight', $product->weight ?? '') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div class="col-span-6 sm:col-span-3">
                    <label for="imagesrc" class="block text-sm font-medium text-gray-700">Image URL</label>
                    <input type="text" name="imagesrc" id="imagesrc" value="{{ old('imagesrc', $product->imagesrc ?? '') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
            </div>
        </div>
    </div>
</div>

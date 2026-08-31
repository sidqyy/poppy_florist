<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = \App\Models\Product::with([
            'categories',
            'sizes.variants'
        ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhereHas('categories', function ($cat) use ($search) {
                            $cat->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('sizes', function ($size) use ($search) {
                            $size->where('size_name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('sizes.variants', function ($variant) use ($search) {
                            $variant->where('variant_name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('marketing.products.index', compact('products'));
    }

    public function create()
    {
        $categories = \App\Models\Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        $materials = \App\Models\Material::where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('marketing.products.create', compact('categories', 'materials'));
    }

    public function store(Request $request)
    {
        if ($request->has('components')) {
            $filtered = array_filter($request->components, function ($item) {
                return !empty($item['material_id']);
            });

            $request->merge([
                'components' => !empty($filtered) ? $filtered : null
            ]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'price_type' => 'required|in:fixed,range',
            'total_price' => 'required|numeric|min:0',
            'max_price' => 'nullable|required_if:price_type,range|numeric|min:0',
            'components' => 'nullable|array',
            'components.*.material_id' => 'required|exists:materials,id',
            'components.*.qty' => 'required|integer|min:1',
            'rental_price_per_day' => 'nullable|numeric|min:0',
            'max_flexible_components' => 'nullable|integer|min:1',
            'sizes' => 'nullable|array',
            'sizes.*.size_name' => 'nullable|string|max:255',
            'sizes.*.image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'sizes.*.old_image' => 'nullable|string',
            'sizes.*.variants' => 'nullable|array',
            'sizes.*.variants.*.variant_name' => 'nullable|string|max:255',
            'sizes.*.variants.*.price' => 'nullable|numeric|min:0',
            'sizes.*.variants.*.image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'sizes.*.variants.*.old_image' => 'nullable|string',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $this->convertToWebpAndStore($request->file('image'));
        }

        $product = \App\Models\Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $imagePath,
            'price_type' => $request->price_type,
            'total_price' => $request->total_price,
            'max_price' => $request->price_type === 'range' ? $request->max_price : null,
            'is_active' => $request->has('is_active'),
            'availability' => $request->availability ?? 'preorder',
            'is_rentable' => $request->has('is_rentable'),
            'rental_price_per_day' => $request->rental_price_per_day,
            'has_flexible_components' => $request->has('has_flexible_components'),
            'max_flexible_components' => $request->max_flexible_components
        ]);

        $product->categories()->sync($request->categories ?? []);

        if (is_array($request->components)) {
            $this->syncComponents($product, $request->components);
        }

        $this->syncSizesAndVariants($product, $request->sizes);

        $url = route('marketing.products.index');

        if ($request->filled('page')) {
            $url .= '?page=' . $request->page;
        }

        return redirect($url)->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $product = \App\Models\Product::with([
            'components.material',
            'categories',
            'sizes.variants'
        ])->findOrFail($id);

        $categories = \App\Models\Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        $materials = \App\Models\Material::where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('marketing.products.edit', compact('product', 'categories', 'materials'));
    }

    public function update(Request $request, string $id)
    {
        $product = \App\Models\Product::with('sizes.variants')->findOrFail($id);

        if ($request->has('components')) {
            $filtered = array_filter($request->components, function ($item) {
                return !empty($item['material_id']);
            });

            $request->merge([
                'components' => !empty($filtered) ? $filtered : null
            ]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'price_type' => 'required|in:fixed,range',
            'total_price' => 'required|numeric|min:0',
            'max_price' => 'nullable|required_if:price_type,range|numeric|min:0',
            'components' => 'nullable|array',
            'components.*.material_id' => 'required|exists:materials,id',
            'components.*.qty' => 'required|integer|min:1',
            'rental_price_per_day' => 'nullable|numeric|min:0',
            'max_flexible_components' => 'nullable|integer|min:1',
            'sizes' => 'nullable|array',
            'sizes.*.size_name' => 'nullable|string|max:255',
            'sizes.*.image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'sizes.*.old_image' => 'nullable|string',
            'sizes.*.variants' => 'nullable|array',
            'sizes.*.variants.*.variant_name' => 'nullable|string|max:255',
            'sizes.*.variants.*.price' => 'nullable|numeric|min:0',
            'sizes.*.variants.*.image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'sizes.*.variants.*.old_image' => 'nullable|string',
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'price_type' => $request->price_type,
            'total_price' => $request->total_price,
            'max_price' => $request->price_type === 'range' ? $request->max_price : null,
            'is_active' => $request->has('is_active'),
            'availability' => $request->availability ?? 'preorder',
            'is_rentable' => $request->has('is_rentable'),
            'rental_price_per_day' => $request->rental_price_per_day,
            'has_flexible_components' => $request->has('has_flexible_components'),
            'max_flexible_components' => $request->max_flexible_components
        ];

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $data['image'] = $this->convertToWebpAndStore($request->file('image'));
        }

        $product->update($data);
        $product->categories()->sync($request->categories ?? []);

        $product->components()->delete();

        if (is_array($request->components)) {
            $this->syncComponents($product, $request->components);
        }

        if (method_exists($product, 'sizes')) {
            $product->sizes()->delete();
        }

        $this->syncSizesAndVariants($product, $request->sizes);

        $url = route('marketing.products.index');

        if ($request->filled('page')) {
            $url .= '?page=' . $request->page;
        }

        return redirect($url)->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Request $request, string $id)
    {
        $product = \App\Models\Product::with('sizes.variants')->findOrFail($id);

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $this->deleteOldSizeAndVariantImages($product);

        $product->delete();

        $url = route('marketing.products.index');

        if ($request->filled('page')) {
            $url .= '?page=' . $request->page;
        }

        return redirect($url)->with('success', 'Produk berhasil dihapus.');
    }

    private function syncComponents(\App\Models\Product $product, array $components)
    {
        foreach ($components as $comp) {
            if (empty($comp['material_id']) || empty($comp['qty'])) {
                continue;
            }

            $material = \App\Models\Material::find($comp['material_id']);

            if (!$material) {
                continue;
            }

            $subtotal = $comp['qty'] * $material->price;

            \App\Models\ProductComponent::create([
                'product_id' => $product->id,
                'material_id' => $material->id,
                'qty' => $comp['qty'],
                'unit_price' => $material->price,
                'subtotal' => $subtotal,
                'notes' => $comp['notes'] ?? null
            ]);
        }
    }

    private function syncSizesAndVariants(\App\Models\Product $product, ?array $sizes)
    {
        if (!$sizes || !is_array($sizes)) {
            return;
        }

        foreach ($sizes as $sizeData) {
            if (empty($sizeData['size_name'])) {
                continue;
            }

            $sizeImagePath = $sizeData['old_image'] ?? null;

            if (
                isset($sizeData['image']) &&
                $sizeData['image'] instanceof UploadedFile
            ) {
                $sizeImagePath = $this->convertToWebpAndStore($sizeData['image']);
            }

            $size = \App\Models\ProductSize::create([
                'product_id' => $product->id,
                'size_name' => $sizeData['size_name'],
                'image' => $sizeImagePath,
                'is_active' => true,
            ]);

            if (!empty($sizeData['variants']) && is_array($sizeData['variants'])) {
                foreach ($sizeData['variants'] as $variantData) {
                    if (empty($variantData['variant_name'])) {
                        continue;
                    }

                    $variantImagePath = $variantData['old_image'] ?? null;

                    if (
                        isset($variantData['image']) &&
                        $variantData['image'] instanceof UploadedFile
                    ) {
                        $variantImagePath = $this->convertToWebpAndStore($variantData['image']);
                    }

                    \App\Models\ProductVariant::create([
                        'product_size_id' => $size->id,
                        'variant_name' => $variantData['variant_name'],
                        'price' => $variantData['price'] ?? 0,
                        'image' => $variantImagePath,
                        'is_active' => true,
                    ]);
                }
            }
        }
    }

    private function deleteOldSizeAndVariantImages(\App\Models\Product $product)
    {
        foreach ($product->sizes as $size) {
            if ($size->image && Storage::disk('public')->exists($size->image)) {
                Storage::disk('public')->delete($size->image);
            }

            foreach ($size->variants as $variant) {
                if (!empty($variant->image) && Storage::disk('public')->exists($variant->image)) {
                    Storage::disk('public')->delete($variant->image);
                }
            }
        }
    }

    private function convertToWebpAndStore(\Illuminate\Http\UploadedFile $file)
    {
        return \App\Services\ImageOptimizerService::uploadAndOptimize($file, 'products');
    }
}
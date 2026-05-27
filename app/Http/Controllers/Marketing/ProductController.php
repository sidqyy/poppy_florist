<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = \App\Models\Product::with('categories')->orderBy('name')->paginate(10)->withQueryString();
        return view('marketing.products.index', compact('products'));
    }

    public function create()
    {
        $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();
        $materials = \App\Models\Material::where('is_active', true)->orderBy('type')->orderBy('name')->get();
        return view('marketing.products.create', compact('categories', 'materials'));
    }

    public function store(Request $request)
    {
        // Bersihkan baris komponen kosong jika tidak diisi oleh user
        if ($request->has('components')) {
            $filtered = array_filter($request->components, function($item) {
                return !empty($item['material_id']);
            });
            $request->merge(['components' => !empty($filtered) ? $filtered : null]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240', // Diperbesar ke 10MB karena akan dikompres otomatis di backend
            'price_type' => 'required|in:fixed,range',
            'total_price' => 'required|numeric|min:0',
            'max_price' => 'nullable|required_if:price_type,range|numeric|min:0',
            'components' => 'nullable|array',
            'components.*.material_id' => 'required|exists:materials,id',
            'components.*.qty' => 'required|integer|min:1',
            'rental_price_per_day' => 'nullable|numeric|min:0',
            'max_flexible_components' => 'nullable|integer|min:1'
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
            'max_price' => $request->price_type == 'range' ? $request->max_price : null,
            'is_active' => $request->has('is_active'),
            'availability' => $request->availability ?? 'preorder',
            'is_rentable' => $request->has('is_rentable'),
            'rental_price_per_day' => $request->rental_price_per_day,
            'has_flexible_components' => $request->has('has_flexible_components'),
            'max_flexible_components' => $request->max_flexible_components
        ]);
        $product->categories()->sync($request->categories ?? []);

        if ($request->has('components')) {
            $this->syncComponents($product, $request->components);
        }

        $url = route('marketing.products.index');
        if ($request->has('page') && $request->page) {
            $url .= '?page=' . $request->page;
        }

        return redirect($url)->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $product = \App\Models\Product::with(['components.material', 'categories'])->findOrFail($id);
        $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();
        $materials = \App\Models\Material::where('is_active', true)->orderBy('type')->orderBy('name')->get();
        return view('marketing.products.edit', compact('product', 'categories', 'materials'));
    }

    public function update(Request $request, string $id)
    {
        $product = \App\Models\Product::findOrFail($id);
        
        // Bersihkan baris komponen kosong jika tidak diisi oleh user
        if ($request->has('components')) {
            $filtered = array_filter($request->components, function($item) {
                return !empty($item['material_id']);
            });
            $request->merge(['components' => !empty($filtered) ? $filtered : null]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240', // Diperbesar ke 10MB karena akan dikompres otomatis di backend
            'price_type' => 'required|in:fixed,range',
            'total_price' => 'required|numeric|min:0',
            'max_price' => 'nullable|required_if:price_type,range|numeric|min:0',
            'components' => 'nullable|array',
            'components.*.material_id' => 'required|exists:materials,id',
            'components.*.qty' => 'required|integer|min:1',
            'rental_price_per_day' => 'nullable|numeric|min:0',
            'max_flexible_components' => 'nullable|integer|min:1'
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'price_type' => $request->price_type,
            'total_price' => $request->total_price,
            'max_price' => $request->price_type == 'range' ? $request->max_price : null,
            'is_active' => $request->has('is_active'),
            'availability' => $request->availability ?? 'preorder',
            'is_rentable' => $request->has('is_rentable'),
            'rental_price_per_day' => $request->rental_price_per_day,
            'has_flexible_components' => $request->has('has_flexible_components'),
            'max_flexible_components' => $request->max_flexible_components
        ];

        if ($request->hasFile('image')) {
            if ($product->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $this->convertToWebpAndStore($request->file('image'));
        }

        $product->update($data);
        $product->categories()->sync($request->categories ?? []);

        $product->components()->delete(); // Remove old components
        if ($request->has('components')) {
            $this->syncComponents($product, $request->components);
        }

        $url = route('marketing.products.index');
        if ($request->has('page') && $request->page) {
            $url .= '?page=' . $request->page;
        }

        return redirect($url)->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Request $request, string $id)
    {
        $product = \App\Models\Product::findOrFail($id);
        $product->delete();

        $url = route('marketing.products.index');
        if ($request->has('page') && $request->page) {
            $url .= '?page=' . $request->page;
        }

        return redirect($url)->with('success', 'Produk berhasil dihapus.');
    }

    private function syncComponents(\App\Models\Product $product, array $components)
    {
        $totalPrice = 0;
        foreach ($components as $comp) {
            $material = \App\Models\Material::find($comp['material_id']);
            $subtotal = $comp['qty'] * $material->price;
            $totalPrice += $subtotal;

            \App\Models\ProductComponent::create([
                'product_id' => $product->id,
                'material_id' => $material->id,
                'qty' => $comp['qty'],
                'unit_price' => $material->price,
                'subtotal' => $subtotal,
                'notes' => $comp['notes'] ?? null
            ]);
        }
        // Harga tidak lagi di-override otomatis dari komponen, tapi diset manual
        // $product->update(['total_price' => $totalPrice]);
    }

    /**
     * Convert the uploaded image to modern WebP format with 80% compression quality.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return string
     */
    private function convertToWebpAndStore($file)
    {
        $image = @imagecreatefromstring(file_get_contents($file->getRealPath()));
        if ($image === false) {
            // Fallback to storing as-is if imagecreatefromstring fails
            return $file->store('products', 'public');
        }

        // Generate clean path
        $filename = 'products/' . uniqid() . '.webp';
        $destinationPath = storage_path('app/public/' . $filename);

        // Ensure directory exists
        if (!file_exists(dirname($destinationPath))) {
            @mkdir(dirname($destinationPath), 0755, true);
        }

        // Save alpha channel for PNG/WebP transparency
        imagealphablending($image, false);
        imagesavealpha($image, true);

        // Compress and save as WebP with 80% quality
        imagewebp($image, $destinationPath, 80);
        imagedestroy($image);

        return $filename;
    }
}

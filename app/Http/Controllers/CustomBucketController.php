<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomBucketController extends Controller
{
    public function index()
    {
        $materials = \App\Models\Material::where('is_active', true)
            ->where('stock', '>', 0)
            ->orderBy('type')
            ->orderBy('name')
            ->get();
            
        return view('custom.builder', compact('materials'));
    }

    public function drafts()
    {
        $drafts = \App\Models\Product::where('availability', 'custom')
            ->where('is_active', false)
            ->with('components.material')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('custom.drafts', compact('drafts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'components' => 'required|array|min:1',
            'components.*.material_id' => 'required|exists:materials,id',
            'components.*.qty' => 'required|integer|min:1'
        ]);

        // Cross-check stok
        $totalPrice = 0;
        $componentData = [];
        
        foreach ($request->components as $comp) {
            $material = \App\Models\Material::findOrFail($comp['material_id']);
            if ($material->stock < $comp['qty']) {
                return response()->json([
                    'success' => false,
                    'message' => "Stok {$material->name} tidak mencukupi. Sisa: {$material->stock}"
                ], 422);
            }
            
            $subtotal = $material->price * $comp['qty'];
            $totalPrice += $subtotal;
            
            $componentData[] = [
                'material_id' => $material->id,
                'qty' => $comp['qty'],
                'unit_price' => $material->price,
                'subtotal' => $subtotal
            ];
        }

        // Cari atau buat kategori "Custom Order"
        $category = \App\Models\Category::firstOrCreate(
            ['slug' => 'custom-order'],
            ['name' => 'Custom Order', 'description' => 'Pesanan khusus hasil rakitan kasir']
        );

        $descriptionJson = json_encode([
            'notes' => 'Draft pesanan custom untuk ' . $request->customer_name,
            'delivery_method' => $request->delivery_method ?? 'pickup',
            'delivery_distance' => floatval($request->delivery_distance ?? 0),
            'delivery_fee' => floatval($request->delivery_fee ?? 0)
        ]);

        // Buat Draft Product
        $product = \App\Models\Product::create([
            'name' => 'Custom Bucket - ' . $request->customer_name,
            'category_id' => $category->id,
            'description' => $descriptionJson,
            'total_price' => $totalPrice + floatval($request->delivery_fee ?? 0),
            'availability' => 'custom',
            'is_active' => false // Sembunyikan dari katalog utama
        ]);

        // Simpan Komponen
        foreach ($componentData as $data) {
            $data['product_id'] = $product->id;
            \App\Models\ProductComponent::create($data);
        }

        return response()->json([
            'success' => true,
            'message' => 'Draft custom bucket berhasil disimpan.',
            'redirect_url' => route('custom.drafts')
        ]);
    }
}
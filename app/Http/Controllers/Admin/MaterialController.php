<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Services\ImageOptimizerService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'flower_fresh');
        $materials = Material::where('type', $type)->orderBy('name')->get();

        return view('admin.materials.index', compact('materials', 'type'));
    }

    public function create()
    {
        return view('admin.materials.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:flower_fresh,flower_artificial,wrapping,ribbon,doll,greeting_card,accessory,packaging,service',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'price_stem' => 'nullable|numeric|min:0',
            'price_arrangement' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $validated['price_stem'] = $request->price_stem ?? 0;
        $validated['price_arrangement'] = $request->price_arrangement ?? 0;

        if ($validated['type'] === 'service') {
            $validated['stock'] = 999999;
        } else {
            $validated['stock'] = $validated['stock'] ?? 0;
        }

        if ($request->hasFile('image')) {
            $validated['image'] = ImageOptimizerService::uploadAndOptimize($request->file('image'), 'materials');
        }

        Material::create($validated);

        return redirect()->route('admin.materials.index', ['type' => $validated['type']])
            ->with('success', 'Bahan baku berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $material = Material::findOrFail($id);

        return view('admin.materials.edit', compact('material'));
    }

    public function update(Request $request, string $id)
    {
        $material = Material::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:flower_fresh,flower_artificial,wrapping,ribbon,doll,greeting_card,accessory,packaging,service',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'price_stem' => 'nullable|numeric|min:0',
            'price_arrangement' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $validated['price_stem'] = $request->price_stem ?? 0;
        $validated['price_arrangement'] = $request->price_arrangement ?? 0;

        if ($validated['type'] === 'service') {
            $validated['stock'] = 999999;
        } else {
            $validated['stock'] = $validated['stock'] ?? 0;
        }

        if ($request->hasFile('image')) {
            if ($material->image && Storage::disk('public')->exists($material->image)) {
                Storage::disk('public')->delete($material->image);
            }

            $validated['image'] = ImageOptimizerService::uploadAndOptimize($request->file('image'), 'materials');
        }

        $material->update($validated);

        return redirect()->route('admin.materials.index', ['type' => $validated['type']])
            ->with('success', 'Bahan baku berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $material = Material::findOrFail($id);
        $type = $material->type;

        try {
            $material->delete();

            return redirect()->route('admin.materials.index', ['type' => $type])
                ->with('success', 'Bahan baku berhasil dihapus.');
        } catch (QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('admin.materials.index', ['type' => $type])
                    ->with('error', 'Gagal menghapus! Bahan baku ini sedang digunakan sebagai komponen pada salah satu produk.');
            }

            return redirect()->route('admin.materials.index', ['type' => $type])
                ->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}

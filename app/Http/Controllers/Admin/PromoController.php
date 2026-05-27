<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function index()
    {
        $promos = \App\Models\Promo::latest()->get();
        return view('admin.promos.index', compact('promos'));
    }

    public function create()
    {
        return view('admin.promos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:promos,code',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'max_uses' => 'nullable|integer|min:1',
            'is_active' => 'boolean'
        ]);
        
        $validated['is_active'] = $request->has('is_active');
        $validated['min_purchase'] = $validated['min_purchase'] ?? 0;

        \App\Models\Promo::create($validated);
        return redirect()->route('admin.promos.index')->with('success', 'Promo berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $promo = \App\Models\Promo::findOrFail($id);
        return view('admin.promos.edit', compact('promo'));
    }

    public function update(Request $request, $id)
    {
        $promo = \App\Models\Promo::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|unique:promos,code,' . $promo->id,
            'name' => 'required|string',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'max_uses' => 'nullable|integer|min:1',
        ]);
        
        $validated['is_active'] = $request->has('is_active');
        $validated['min_purchase'] = $validated['min_purchase'] ?? 0;

        $promo->update($validated);
        return redirect()->route('admin.promos.index')->with('success', 'Promo berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $promo = \App\Models\Promo::findOrFail($id);
        $promo->delete();
        return redirect()->route('admin.promos.index')->with('success', 'Promo berhasil dihapus.');
    }
}

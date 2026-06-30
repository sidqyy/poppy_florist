<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ArrangementService;

class ArrangementServiceController extends Controller
{
    public function index()
    {
        $services = ArrangementService::orderBy('min_item')->get();

        return view('admin.arrangement-services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.arrangement-services.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'min_item' => 'required|integer|min:1',
            'max_item' => 'nullable|integer',
            'price' => 'required|numeric|min:0',
        ]);

        ArrangementService::create([
            'name' => $request->name,
            'min_item' => $request->min_item,
            'max_item' => $request->max_item,
            'price' => $request->price,
            'is_premium' => $request->has('is_premium'),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()
            ->route('admin.arrangement-services.index')
            ->with('success', 'Jasa rangkai berhasil ditambahkan');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $service = ArrangementService::findOrFail($id);

        return view('admin.arrangement-services.edit', compact('service'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'min_item' => 'required|integer|min:1',
            'max_item' => 'nullable|integer',
            'price' => 'required|numeric|min:0',
        ]);

        $service = ArrangementService::findOrFail($id);

        $service->update([
            'name' => $request->name,
            'min_item' => $request->min_item,
            'max_item' => $request->max_item,
            'price' => $request->price,
            'is_premium' => $request->has('is_premium'),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()
            ->route('admin.arrangement-services.index')
            ->with('success', 'Jasa rangkai berhasil diperbarui');
    }

    public function destroy(string $id)
    {
        $service = ArrangementService::findOrFail($id);

        $service->delete();

        return redirect()
            ->route('admin.arrangement-services.index')
            ->with('success', 'Jasa rangkai berhasil dihapus');
    }
}
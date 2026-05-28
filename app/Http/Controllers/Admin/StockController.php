<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $mutations = \App\Models\StockMutation::with(['material', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        $lowStocks = \App\Models\Material::whereRaw('stock < min_stock')->get();
        $expiringFlowers = \App\Models\StockMutation::with('material')
            ->where('type', 'in')
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', '<=', now()->addDays(2))
            ->orderBy('expires_at', 'asc')
            ->get();
            
        return view('admin.stocks.index', compact('mutations', 'lowStocks', 'expiringFlowers'));
    }

    public function create()
    {
        $materials = \App\Models\Material::where('is_active', true)->orderBy('name')->get();
        return view('admin.stocks.create', compact('materials'));
    }

    public function store(Request $request, \App\Services\StockService $stockService)
    {
        $request->validate([
            'material_id' => 'required|exists:materials,id',
            'type' => 'required|in:in,out',
            'qty' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        $material = \App\Models\Material::findOrFail($request->material_id);
        
        if ($request->type === 'in') {
            $stockService->addStock($material, $request->qty, Auth::id(), $request->notes ?? 'Restock manual');
        } else {
            if ($material->stock < $request->qty) {
                return back()->withErrors(['qty' => 'Stok tidak mencukupi untuk dikeluarkan.']);
            }
            
            // Manual stock out
            $stockBefore = $material->stock;
            $material->decrement('stock', $request->qty);
            $stockAfter = $material->fresh()->stock;

            \App\Models\StockMutation::create([
                'material_id' => $material->id,
                'user_id' => Auth::id(),
                'type' => 'out',
                'qty' => $request->qty,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'notes' => $request->notes ?? 'Pengeluaran manual'
            ]);
        }
        
        \App\Services\AuditService::log('Mengubah Stok Manual', ['material' => $material->name, 'stock_before' => $material->stock + ($request->type == 'in' ? -$request->qty : $request->qty)], ['material' => $material->name, 'stock_after' => $material->fresh()->stock, 'type' => $request->type, 'qty' => $request->qty]);

        return redirect()->route('admin.stocks.index')->with('success', 'Mutasi stok berhasil dicatat.');
    }
}

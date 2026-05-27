<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Product::with(['categories', 'components.material'])->where('is_active', true);

        // Filter Category
        if ($request->has('category_id') && $request->category_id != '') {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        // Search Name
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter Availability (Ready/Preorder/Custom)
        if ($request->has('availability') && $request->availability != '') {
            $query->where('availability', $request->availability);
        }

        $products = $query->orderBy('name')->get();

        // Filter Fresh / Artificial
        // We do this in collection level because checking nested relation conditionally is complex in query builder sometimes
        if ($request->has('flower_type') && $request->flower_type != '') {
            $products = $products->filter(function($product) use ($request) {
                $hasFresh = $product->components->contains(function($c) {
                    return $c->material->type === 'flower_fresh';
                });
                $hasArtificial = $product->components->contains(function($c) {
                    return $c->material->type === 'flower_artificial';
                });

                if ($request->flower_type === 'fresh') return $hasFresh;
                if ($request->flower_type === 'artificial') return $hasArtificial && !$hasFresh;
                return true;
            });
        }

        $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();

        return view('catalog.index', compact('products', 'categories'));
    }
}
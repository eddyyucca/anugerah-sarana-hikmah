<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\SparepartCategory;
use Illuminate\Http\Request;

class SparepartController extends Controller
{
    public function index(Request $request)
    {
        $query = Sparepart::with('category:id,name');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('part_number', 'like', "%{$s}%")
                ->orWhere('part_name', 'like', "%{$s}%"));
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->stock_filter === 'low') {
            $query->lowStock();
        }

        $spareparts = $query->latest()->paginate(25)->withQueryString();
        $categories = SparepartCategory::orderBy('name')->get();

        return view('spareparts.index', compact('spareparts', 'categories'));
    }

    public function create()
    {
        $categories = SparepartCategory::orderBy('name')->get();
        return view('spareparts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'part_number' => 'required|string|max:50|unique:spareparts,part_number',
            'part_name' => 'required|string|max:150',
            'category_id' => 'nullable|exists:sparepart_categories,id',
            'unit_price' => 'nullable|numeric|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
            'stock_on_hand' => 'nullable|integer|min:0',
            'uom' => 'nullable|string|max:20',
        ]);

        Sparepart::create($validated);
        return redirect()->route('spareparts.index')->with('success', 'Sparepart created successfully.');
    }

    public function show(Sparepart $sparepart)
    {
        $sparepart->load('category');
        return view('spareparts.show', compact('sparepart'));
    }

    public function edit(Sparepart $sparepart)
    {
        $categories = SparepartCategory::orderBy('name')->get();
        return view('spareparts.edit', compact('sparepart', 'categories'));
    }

    public function update(Request $request, Sparepart $sparepart)
    {
        $validated = $request->validate([
            'part_number' => 'required|string|max:50|unique:spareparts,part_number,' . $sparepart->id,
            'part_name' => 'required|string|max:150',
            'category_id' => 'nullable|exists:sparepart_categories,id',
            'unit_price' => 'nullable|numeric|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
            'uom' => 'nullable|string|max:20',
        ]);

        $sparepart->update($validated);
        return redirect()->route('spareparts.index')->with('success', 'Sparepart updated successfully.');
    }

    public function destroy(Sparepart $sparepart)
    {
        $sparepart->delete();
        return redirect()->route('spareparts.index')->with('success', 'Sparepart deleted successfully.');
    }
}

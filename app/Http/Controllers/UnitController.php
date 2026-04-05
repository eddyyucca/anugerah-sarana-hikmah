<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\UnitCategory;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $query = Unit::with('category:id,name');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('unit_code', 'like', "%{$s}%")
                ->orWhere('unit_model', 'like', "%{$s}%"));
        }
        if ($request->filled('status')) {
            $query->where('current_status', $request->status);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $units = $query->latest()->paginate(25)->withQueryString();
        $categories = UnitCategory::orderBy('name')->get();

        return view('units.index', compact('units', 'categories'));
    }

    public function create()
    {
        $categories = UnitCategory::orderBy('name')->get();
        return view('units.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'unit_code' => 'required|string|max:30|unique:units,unit_code',
            'unit_model' => 'required|string|max:100',
            'unit_type' => 'nullable|string|max:50',
            'category_id' => 'nullable|exists:unit_categories,id',
            'department' => 'nullable|string|max:80',
            'current_status' => 'required|in:available,under_repair,standby',
            'hour_meter' => 'nullable|numeric|min:0',
        ]);

        Unit::create($validated);
        return redirect()->route('units.index')->with('success', 'Unit created successfully.');
    }

    public function show(Unit $unit)
    {
        $unit->load('category', 'workOrders.technician', 'repairCosts');
        return view('units.show', compact('unit'));
    }

    public function edit(Unit $unit)
    {
        $categories = UnitCategory::orderBy('name')->get();
        return view('units.edit', compact('unit', 'categories'));
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'unit_code' => 'required|string|max:30|unique:units,unit_code,' . $unit->id,
            'unit_model' => 'required|string|max:100',
            'unit_type' => 'nullable|string|max:50',
            'category_id' => 'nullable|exists:unit_categories,id',
            'department' => 'nullable|string|max:80',
            'current_status' => 'required|in:available,under_repair,standby',
            'hour_meter' => 'nullable|numeric|min:0',
        ]);

        $unit->update($validated);
        return redirect()->route('units.index')->with('success', 'Unit updated successfully.');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();
        return redirect()->route('units.index')->with('success', 'Unit deleted successfully.');
    }
}

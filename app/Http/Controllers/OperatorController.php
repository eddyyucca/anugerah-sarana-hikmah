<?php

namespace App\Http\Controllers;

use App\Models\Operator;
use Illuminate\Http\Request;

class OperatorController extends Controller
{
    public function index(Request $request)
    {
        $query = Operator::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('operator_code', 'like', "%{$s}%")
                ->orWhere('operator_name', 'like', "%{$s}%")
                ->orWhere('nik', 'like', "%{$s}%"));
        }

        $operators = $query->latest()->paginate(25)->withQueryString();
        return view('operators.index', compact('operators'));
    }

    public function create()
    {
        return view('operators.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'operator_code' => 'required|string|max:30|unique:operators,operator_code',
            'operator_name' => 'required|string|max:100',
            'nik' => 'nullable|string|max:30',
            'phone' => 'nullable|string|max:30',
            'license_type' => 'nullable|string|max:30',
            'license_expiry' => 'nullable|date',
        ]);

        Operator::create($validated);
        return redirect()->route('operators.index')->with('success', 'Operator created successfully.');
    }

    public function show(Operator $operator)
    {
        $operator->load(['p2hChecks' => fn($q) => $q->with('unit:id,unit_code,unit_model')->latest()->limit(20)]);
        return view('operators.show', compact('operator'));
    }

    public function edit(Operator $operator)
    {
        return view('operators.edit', compact('operator'));
    }

    public function update(Request $request, Operator $operator)
    {
        $validated = $request->validate([
            'operator_code' => 'required|string|max:30|unique:operators,operator_code,' . $operator->id,
            'operator_name' => 'required|string|max:100',
            'nik' => 'nullable|string|max:30',
            'phone' => 'nullable|string|max:30',
            'license_type' => 'nullable|string|max:30',
            'license_expiry' => 'nullable|date',
        ]);

        $operator->update($validated);
        return redirect()->route('operators.index')->with('success', 'Operator updated successfully.');
    }

    public function destroy(Operator $operator)
    {
        $operator->delete();
        return redirect()->route('operators.index')->with('success', 'Operator deleted successfully.');
    }
}

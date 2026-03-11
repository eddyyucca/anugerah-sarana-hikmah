<?php

namespace App\Http\Controllers;

use App\Models\Technician;
use Illuminate\Http\Request;

class TechnicianController extends Controller
{
    public function index(Request $request)
    {
        $query = Technician::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('technician_code', 'like', "%{$s}%")
                ->orWhere('technician_name', 'like', "%{$s}%"));
        }

        $technicians = $query->latest()->paginate(25)->withQueryString();
        return view('technicians.index', compact('technicians'));
    }

    public function create()
    {
        return view('technicians.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'technician_code' => 'required|string|max:30|unique:technicians,technician_code',
            'technician_name' => 'required|string|max:100',
            'skill' => 'nullable|string|max:80',
            'phone' => 'nullable|string|max:30',
        ]);

        Technician::create($validated);
        return redirect()->route('technicians.index')->with('success', 'Technician created successfully.');
    }

    public function show(Technician $technician)
    {
        $technician->load('workOrders.unit');
        return view('technicians.show', compact('technician'));
    }

    public function edit(Technician $technician)
    {
        return view('technicians.edit', compact('technician'));
    }

    public function update(Request $request, Technician $technician)
    {
        $validated = $request->validate([
            'technician_code' => 'required|string|max:30|unique:technicians,technician_code,' . $technician->id,
            'technician_name' => 'required|string|max:100',
            'skill' => 'nullable|string|max:80',
            'phone' => 'nullable|string|max:30',
        ]);

        $technician->update($validated);
        return redirect()->route('technicians.index')->with('success', 'Technician updated successfully.');
    }

    public function destroy(Technician $technician)
    {
        $technician->delete();
        return redirect()->route('technicians.index')->with('success', 'Technician deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\FitToWork;
use App\Models\Operator;
use App\Services\DocumentNumberService;
use Illuminate\Http\Request;

class FitToWorkController extends Controller
{
    public function index(Request $request)
    {
        $query = FitToWork::with('operator:id,operator_code,operator_name', 'checker:id,name')
            ->latest('check_date');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('ftw_number', 'like', "%{$s}%")
                ->orWhereHas('operator', fn($o) => $o->where('operator_name', 'like', "%{$s}%")
                    ->orWhere('operator_code', 'like', "%{$s}%")));
        }
        if ($request->filled('status')) {
            $query->where('is_fit', $request->status === 'fit');
        }
        if ($request->filled('date_from')) {
            $query->where('check_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('check_date', '<=', $request->date_to);
        }
        if ($request->filled('shift')) {
            $query->where('shift', $request->shift);
        }

        $checks = $query->paginate(25)->withQueryString();

        return view('fit-to-work.index', compact('checks'));
    }

    public function create()
    {
        $ftwNumber = DocumentNumberService::generateFTW();
        $operators = Operator::active()->orderBy('operator_name')->get(['id', 'operator_code', 'operator_name']);

        return view('fit-to-work.create', compact('ftwNumber', 'operators'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'operator_id'  => 'required|exists:operators,id',
            'check_date'   => 'required|date',
            'shift'        => 'required|in:day,night',
            'siap_bekerja' => 'required|in:1,0',
            'kondisi_sehat'=> 'required|in:1,0',
            'notes'        => 'nullable|string',
        ]);

        $siap   = (bool) $request->siap_bekerja;
        $sehat  = (bool) $request->kondisi_sehat;
        $isFit  = $siap && $sehat;

        FitToWork::create([
            'ftw_number'    => DocumentNumberService::generateFTW(),
            'operator_id'   => $request->operator_id,
            'check_date'    => $request->check_date,
            'shift'         => $request->shift,
            'siap_bekerja'  => $siap,
            'kondisi_sehat' => $sehat,
            'is_fit'        => $isFit,
            'notes'         => $request->notes,
            'checked_by'    => auth()->id(),
        ]);

        return redirect()->route('fit-to-work.index')->with('success', 'Fit to Work berhasil disimpan.');
    }

    public function show(FitToWork $fitToWork)
    {
        $fitToWork->load('operator', 'checker');

        return view('fit-to-work.show', compact('fitToWork'));
    }

    // ── Standalone (operator tanpa login) ────────────────────────────────────

    public function formOperator()
    {
        $operators = Operator::active()->orderBy('operator_name')->get(['id', 'operator_code', 'operator_name']);

        return view('fit-to-work.form-operator', compact('operators'));
    }

    public function storeOperator(Request $request)
    {
        $request->validate([
            'operator_id'  => 'required|exists:operators,id',
            'check_date'   => 'required|date',
            'shift'        => 'required|in:day,night',
            'siap_bekerja' => 'required|in:1,0',
            'kondisi_sehat'=> 'required|in:1,0',
            'notes'        => 'nullable|string',
        ]);

        $siap  = (bool) $request->siap_bekerja;
        $sehat = (bool) $request->kondisi_sehat;

        FitToWork::create([
            'ftw_number'    => DocumentNumberService::generateFTW(),
            'operator_id'   => $request->operator_id,
            'check_date'    => $request->check_date,
            'shift'         => $request->shift,
            'siap_bekerja'  => $siap,
            'kondisi_sehat' => $sehat,
            'is_fit'        => $siap && $sehat,
            'notes'         => $request->notes,
            'checked_by'    => null,
        ]);

        return redirect()->route('operator.ftw-form')->with('success', 'Fit to Work berhasil disubmit!');
    }
}

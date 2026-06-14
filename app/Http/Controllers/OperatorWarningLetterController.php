<?php

namespace App\Http\Controllers;

use App\Models\OperatorWarningLetter;
use App\Models\OperatorPerformanceRecord;
use App\Services\DocumentNumberService;
use Illuminate\Http\Request;

class OperatorWarningLetterController extends Controller
{
    public function index(Request $request)
    {
        $letters = OperatorWarningLetter::with('operator', 'unit')
            ->latest('letter_date')
            ->paginate(20);

        return view('operator-warning-letters.index', compact('letters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'operator_performance_record_id' => 'required|exists:operator_performance_records,id',
            'violation_description'          => 'required|string',
        ]);

        $record = OperatorPerformanceRecord::with('operator', 'unit', 'workOrder')
            ->findOrFail($request->operator_performance_record_id);

        if (OperatorWarningLetter::where('operator_performance_record_id', $record->id)->exists()) {
            return back()->with('error', 'Surat peringatan untuk pelanggaran ini sudah dibuat.');
        }

        $letter = OperatorWarningLetter::create([
            'letter_no'                      => DocumentNumberService::generateSK(),
            'letter_date'                    => today(),
            'operator_performance_record_id' => $record->id,
            'operator_id'                    => $record->operator_id,
            'unit_id'                        => $record->unit_id,
            'work_order_id'                  => $record->work_order_id,
            'year_month'                     => $record->year_month,
            'budget_limit'                   => $record->monthly_budget_limit,
            'total_cost'                     => $record->total_cost_at_exceedance,
            'excess_amount'                  => $record->excess_amount,
            'violation_description'          => $request->violation_description,
            'created_by'                     => auth()->user()->name ?? 'admin',
        ]);

        return redirect()->route('operator-warning-letters.show', $letter)
            ->with('success', 'Surat peringatan berhasil dibuat.');
    }

    public function show(OperatorWarningLetter $operatorWarningLetter)
    {
        $operatorWarningLetter->load('operator', 'unit', 'workOrder', 'performanceRecord');
        return view('operator-warning-letters.show', compact('operatorWarningLetter'));
    }

    public function acknowledge(OperatorWarningLetter $operatorWarningLetter)
    {
        if ($operatorWarningLetter->acknowledged_at) {
            return back()->with('error', 'Surat sudah dikonfirmasi sebelumnya.');
        }

        $operatorWarningLetter->update(['acknowledged_at' => now()]);
        return back()->with('success', 'Surat peringatan telah dikonfirmasi (operator tanda tangan).');
    }
}

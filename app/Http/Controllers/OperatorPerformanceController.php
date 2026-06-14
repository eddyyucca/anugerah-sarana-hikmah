<?php

namespace App\Http\Controllers;

use App\Models\Operator;
use App\Models\OperatorPerformanceRecord;
use App\Models\Unit;
use Illuminate\Http\Request;

class OperatorPerformanceController extends Controller
{
    public function index(Request $request)
    {
        $query = OperatorPerformanceRecord::with(
            'operator:id,operator_code,operator_name',
            'unit:id,unit_code,unit_model',
            'workOrder:id,wo_number',
            'warningLetter:id,operator_performance_record_id,letter_no'
        );

        if ($request->filled('operator_id')) {
            $query->where('operator_id', $request->operator_id);
        }
        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }
        if ($request->filled('year_month')) {
            $query->where('year_month', $request->year_month);
        }

        $records   = $query->orderBy('recorded_at', 'desc')->paginate(25)->withQueryString();
        $operators = Operator::orderBy('operator_name')->get(['id', 'operator_code', 'operator_name']);
        $units     = Unit::orderBy('unit_code')->get(['id', 'unit_code', 'unit_model']);

        // Ringkasan: jumlah pelanggaran per operator bulan ini
        $currentMonth = now()->format('Y-m');
        $summary = OperatorPerformanceRecord::where('year_month', $currentMonth)
            ->with('operator:id,operator_code,operator_name')
            ->selectRaw('operator_id, COUNT(*) as total_violations, SUM(excess_amount) as total_excess')
            ->groupBy('operator_id')
            ->orderByDesc('total_violations')
            ->get();

        return view('operator-performance.index', compact('records', 'operators', 'units', 'summary', 'currentMonth'));
    }
}

<?php

namespace App\Services;

use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use App\Models\GoodsReceipt;
use App\Models\GoodsIssue;
use App\Models\WorkOrder;
use App\Models\P2hCheck;
use App\Models\StockOpname;
use App\Models\WarehouseTransfer;
use App\Models\FitToWork;
use App\Models\Timesheet;
use App\Models\SupplierReturn;
use App\Models\TireDamageReport;
use App\Models\OperatorWarningLetter;
use Illuminate\Support\Facades\DB;

class DocumentNumberService
{
    public static function generatePR(): string
    {
        return self::generate('PR', PurchaseRequest::class, 'pr_number');
    }

    public static function generatePO(): string
    {
        return self::generate('PO', PurchaseOrder::class, 'po_number');
    }

    public static function generateGR(): string
    {
        return self::generate('GR', GoodsReceipt::class, 'gr_number');
    }

    public static function generateGI(): string
    {
        return self::generate('GI', GoodsIssue::class, 'gi_number');
    }

    public static function generateWO(): string
    {
        return self::generate('WO', WorkOrder::class, 'wo_number');
    }

    public static function generateP2H(): string
    {
        return self::generate('P2H', P2hCheck::class, 'p2h_number');
    }

    public static function generateSO(): string
    {
        return self::generate('SO', StockOpname::class, 'opname_number');
    }

    public static function generateWT(): string
    {
        return self::generate('WT', WarehouseTransfer::class, 'transfer_number');
    }

    public static function generateFTW(): string
    {
        return self::generate('FTW', FitToWork::class, 'ftw_number');
    }

    public static function generateTS(): string
    {
        return self::generate('TS', Timesheet::class, 'ts_number');
    }

    public static function generateSR(): string
    {
        return self::generate('SR', SupplierReturn::class, 'return_no');
    }

    public static function generateBA(): string
    {
        return self::generate('BA', TireDamageReport::class, 'report_no');
    }

    public static function generateSK(): string
    {
        return self::generate('SK', OperatorWarningLetter::class, 'letter_no');
    }

    private static function generate(string $prefix, string $model, string $column): string
    {
        $full = $prefix . '-' . date('Ym');

        return DB::transaction(function () use ($full, $model, $column) {
            // Row-level (gap) lock on this key serializes concurrent requests for the
            // same prefix/month, preventing two requests from computing the same number.
            $row = DB::table('document_number_sequences')->where('key', $full)->lockForUpdate()->first();

            if ($row) {
                $seq = $row->seq + 1;
                DB::table('document_number_sequences')->where('key', $full)->update(['seq' => $seq]);
            } else {
                // Take the true numeric max of the trailing 4 digits across ALL matching rows,
                // not the string-max row — mixed-format legacy/seeded numbers make ORDER BY ... DESC
                // on the string unreliable and can yield a seq that's already taken.
                $maxSeq = $model::where($column, 'like', $full . '%')
                    ->selectRaw("MAX(CAST(RIGHT($column, 4) AS UNSIGNED)) as max_seq")
                    ->value('max_seq');
                $seq = $maxSeq ? (int) $maxSeq + 1 : 1;
                DB::table('document_number_sequences')->insert(['key' => $full, 'seq' => $seq, 'created_at' => now(), 'updated_at' => now()]);
            }

            return $full . str_pad($seq, 4, '0', STR_PAD_LEFT);
        });
    }
}

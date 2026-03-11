<?php

namespace App\Services;

use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use App\Models\GoodsReceipt;
use App\Models\GoodsIssue;
use App\Models\WorkOrder;

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

    private static function generate(string $prefix, string $model, string $column): string
    {
        $full = $prefix . '-' . date('Ym');
        $last = $model::where($column, 'like', $full . '%')
            ->orderByDesc($column)->value($column);
        $seq = $last ? (int) substr($last, -4) + 1 : 1;
        return $full . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}

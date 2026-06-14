<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\GoodsReceipt;
use App\Models\GoodsIssue;
use App\Models\WorkOrder;
use App\Models\SupplierReturn;
use App\Models\TireDamageReport;
use App\Models\OperatorWarningLetter;

class PrintController extends Controller
{
    public function po(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('items.sparepart', 'supplier', 'purchaseRequest');
        return view('print.po', compact('purchaseOrder'));
    }

    public function pr(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->load('items.sparepart', 'requester', 'approver');
        return view('print.pr', compact('purchaseRequest'));
    }

    public function gr(GoodsReceipt $goodsReceipt)
    {
        $goodsReceipt->load('items.sparepart', 'items.warehouseLocation', 'purchaseOrder.supplier', 'postedByUser');
        return view('print.gr', compact('goodsReceipt'));
    }

    public function gi(GoodsIssue $goodsIssue)
    {
        $goodsIssue->load('items.sparepart', 'workOrder.unit', 'postedByUser');
        return view('print.gi', compact('goodsIssue'));
    }

    public function wo(WorkOrder $workOrder)
    {
        $workOrder->load('unit', 'technician', 'goodsIssues.items.sparepart', 'costSummary', 'logs.creator');
        return view('print.wo', compact('workOrder'));
    }

    public function supplierReturn(SupplierReturn $supplierReturn)
    {
        $supplierReturn->load('supplier', 'items.sparepart', 'goodsReceipt');
        return view('print.supplier-return', compact('supplierReturn'));
    }

    public function baDamage(TireDamageReport $tireDamageReport)
    {
        $tireDamageReport->load('unitTire.sparepart', 'unit');
        return view('print.ba-kerusakan', compact('tireDamageReport'));
    }

    public function warningLetter(OperatorWarningLetter $operatorWarningLetter)
    {
        $operatorWarningLetter->load('operator', 'unit', 'workOrder');
        return view('print.warning-letter', compact('operatorWarningLetter'));
    }
}

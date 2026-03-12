<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\SparepartController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\GoodsReceiptController;
use App\Http\Controllers\GoodsIssueController;
use App\Http\Controllers\WorkOrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\P2hCheckController;
use App\Http\Controllers\DowntimeAnalysisController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ApprovalSettingController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\WarehouseTransferController;
use App\Http\Controllers\ConsumablePrController;
use App\Http\Controllers\PrintController;

// === AUTH ===
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// === P2H Standalone (no auth required) ===
Route::get('p2h-form', [P2hCheckController::class, 'formOperator'])->name('p2h.form-operator');
Route::post('p2h-form', [P2hCheckController::class, 'storeOperator'])->name('p2h.store-operator');

// === AUTHENTICATED ROUTES ===
Route::middleware('auth')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Master Data
    Route::resource('units', UnitController::class);
    Route::resource('spareparts', SparepartController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('technicians', TechnicianController::class);
    Route::resource('operators', OperatorController::class);

    // Purchase Requests
    Route::resource('purchase-requests', PurchaseRequestController::class)->except(['destroy']);
    Route::post('purchase-requests/{purchase_request}/submit', [PurchaseRequestController::class, 'submit'])->name('purchase-requests.submit');
    Route::post('purchase-requests/{purchase_request}/approve', [PurchaseRequestController::class, 'approve'])->name('purchase-requests.approve');
    Route::post('purchase-requests/{purchase_request}/reject', [PurchaseRequestController::class, 'reject'])->name('purchase-requests.reject');

    // Purchase Orders
    Route::resource('purchase-orders', PurchaseOrderController::class)->except(['destroy']);
    Route::post('purchase-orders/{purchase_order}/issue', [PurchaseOrderController::class, 'issue'])->name('purchase-orders.issue');
    Route::post('purchase-orders/{purchase_order}/close', [PurchaseOrderController::class, 'close'])->name('purchase-orders.close');

    // Goods Receipts
    Route::resource('goods-receipts', GoodsReceiptController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('goods-receipts/{goods_receipt}/post', [GoodsReceiptController::class, 'post'])->name('goods-receipts.post');

    // Goods Issues
    Route::resource('goods-issues', GoodsIssueController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('goods-issues/{goods_issue}/post', [GoodsIssueController::class, 'post'])->name('goods-issues.post');

    // Warehouse Transfer
    Route::resource('warehouse-transfer', WarehouseTransferController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('warehouse-transfer/{warehouse_transfer}/post', [WarehouseTransferController::class, 'post'])->name('warehouse-transfer.post');

    // Stock Opname
    Route::resource('stock-opname', StockOpnameController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('stock-opname/{stock_opname}/approve', [StockOpnameController::class, 'approve'])->name('stock-opname.approve');

    // Work Orders
    Route::resource('work-orders', WorkOrderController::class)->except(['destroy']);
    Route::post('work-orders/{work_order}/progress', [WorkOrderController::class, 'progress'])->name('work-orders.progress');
    Route::post('work-orders/{work_order}/complete', [WorkOrderController::class, 'complete'])->name('work-orders.complete');

    // Downtime Analysis
    Route::get('downtime', [DowntimeAnalysisController::class, 'index'])->name('downtime.index');

    // P2H (dalam sidebar)
    Route::get('p2h', [P2hCheckController::class, 'index'])->name('p2h.index');
    Route::get('p2h/create', [P2hCheckController::class, 'create'])->name('p2h.create');
    Route::post('p2h', [P2hCheckController::class, 'store'])->name('p2h.store');
    Route::get('p2h/summary', [P2hCheckController::class, 'summary'])->name('p2h.summary');
    Route::get('p2h/{p2h}', [P2hCheckController::class, 'show'])->name('p2h.show');
    Route::post('p2h/{p2h}/review', [P2hCheckController::class, 'review'])->name('p2h.review');

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/availability', [ReportController::class, 'availability'])->name('reports.availability');
    Route::get('reports/repair-cost', [ReportController::class, 'repairCost'])->name('reports.repair-cost');
    Route::get('reports/stock-movement', [ReportController::class, 'stockMovement'])->name('reports.stock-movement');

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');

    // Approval Settings
    Route::get('settings/approval', [ApprovalSettingController::class, 'index'])->name('approval-settings.index');
    Route::post('settings/approval', [ApprovalSettingController::class, 'store'])->name('approval-settings.store');
    Route::put('settings/approval/{approval_setting}', [ApprovalSettingController::class, 'update'])->name('approval-settings.update');
    Route::delete('settings/approval/{approval_setting}', [ApprovalSettingController::class, 'destroy'])->name('approval-settings.destroy');

    // Consumable PR
    Route::get('consumable-pr', [ConsumablePrController::class, 'index'])->name('consumable-pr.index');
    Route::get('consumable-pr/create', [ConsumablePrController::class, 'create'])->name('consumable-pr.create');
    Route::post('consumable-pr', [ConsumablePrController::class, 'store'])->name('consumable-pr.store');

    // Print / PDF
    Route::get('print/po/{purchase_order}', [PrintController::class, 'po'])->name('print.po');
    Route::get('print/pr/{purchase_request}', [PrintController::class, 'pr'])->name('print.pr');
    Route::get('print/gr/{goods_receipt}', [PrintController::class, 'gr'])->name('print.gr');
    Route::get('print/gi/{goods_issue}', [PrintController::class, 'gi'])->name('print.gi');
    Route::get('print/wo/{work_order}', [PrintController::class, 'wo'])->name('print.wo');
});

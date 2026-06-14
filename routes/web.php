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
use App\Http\Controllers\MenuPermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FitToWorkController;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\OperasiLogController;
use App\Http\Controllers\OperatorPerformanceController;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\OdometerController;
use App\Http\Controllers\TireController;
use App\Http\Controllers\MaintenanceItemController;
use App\Http\Controllers\SupplierReturnController;
use App\Http\Controllers\TireDamageReportController;
use App\Http\Controllers\OperatorWarningLetterController;

// Company Profile (public)
Route::get('/', [CompanyProfileController::class, 'index'])->name('company.profile');
Route::get('/lang/{locale}', function (string $locale) {
    abort_if(!in_array($locale, ['id', 'en', 'zh']), 404);
    session(['locale' => $locale]);
    return redirect()->route('company.profile');
})->name('lang.switch');

// Auth
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// ── Portal Operator (tanpa login) ────────────────────────────────────────────
Route::get('operator', fn() => view('operator.landing'))->name('operator.landing');

// P2H Standalone
Route::get('operator/p2h', [P2hCheckController::class, 'formOperator'])->name('p2h.form-operator');
Route::post('operator/p2h', [P2hCheckController::class, 'storeOperator'])->name('p2h.store-operator');

// Fit to Work Standalone
Route::get('operator/fit-to-work', [FitToWorkController::class, 'formOperator'])->name('operator.ftw-form');
Route::post('operator/fit-to-work', [FitToWorkController::class, 'storeOperator'])->name('operator.ftw-store');

// Timesheet Standalone
Route::get('operator/timesheet', [TimesheetController::class, 'formOperator'])->name('operator.ts-form');
Route::post('operator/timesheet', [TimesheetController::class, 'storeOperator'])->name('operator.ts-store');

// Authenticated
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master
    Route::resource('units', UnitController::class);
    Route::resource('spareparts', SparepartController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('technicians', TechnicianController::class);
    Route::resource('operators', OperatorController::class);
    Route::resource('users', UserController::class);

    // PR
    Route::resource('purchase-requests', PurchaseRequestController::class)->except(['destroy']);
    Route::post('purchase-requests/{purchase_request}/submit', [PurchaseRequestController::class, 'submit'])->name('purchase-requests.submit');
    Route::post('purchase-requests/{purchase_request}/approve', [PurchaseRequestController::class, 'approve'])->name('purchase-requests.approve');
    Route::post('purchase-requests/{purchase_request}/reject', [PurchaseRequestController::class, 'reject'])->name('purchase-requests.reject');

    // Consumable PR
    Route::get('consumable-pr', [ConsumablePrController::class, 'index'])->name('consumable-pr.index');
    Route::get('consumable-pr/create', [ConsumablePrController::class, 'create'])->name('consumable-pr.create');
    Route::post('consumable-pr', [ConsumablePrController::class, 'store'])->name('consumable-pr.store');

    // PO
    Route::resource('purchase-orders', PurchaseOrderController::class)->except(['destroy']);
    Route::post('purchase-orders/{purchase_order}/issue', [PurchaseOrderController::class, 'issue'])->name('purchase-orders.issue');
    Route::post('purchase-orders/{purchase_order}/close', [PurchaseOrderController::class, 'close'])->name('purchase-orders.close');

    // GR
    Route::resource('goods-receipts', GoodsReceiptController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('goods-receipts/{goods_receipt}/post', [GoodsReceiptController::class, 'post'])->name('goods-receipts.post');

    // GI
    Route::resource('goods-issues', GoodsIssueController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('goods-issues/{goods_issue}/post', [GoodsIssueController::class, 'post'])->name('goods-issues.post');

    // Warehouse Transfer (send/receive)
    Route::resource('warehouse-transfer', WarehouseTransferController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('warehouse-transfer/{warehouse_transfer}/send', [WarehouseTransferController::class, 'send'])->name('warehouse-transfer.send');
    Route::post('warehouse-transfer/{warehouse_transfer}/receive', [WarehouseTransferController::class, 'receive'])->name('warehouse-transfer.receive');

    // Stock Opname
    Route::resource('stock-opname', StockOpnameController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('stock-opname/{stock_opname}/count', [StockOpnameController::class, 'count'])->name('stock-opname.count');
    Route::post('stock-opname/{stock_opname}/submit', [StockOpnameController::class, 'submit'])->name('stock-opname.submit');
    Route::post('stock-opname/{stock_opname}/approve', [StockOpnameController::class, 'approve'])->name('stock-opname.approve');
    Route::post('stock-opname/{stock_opname}/reject', [StockOpnameController::class, 'reject'])->name('stock-opname.reject');

    // WO
    Route::resource('work-orders', WorkOrderController::class)->except(['destroy']);
    Route::post('work-orders/{work_order}/progress', [WorkOrderController::class, 'progress'])->name('work-orders.progress');
    Route::post('work-orders/{work_order}/complete', [WorkOrderController::class, 'complete'])->name('work-orders.complete');
    Route::post('work-orders/{work_order}/approve', [WorkOrderController::class, 'approve'])->name('work-orders.approve');
    Route::post('work-orders/{work_order}/reject', [WorkOrderController::class, 'reject'])->name('work-orders.reject');

    // Operator Performance (Budget Exceedance Records)
    Route::get('operator-performance', [OperatorPerformanceController::class, 'index'])->name('operator-performance.index');

    // Downtime
    Route::get('downtime', [DowntimeAnalysisController::class, 'index'])->name('downtime.index');

    // P2H
    Route::get('p2h', [P2hCheckController::class, 'index'])->name('p2h.index');
    Route::get('p2h/create', [P2hCheckController::class, 'create'])->name('p2h.create');
    Route::post('p2h', [P2hCheckController::class, 'store'])->name('p2h.store');
    Route::get('p2h/summary', [P2hCheckController::class, 'summary'])->name('p2h.summary');
    Route::get('p2h/{p2h}', [P2hCheckController::class, 'show'])->name('p2h.show');
    Route::post('p2h/{p2h}/review', [P2hCheckController::class, 'review'])->name('p2h.review');

    // Fit to Work
    Route::get('fit-to-work', [FitToWorkController::class, 'index'])->name('fit-to-work.index');
    Route::get('fit-to-work/create', [FitToWorkController::class, 'create'])->name('fit-to-work.create');
    Route::post('fit-to-work', [FitToWorkController::class, 'store'])->name('fit-to-work.store');
    Route::get('fit-to-work/{fitToWork}', [FitToWorkController::class, 'show'])->name('fit-to-work.show');

    // Timesheet
    Route::get('timesheets', [TimesheetController::class, 'index'])->name('timesheets.index');
    Route::get('timesheets/create', [TimesheetController::class, 'create'])->name('timesheets.create');
    Route::post('timesheets', [TimesheetController::class, 'store'])->name('timesheets.store');
    Route::get('timesheets/{timesheet}', [TimesheetController::class, 'show'])->name('timesheets.show');

    // Operasi Log & Laporan
    Route::get('operasi/log', [OperasiLogController::class, 'log'])->name('operasi.log');
    Route::get('operasi/laporan', [OperasiLogController::class, 'laporan'])->name('operasi.laporan');

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/availability', [ReportController::class, 'availability'])->name('reports.availability');
    Route::get('reports/repair-cost', [ReportController::class, 'repairCost'])->name('reports.repair-cost');
    Route::get('reports/stock-movement', [ReportController::class, 'stockMovement'])->name('reports.stock-movement');
    Route::get('reports/complaint-analysis', [ReportController::class, 'complaintAnalysis'])->name('reports.complaint-analysis');

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');

    // Print
    Route::get('print/po/{purchase_order}', [PrintController::class, 'po'])->name('print.po');
    Route::get('print/pr/{purchase_request}', [PrintController::class, 'pr'])->name('print.pr');
    Route::get('print/gr/{goods_receipt}', [PrintController::class, 'gr'])->name('print.gr');
    Route::get('print/gi/{goods_issue}', [PrintController::class, 'gi'])->name('print.gi');
    Route::get('print/wo/{work_order}', [PrintController::class, 'wo'])->name('print.wo');
    Route::get('print/sr/{supplier_return}', [PrintController::class, 'supplierReturn'])->name('print.supplier-return');
    Route::get('print/ba/{tire_damage_report}', [PrintController::class, 'baDamage'])->name('print.ba-damage');
    Route::get('print/sk/{operator_warning_letter}', [PrintController::class, 'warningLetter'])->name('print.warning-letter');

    // Settings
    Route::get('settings/approval', [ApprovalSettingController::class, 'index'])->name('approval-settings.index');
    Route::post('settings/approval', [ApprovalSettingController::class, 'store'])->name('approval-settings.store');
    Route::put('settings/approval/{approval_setting}', [ApprovalSettingController::class, 'update'])->name('approval-settings.update');
    Route::delete('settings/approval/{approval_setting}', [ApprovalSettingController::class, 'destroy'])->name('approval-settings.destroy');

    // Menu Permissions
    Route::get('settings/menu', [MenuPermissionController::class, 'index'])->name('menu-settings.index');
    Route::post('settings/menu', [MenuPermissionController::class, 'store'])->name('menu-settings.store');
    Route::post('settings/menu/add-role', [MenuPermissionController::class, 'addRole'])->name('menu-settings.add-role');

    // ── Odometer ─────────────────────────────────────────────────────────────
    Route::get('odometer', [OdometerController::class, 'index'])->name('odometer.index');
    Route::post('odometer', [OdometerController::class, 'store'])->name('odometer.store');
    Route::get('odometer/{unit}/history', [OdometerController::class, 'history'])->name('odometer.history');

    // ── Surat Peringatan Operator ─────────────────────────────────────────────
    Route::get('operator-warning-letters', [OperatorWarningLetterController::class, 'index'])->name('operator-warning-letters.index');
    Route::post('operator-warning-letters', [OperatorWarningLetterController::class, 'store'])->name('operator-warning-letters.store');
    Route::get('operator-warning-letters/{operatorWarningLetter}', [OperatorWarningLetterController::class, 'show'])->name('operator-warning-letters.show');
    Route::post('operator-warning-letters/{operatorWarningLetter}/acknowledge', [OperatorWarningLetterController::class, 'acknowledge'])->name('operator-warning-letters.acknowledge');

    // ── BA Kerusakan Ban ─────────────────────────────────────────────────────
    Route::resource('tire-damage-reports', TireDamageReportController::class)->only(['index','create','store','show']);
    Route::post('tire-damage-reports/{tireDamageReport}/approve', [TireDamageReportController::class, 'approve'])->name('tire-damage-reports.approve');

    // ── Return ke Supplier ────────────────────────────────────────────────────
    Route::resource('supplier-returns', SupplierReturnController::class)->only(['index','create','store','show']);
    Route::post('supplier-returns/{supplierReturn}/confirm', [SupplierReturnController::class, 'confirm'])->name('supplier-returns.confirm');
    Route::post('supplier-returns/{supplierReturn}/send', [SupplierReturnController::class, 'send'])->name('supplier-returns.send');

    // ── Ban (dari unit) ───────────────────────────────────────────────────────
    Route::get('tires', [TireController::class, 'index'])->name('tires.index');
    Route::get('tires-analytics', [TireController::class, 'analytics'])->name('tires.analytics');
    Route::post('tires-analytics/set-limit', [TireController::class, 'setKmLimitFromAnalytics'])->name('tires.set-km-limit');
    Route::get('tires/{tire}', [TireController::class, 'show'])->name('tires.show');
    Route::get('units/{unit}/tires/install', [TireController::class, 'installForm'])->name('tires.install-form');
    Route::post('units/{unit}/tires/install', [TireController::class, 'install'])->name('tires.install');
    Route::get('tires/{tire}/move', [TireController::class, 'moveForm'])->name('tires.move-form');
    Route::post('tires/{tire}/move', [TireController::class, 'move'])->name('tires.move');
    Route::post('tires/{tire}/remove', [TireController::class, 'remove'])->name('tires.remove');

    // ── Maintenance KM ────────────────────────────────────────────────────────
    Route::get('maintenance', [MaintenanceItemController::class, 'index'])->name('maintenance.index');
    Route::post('maintenance/items', [MaintenanceItemController::class, 'store'])->name('maintenance.items.store');
    Route::put('maintenance/items/{maintenanceItem}', [MaintenanceItemController::class, 'update'])->name('maintenance.items.update');
    Route::delete('maintenance/items/{maintenanceItem}', [MaintenanceItemController::class, 'destroy'])->name('maintenance.items.destroy');
    Route::post('maintenance/log', [MaintenanceItemController::class, 'logStore'])->name('maintenance.log');
});

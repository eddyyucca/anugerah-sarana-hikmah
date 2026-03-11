<?php

use Illuminate\Support\Facades\Route;
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

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('units', UnitController::class);
Route::resource('spareparts', SparepartController::class);
Route::resource('suppliers', SupplierController::class);
Route::resource('technicians', TechnicianController::class);

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

// Work Orders
Route::resource('work-orders', WorkOrderController::class)->except(['destroy']);
Route::post('work-orders/{work_order}/progress', [WorkOrderController::class, 'progress'])->name('work-orders.progress');
Route::post('work-orders/{work_order}/complete', [WorkOrderController::class, 'complete'])->name('work-orders.complete');

// Reports
Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('reports/availability', [ReportController::class, 'availability'])->name('reports.availability');
Route::get('reports/repair-cost', [ReportController::class, 'repairCost'])->name('reports.repair-cost');
Route::get('reports/stock-movement', [ReportController::class, 'stockMovement'])->name('reports.stock-movement');

// Operators
Route::resource('operators', OperatorController::class);

// P2H Checks
Route::get('p2h', [P2hCheckController::class, 'index'])->name('p2h.index');
Route::get('p2h/create', [P2hCheckController::class, 'create'])->name('p2h.create');
Route::post('p2h', [P2hCheckController::class, 'store'])->name('p2h.store');
Route::get('p2h/summary', [P2hCheckController::class, 'summary'])->name('p2h.summary');
Route::get('p2h/{p2h}', [P2hCheckController::class, 'show'])->name('p2h.show');
Route::post('p2h/{p2h}/review', [P2hCheckController::class, 'review'])->name('p2h.review');

// P2H Standalone Form (untuk operator, tanpa login/sidebar)
Route::get('p2h-form', [P2hCheckController::class, 'formOperator'])->name('p2h.form-operator');
Route::post('p2h-form', [P2hCheckController::class, 'storeOperator'])->name('p2h.store-operator');

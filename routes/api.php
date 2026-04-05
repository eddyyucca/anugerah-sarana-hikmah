<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\SparepartController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\TechnicianController;
use App\Http\Controllers\Api\PurchaseRequestController;
use App\Http\Controllers\Api\PurchaseOrderController;
use App\Http\Controllers\Api\GoodsReceiptController;
use App\Http\Controllers\Api\GoodsIssueController;
use App\Http\Controllers\Api\WorkOrderController;
use App\Http\Controllers\Api\StockOpnameController;
use App\Http\Controllers\Api\WarehouseTransferController;
use App\Http\Controllers\Api\P2hCheckController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ReportController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // User management
    Route::apiResource('users', UserController::class);

    // Master data
    Route::apiResource('units', UnitController::class);
    Route::apiResource('spareparts', SparepartController::class);
    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('technicians', TechnicianController::class);

    // Procurement
    Route::apiResource('purchase-requests', PurchaseRequestController::class)->except(['destroy']);
    Route::post('purchase-requests/{purchase_request}/submit', [PurchaseRequestController::class, 'submit']);
    Route::post('purchase-requests/{purchase_request}/approve', [PurchaseRequestController::class, 'approve']);
    Route::post('purchase-requests/{purchase_request}/reject', [PurchaseRequestController::class, 'reject']);

    Route::apiResource('purchase-orders', PurchaseOrderController::class);

    // Warehouse operations
    Route::apiResource('goods-receipts', GoodsReceiptController::class);
    Route::apiResource('goods-issues', GoodsIssueController::class);
    Route::apiResource('warehouse-transfers', WarehouseTransferController::class);
    Route::apiResource('stock-opnames', StockOpnameController::class)->except(['destroy']);
    Route::post('stock-opnames/{stock_opname}/submit', [StockOpnameController::class, 'submit']);
    Route::post('stock-opnames/{stock_opname}/approve', [StockOpnameController::class, 'approve']);
    Route::post('stock-opnames/{stock_opname}/reject', [StockOpnameController::class, 'reject']);

    // Work orders
    Route::apiResource('work-orders', WorkOrderController::class);
    Route::post('work-orders/{work_order}/complete', [WorkOrderController::class, 'complete']);
    Route::post('work-orders/{work_order}/start', [WorkOrderController::class, 'start']);

    // P2H Checks
    Route::apiResource('p2h-checks', P2hCheckController::class);
    Route::post('p2h-checks/{p2h_check}/review', [P2hCheckController::class, 'review']);

    // Notifications
    Route::apiResource('notifications', NotificationController::class)->only(['index', 'show']);
    Route::post('notifications/{notification}/read', [NotificationController::class, 'markRead']);
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllRead']);

    // Reports
    Route::get('reports/availability', [ReportController::class, 'availability']);
    Route::get('reports/repair-cost', [ReportController::class, 'repairCost']);
    Route::get('reports/stock-movement', [ReportController::class, 'stockMovement']);
    Route::get('reports/complaint-analysis', [ReportController::class, 'complaintAnalysis']);
});
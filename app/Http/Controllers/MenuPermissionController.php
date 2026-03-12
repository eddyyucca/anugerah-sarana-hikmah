<?php

namespace App\Http\Controllers;

use App\Models\MenuPermission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuPermissionController extends Controller
{
    public function index(Request $request)
    {
        $roles = User::select('role')->distinct()->pluck('role')->toArray();
        $currentRole = $request->get('role', $roles[0] ?? 'user');

        $menuKeys = MenuPermission::getAllMenuKeys();
        $permissions = MenuPermission::where('role', $currentRole)->get()->keyBy('menu_key');

        $menuLabels = [
            'dashboard' => 'Dashboard',
            'units' => 'Master Units',
            'spareparts' => 'Master Spareparts',
            'suppliers' => 'Master Suppliers',
            'technicians' => 'Master Technicians',
            'purchase-requests' => 'Purchase Requests',
            'consumable-pr' => 'Consumable PR',
            'purchase-orders' => 'Purchase Orders',
            'goods-receipts' => 'Goods Receipt',
            'goods-issues' => 'Goods Issue',
            'warehouse-transfer' => 'Warehouse Transfer',
            'stock-opname' => 'Stock Opname',
            'work-orders' => 'Work Orders',
            'downtime' => 'Downtime Analysis',
            'operators' => 'Operators',
            'p2h' => 'P2H Check',
            'p2h-summary' => 'P2H Summary',
            'reports' => 'Reports',
            'approval-settings' => 'Approval Settings',
            'menu-settings' => 'Menu Settings',
        ];

        return view('settings.menu-permissions', compact('roles', 'currentRole', 'menuKeys', 'permissions', 'menuLabels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'role' => 'required|string|max:30',
            'permissions' => 'required|array',
        ]);

        DB::transaction(function () use ($request) {
            MenuPermission::where('role', $request->role)->delete();

            foreach ($request->permissions as $menuKey => $perms) {
                MenuPermission::create([
                    'role' => $request->role,
                    'menu_key' => $menuKey,
                    'can_view' => !empty($perms['can_view']),
                    'can_create' => !empty($perms['can_create']),
                    'can_edit' => !empty($perms['can_edit']),
                    'can_delete' => !empty($perms['can_delete']),
                    'can_approve' => !empty($perms['can_approve']),
                ]);
            }
        });

        return back()->with('success', "Permissions for role '{$request->role}' saved.");
    }

    public function addRole(Request $request)
    {
        $request->validate(['new_role' => 'required|string|max:30']);
        // Just redirect with new role param
        return redirect()->route('menu-settings.index', ['role' => strtolower($request->new_role)]);
    }
}

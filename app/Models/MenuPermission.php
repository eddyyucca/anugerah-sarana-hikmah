<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MenuPermission extends Model
{
    protected $fillable = [
        'role', 'menu_key', 'can_view', 'can_create', 'can_edit', 'can_delete', 'can_approve',
    ];

    protected function casts(): array
    {
        return [
            'can_view' => 'boolean', 'can_create' => 'boolean',
            'can_edit' => 'boolean', 'can_delete' => 'boolean', 'can_approve' => 'boolean',
        ];
    }

    public static function hasAccess(string $role, string $menuKey, string $action = 'can_view'): bool
    {
        $perm = self::where('role', $role)->where('menu_key', $menuKey)->first();
        if (!$perm) return $role === 'admin'; // admin can access everything by default
        return (bool) $perm->$action;
    }

    public static function getMenusForRole(string $role): array
    {
        if ($role === 'admin') {
            return self::getAllMenuKeys();
        }
        return self::where('role', $role)->where('can_view', true)->pluck('menu_key')->toArray();
    }

    public static function getAllMenuKeys(): array
    {
        return [
            'dashboard', 'units', 'spareparts', 'suppliers', 'technicians',
            'purchase-requests', 'consumable-pr', 'purchase-orders',
            'goods-receipts', 'goods-issues', 'warehouse-transfer', 'stock-opname',
            'work-orders', 'downtime',
            'operators', 'p2h', 'p2h-summary',
            'reports', 'approval-settings', 'menu-settings',
        ];
    }
}

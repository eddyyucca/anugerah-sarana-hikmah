# Anugerah Sarana Hikmah - Workshop ERP System

Sistem ERP terintegrasi untuk pengelolaan workshop yang mencakup user management, inventory control, purchase requests, maintenance workflows, dan approval system.

## 🚀 Fitur Utama

### User & Access Control
- **User Management**: CRUD user dengan role-based access control
- **Role & Permission System**: Manajemen role dan menu permission yang flexible
- **Approval Workflow**: Multi-level approval untuk dokumen (PR, Stock Opname, dll)

### Modul Utama

| Modul | Route | Deskripsi |
|-------|-------|-----------|
| **Dashboard** | `/` | KPI, chart, dan ringkasan |
| **User Management** | `/users` | Kelola user, role, dan permission |
| **Menu Permissions** | `/settings/menu-permissions` | Konfigurasi role dan akses menu |
| **Units** | `/units` | Master data unit/alat berat |
| **Spareparts** | `/spareparts` | Master data sparepart |
| **Suppliers** | `/suppliers` | Master data supplier |
| **Technicians** | `/technicians` | Master data teknisi |
| **Purchase Request** | `/purchase-requests` | Pengajuan pembelian (dengan approval) |
| **Purchase Order** | `/purchase-orders` | Order pembelian ke supplier |
| **Goods Receipt** | `/goods-receipts` | Penerimaan barang |
| **Goods Issue** | `/goods-issues` | Pengeluaran barang |
| **Stock Opname** | `/stock-opnames` | Verifikasi stok (dengan approval) |
| **Work Orders** | `/work-orders` | Order perbaikan unit |
| **Reports** | `/reports` | Laporan dan analisis |

## 📋 Alur Bisnis

### Procurement Workflow
```
PR (Approval) → PO → GR → Stock Update
```

### Maintenance Workflow
```
WO → GI (Parts) → Complete → Cost Summary
```

### Warehouse Management
```
GR (Masuk) / GI (Keluar) → Stock Movement → Opname (Approval)
```

## 🛠️ Instalasi & Setup

### Prasyarat
- PHP 8.2+
- Composer
- MySQL/MariaDB
- Laravel 11+

### Langkah Instalasi

```bash
# 1. Clone repository
git clone [repository-url]
cd anugerah-sarana-hikmah-main

# 2. Install dependencies
composer install

# 3. Setup environment
cp .env.example .env

# 4. Generate app key
php artisan key:generate

# 5. Konfigurasi database di .env
DB_DATABASE=anugerah_erp
DB_USERNAME=root
DB_PASSWORD=

# 6. Jalankan migration dan seeder
php artisan migrate
php artisan db:seed

# 7. Jalankan server
php artisan serve
```

Akses aplikasi: **http://localhost:8000**

## 🔐 Authorization & Approval System

### Permission Hierarchy
1. **User Role**: Pengguna memiliki role yang dikonfigurasi di menu_permissions
2. **Menu Access**: Setiap menu memiliki permission per role (can_create, can_view, can_edit, can_delete, can_approve)
3. **Document Approval**:
   - Konfigurasi di ApprovalSetting (min/max budget, approver role/user)
   - Tracking di ApprovalLog (pending→approved/rejected)

### Contoh Usage
```php
// Check if user can access menu
$canAccess = auth()->user()->canAccess('purchase-request', 'create');

// Check if user can approve document
$canApprove = auth()->user()->canApproveDocument('pr', $id, $amount);

// Get list of menus user can access
$menus = auth()->user()->allowedMenus();
```

## 📦 Database Structure

- **27 Tabel** dengan foreign keys dan indexes
- **Soft delete** untuk master data (units, suppliers, spareparts, technicians)
- **Approval logs** untuk tracking perubahan status document
- **Menu permissions** untuk granular access control

### Tabel Utama
- `users` - User dan role assignment
- `menu_permissions` - Permission per role
- `purchase_requests` - Pengajuan pembelian
- `purchase_orders` - Order pembelian
- `goods_receipts` - Penerimaan barang
- `goods_issues` - Pengeluaran barang
- `stock_opnames` - Verifikasi stok
- `approval_settings` - Konfigurasi approval level
- `approval_logs` - History approval dokumen

## 🎨 Tech Stack

- **Backend**: Laravel 11+
- **Database**: MySQL/MariaDB
- **Frontend**: Blade Templating + Bootstrap 5
- **UI Components**: Bootstrap 5 CDN, Bootstrap Icons
- **Charts**: Chart.js CDN
- **Authentication**: Laravel Auth built-in

## 📝 Konvensi & Best Practices

### Authorization
- Selalu validasi permission di controller sebelum approval
- Gunakan ApprovalService untuk multi-level approval
- Check user permission di blade template sebelum show button

### Database
- Gunakan soft delete untuk data master yang sering di-update
- Composite indexes untuk query yang sering diakses
- Foreign keys dengan cascading rules yang tepat

### Code Structure
- Controllers handle: validation, authorization, business logic
- Models handle: relationships, custom queries, helpers
- Views handle: display logic hanya, gunakan @php sparingly

## 📚 Development Notes

- Semua migrasi sudah ada di `database/migrations/`
- Seeders tersedia di `database/seeders/`
- Custom helpers di `app/Services/`
- Role checkers di `User.php` model

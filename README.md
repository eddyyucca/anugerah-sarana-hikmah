# APEX — Mining ERP System

> **Asset & Plant ERP for Extraction**
> Sistem ERP terintegrasi untuk industri pertambangan — mengelola work order, inventaris sparepart, pengadaan, dan operasional workshop alat berat secara menyeluruh.

---

## Tentang Aplikasi

**APEX** adalah sistem Enterprise Resource Planning (ERP) yang dirancang khusus untuk perusahaan pertambangan. Aplikasi ini mencakup seluruh siklus operasional workshop, mulai dari pemeliharaan alat berat, manajemen suku cadang, proses pengadaan, hingga pelaporan dan analisis.

Dikembangkan oleh **PT Anugerah Sarana Hikmah**.

---

## Fitur Utama

### Manajemen Operasional
- **Work Order** — Pencatatan dan pelacakan perbaikan/perawatan unit alat berat
- **P2H Check** — Pemeriksaan harian pra-penggunaan unit oleh operator
- **Downtime Analysis** — Analisis downtime unit untuk efisiensi operasional

### Inventaris & Gudang
- **Sparepart Management** — Master data suku cadang dengan stok real-time
- **Goods Receipt** — Penerimaan barang dari supplier
- **Goods Issue** — Pengeluaran barang untuk kebutuhan perbaikan
- **Stock Opname** — Verifikasi stok fisik dengan approval multi-level
- **Warehouse Transfer** — Transfer barang antar lokasi gudang

### Pengadaan (Procurement)
- **Purchase Request** — Pengajuan pembelian dengan workflow approval
- **Purchase Order** — Order pembelian ke supplier
- **Consumable PR** — Pengajuan khusus untuk barang konsumabel

### Akses & Keamanan
- **User Management** — CRUD pengguna dengan role-based access control
- **Menu Permissions** — Konfigurasi hak akses per role secara granular
- **Approval Workflow** — Multi-level approval untuk dokumen kritis

### Laporan
- **Dashboard** — KPI, chart, dan ringkasan operasional
- **Reports** — Laporan dan analisis data

---

## Alur Bisnis

```
Procurement:   PR (Approval) → PO → GR → Stock Update
Maintenance:   WO → GI (Parts) → Complete → Cost Summary
Warehouse:     GR (Masuk) / GI (Keluar) → Stock Movement → Opname (Approval)
Unit:          Registration → Availability Log → P2H Check → Work Order
```

---

## Modul & Route

| Modul | Route | Deskripsi |
|---|---|---|
| Dashboard | `/` | KPI, chart, ringkasan |
| Units | `/units` | Master data unit alat berat |
| Spareparts | `/spareparts` | Master data suku cadang |
| Suppliers | `/suppliers` | Master data supplier |
| Technicians | `/technicians` | Master data teknisi |
| Operators | `/operators` | Master data operator |
| Purchase Request | `/purchase-requests` | Pengajuan pembelian |
| Consumable PR | `/consumable-pr` | Pengajuan barang konsumabel |
| Purchase Order | `/purchase-orders` | Order pembelian ke supplier |
| Goods Receipt | `/goods-receipts` | Penerimaan barang |
| Goods Issue | `/goods-issues` | Pengeluaran barang |
| Stock Opname | `/stock-opname` | Verifikasi stok fisik |
| Work Orders | `/work-orders` | Order perbaikan unit |
| Downtime Analysis | `/downtime` | Analisis downtime |
| P2H Check | `/p2h` | Pemeriksaan harian unit |
| Reports | `/reports` | Laporan & analisis |
| Users | `/users` | Manajemen pengguna |
| Menu Permissions | `/settings/menu-permissions` | Konfigurasi akses role |
| Approval Settings | `/approval-settings` | Konfigurasi level approval |

---

## Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | Laravel 11 (PHP 8.2+) |
| Database | MySQL / MariaDB |
| Frontend | Blade Templating + Bootstrap 5 |
| UI Icons | Bootstrap Icons |
| Charts | Chart.js |
| Select Input | Tom Select |
| Authentication | Laravel Auth built-in |

---

## Instalasi

### Prasyarat
- PHP 8.2+
- Composer
- MySQL / MariaDB
- XAMPP / Laragon (opsional)

### Langkah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/eddyyucca/anugerah-sarana-hikmah-main.git
cd anugerah-sarana-hikmah-main

# 2. Install dependencies
composer install

# 3. Setup environment
cp .env.example .env

# 4. Generate app key
php artisan key:generate

# 5. Konfigurasi database di .env
DB_DATABASE=apex_erp
DB_USERNAME=root
DB_PASSWORD=

# 6. Jalankan migration dan seeder
php artisan migrate
php artisan db:seed

# 7. Jalankan server
php artisan serve
```

Akses aplikasi: **http://localhost:8000**

---

## Struktur Database

- **27+ Tabel** dengan foreign keys dan indexes
- Soft delete untuk master data (units, suppliers, spareparts, technicians)
- Approval logs untuk tracking perubahan status dokumen
- Menu permissions untuk granular access control

### Tabel Utama
- `users` — Pengguna dan role assignment
- `menu_permissions` — Permission per role
- `units` — Master data alat berat
- `spareparts` — Master data suku cadang
- `purchase_requests` — Pengajuan pembelian
- `purchase_orders` — Order pembelian
- `goods_receipts` — Penerimaan barang
- `goods_issues` — Pengeluaran barang
- `stock_opnames` — Verifikasi stok
- `work_orders` — Order perbaikan
- `approval_settings` — Konfigurasi approval level
- `approval_logs` — History approval dokumen

---

## Developer

**Eddy Adha Saputra**
GitHub: [https://github.com/eddyyucca](https://github.com/eddyyucca)

---

## Lisensi

Dikembangkan oleh **PT Anugerah Sarana Hikmah**. Hak cipta dilindungi.
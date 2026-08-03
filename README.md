# Laravel Admin Panel — Base Project

Base project admin panel siap pakai berbasis **Laravel + Alpine.js + Tailwind CSS**. Dirancang sebagai starting point untuk membangun sistem back-office dengan CRUD, role-based permission per-menu, activity logging, dan export data — tanpa perlu setup ulang dari nol setiap kali mulai project baru.

## Fitur Utama

- **CRUD generik** yang konsisten di semua modul (search, sort, pagination, bulk-delete, export CSV)
- **Permission per-menu** — bukan sekadar role, tapi kombinasi role + menu + aksi (create/edit/delete/export/view), diatur lewat matrix UI
- **Menu dinamis & bertingkat (tree)** — sidebar dan struktur menu diatur dari database, mendukung kedalaman tak terbatas
- **Activity log otomatis** — semua perubahan data (create/update/delete) tercatat otomatis lewat trait, tanpa perlu nulis log manual di tiap Controller/Service
- **Frontend ringan** — Alpine.js + Blade component, tanpa build step SPA yang berat (Vue/React), tapi tetap reaktif dan modular

## Tech Stack

| Layer | Teknologi |
| --- | --- |
| Backend | Laravel 13.8, PHP 8.3 |
| Frontend | Alpine.js 3, Blade Components, Tailwind CSS |
| Build tool | Vite |
| Icon | Remix Icon (`ri-*` class) |
| Database | MySQL/MariaDB (default), kompatibel dengan driver Eloquent lain |

## Instalasi

```bash
git clone <repo-url> nama-project
cd nama-project

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Sesuaikan koneksi database di `.env`, lalu:

```bash
php artisan migrate --seed
```

Jalankan development server:

```bash
php artisan serve
npm run dev
```

Buka `http://127.0.0.1:8000` — login dengan akun default hasil seeder (lihat `database/seeders/UserSeeder.php` untuk kredensial).

## Struktur Project

Project ini konsisten memakai **Repository + Service pattern** di semua modul — bukan logic langsung di Controller.

```
app/
├── Http/
│   ├── Controllers/       # Tipis — cuma terima request, panggil Service, balas response
│   ├── Middleware/
│   │   └── CheckPermission.php   # Middleware inti sistem otorisasi (lihat bagian Permission System)
│   └── Requests/          # FormRequest untuk validasi, semua extends BaseFormRequest
├── Models/                # Eloquent model, murni relasi & accessor — tanpa query logic kompleks
├── Repositories/          # Query builder murni — tidak tahu apa-apa soal HTTP/JSON
│   └── Contracts/         # Interface, di-bind ke implementasi di RepositoryServiceProvider
├── Services/              # Business logic — orkestrasi Repository, transformasi data untuk response
│   ├── Contracts/
│   │   └── Exportable.php # Interface untuk Service yang punya fitur export CSV
│   ├── MenuAliasResolver.php  # Resolve link_alias menu -> menu_id, dengan cache
│   └── SidebarService.php     # Build struktur sidebar dari data menu + permission user
├── Support/
│   ├── CsvExporter.php            # Util reusable untuk stream CSV, dipakai semua modul
│   └── TableResponseFormatter.php # Format response paginated konsisten (data + meta)
└── Traits/
    └── LogsActivity.php   # Trait — tinggal `use LogsActivity` di model, otomatis tercatat ke activity_logs

resources/
├── js/
|   ├── components/ 
│   ├── pages/              # 1 file per modul, isi state Alpine (x-data) untuk halaman itu
│   └── utils/               # apiFetch, format tanggal, normalisasi response paginated, dll — reusable
└── views/
    ├── components/
    │   ├── atoms/           # Komponen dasar: input, button, select, badge, switch, dll
    │   ├── molecules/        # Kombinasi atom: data-table, modal, pagination, table-toolbar
    │   └── organisms/        # Sidebar, navbar — bagian besar layout
    └── pages/                # 1 folder per modul (index.blade.php + partials/)
```

## Konvensi & Pola yang Dipakai

Supaya konsisten saat menambah modul baru, ikuti pola yang sudah ada di modul existing (User/Role/Menu/Icon adalah contoh referensi paling lengkap).

### 1. Alur data 1 modul

```
Route → Controller (tipis) → Service (business logic) → Repository (query) → Model
```

Controller **tidak pernah** query langsung ke Model. Service **tidak pernah** tahu soal `Request`/`response()->json()` kecuali menerima `Request` untuk keperluan filter (lihat pola `baseQuery()` di Service manapun).

### 2. Menambah modul CRUD baru

1. **Model** — buat model + migration.
2. **Repository** — extends `BaseRepository`, implements interface sendiri (kalau butuh method custom di luar CRUD dasar).
3. **Service** — inject Repository, implementasikan minimal: `table()` (untuk data-table), dan `baseQuery()` sebagai satu sumber query yang dipakai bareng oleh `table()` dan `export()`.
4. **FormRequest** — `StoreXxxRequest`/`UpdateXxxRequest`, keduanya extends `BaseFormRequest` (otomatis balas JSON 422 untuk request AJAX, redirect+flash untuk request form biasa).
5. **Controller** — tipis, delegasikan ke Service.
6. **Route** — daftarkan di `routes/web.php`, ikuti format permission di bawah.
7. **View** — buat folder di `resources/views/pages/{modul}/`, isi `index.blade.php` + `partials/table.blade.php` + `partials/form-modal.blade.php`, compose dari komponen `x-molecules.data-table`, `x-molecules.table-toolbar`, dll yang sudah ada — jangan tulis ulang markup tabel/pagination/modal dari nol.
8. **JS** — buat `resources/js/pages/{modul}-management.js`, daftarkan di `resources/js/pages` import (cek file registrasi JS yang ada).

### 3. Export CSV

Kalau modul butuh export, implement interface `App\Services\Contracts\Exportable` di Service, dan pakai `App\Support\CsvExporter::stream()` — jangan tulis ulang `fopen`/`fputcsv` manual.

### 4. `data-table` component

Komponen inti `x-molecules.data-table` sudah mendukung: search+debounce, sort per-kolom (klik header), pagination, pemilihan jumlah data per halaman, bulk-select + bulk action bar, export button, kolom nomor urut otomatis. Cek prop yang tersedia di `resources/views/components/molecules/data-table.blade.php` sebelum menulis tabel manual.

## Sistem Permission

Ini bagian paling penting untuk dipahami sebelum menambah modul baru — salah konfigurasi di sini langsung berarti celah keamanan (route ke-expose tanpa proteksi) atau user terkunci dari fitur yang seharusnya boleh diakses.

### Cara kerja

Permission **bukan** dicek berdasarkan role langsung, tapi kombinasi 3 hal lewat tabel `role_menu_permissions`:

```
role_id + menu_id + permission_id
```

- **`permissions`** — tabel aksi generik: `view`, `create`, `edit`, `delete`, `export`. **Bukan** nama per-modul (jangan buat `user.view`, `role.view`, dst — itu salah).
- **`menus`** — tiap baris punya `link_alias` unik (contoh: `users.index`, `roles.index`), inilah yang jadi acuan resolusi menu di middleware.
- **`role_menu_permissions`** — pivot yang menentukan "role X boleh aksi Y di menu Z".

### Memakai middleware di route

```php
Route::middleware('permission:{aksi},{link_alias}')->...
```

Contoh:

```php
Route::middleware('permission:view,users.index')->get('/users/data', [UserController::class, 'data']);
Route::middleware('permission:create,users.index')->post('/users', [UserController::class, 'store']);
Route::middleware('permission:edit,users.index')->put('/users/{id}', [UserController::class, 'update']);
Route::middleware('permission:delete,users.index')->delete('/users/{id}', [UserController::class, 'destroy']);
```

**`{link_alias}` harus PERSIS SAMA** dengan nilai kolom `link_alias` di tabel `menus` untuk menu terkait — bukan disingkat, bukan diplural/singularkan sembarangan. `App\Services\MenuAliasResolver` mencocokkan secara *exact match*, bukan `LIKE`/prefix. Kalau typo atau tidak match, middleware akan `abort(403)` — fail-safe, bukan fail-open.

### Alur `CheckPermission` middleware

1. Cek user login & `status === 'active'`.
2. Kalau route butuh `$menuAlias`, resolve ke `menu_id` lewat `MenuAliasResolver` (di-cache).
3. Kalau alias disebut di route tapi tidak ketemu menu-nya → **403** (bukan lolos diam-diam).
4. Cek `$user->hasPermission($permission, $menuId)` — query ke `role_menu_permissions` lewat relasi role user.

### Menambah entri menu + permission baru

Setelah modul baru siap, tambahkan:

1. Baris baru di `database/seeders/MenuSeeder.php` (termasuk `icon_id`, `link_alias`, `parent_id` kalau nested).
2. Mapping izin di `database/seeders/RoleMenuPermissionSeeder.php` — pakai struktur `$matrix[roleName][menuName] = [actions]` yang eksplisit, **jangan** loop blanket ke semua menu kalau memang tidak semua role/menu perlu izin yang sama.

### Cache

`MenuAliasResolver` dan `SidebarService` sama-sama cache hasil query menu. Kalau ubah struktur menu (tambah/hapus/ubah `link_alias`) lewat cara **selain** UI Menu Management (misal seeder manual, tinker), clear cache manual:

```bash
php artisan cache:clear
```

Atau panggil `app(MenuAliasResolver::class)->clearAll()` dan `app(SidebarService::class)->clearAll()` di kode kalau memang perlu invalidate terprogram.

## Activity Log

Model apapun yang pakai trait `LogsActivity` otomatis tercatat ke tabel `activity_logs` setiap kali `created`/`updated`/`deleted` — tanpa perlu nulis log manual:

```php
class YourModel extends Model
{
    use LogsActivity;
    // selesai — created/updated/deleted otomatis tercatat
}
```

Password otomatis di-redact dari log perubahan (`recordActivity()` sudah handle ini). Log bersifat **read-only** dari sisi UI (tidak ada create/edit/delete manual) — sesuai sifatnya sebagai audit trail.

## Menjalankan Test

*(Belum ada test suite — lihat bagian "Yang Masih Perlu Ditambahkan" di bawah)*

```bash
php artisan test
```

## Yang Masih Perlu Ditambahkan

Base project ini sudah solid untuk fondasi CRUD + permission, tapi beberapa hal berikut belum tersedia dan perlu ditambahkan sesuai kebutuhan project turunan:

- [ ] Test suite (terutama untuk `CheckPermission` middleware — paling kritis dari sisi security)
- [ ] Forgot/reset password
- [ ] Halaman profile edit (saat ini masih placeholder)
- [ ] Halaman Settings sungguhan (saat ini redirect ke Menu Management)
- [ ] Halaman error kustom (404/403/500) yang konsisten dengan layout aplikasi
- [ ] Dashboard dengan widget/statistik (saat ini masih kosong)
- [ ] Rate limiting di route login
- [ ] `.env.example` — pastikan selalu sinkron dengan env var yang dipakai

## Kontribusi / Menambah Modul Baru

Sebelum menambah modul, cek dulu apakah komponen yang dibutuhkan sudah ada di `resources/views/components/` — sebagian besar kebutuhan UI (select dengan search, badge status dinamis, tombol aksi baris tabel, konfirmasi hapus) sudah tersedia sebagai komponen reusable. Menulis ulang markup yang sudah ada sebagai komponen adalah anti-pola di project ini.

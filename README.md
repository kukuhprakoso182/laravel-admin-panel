# Laravel Admin Panel — Base Project

Base project admin panel siap pakai berbasis **Laravel + Alpine.js + Tailwind CSS**. Dirancang sebagai starting point untuk membangun sistem back-office dengan CRUD, role-based permission per-menu, activity logging, dan export data — tanpa perlu setup ulang dari nol setiap kali mulai project baru.

## Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Tech Stack](#tech-stack)
- [Instalasi](#instalasi)
- [Struktur Project](#struktur-project)
- [Konvensi & Pola yang Dipakai](#konvensi--pola-yang-dipakai)
- [Sistem Permission](#sistem-permission)
- [Activity Log](#activity-log)
- [Testing](#testing)
- [Yang Masih Perlu Ditambahkan](#yang-masih-perlu-ditambahkan)
- [Kontribusi / Menambah Modul Baru](#kontribusi--menambah-modul-baru)

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

Project ini konsisten memakai **Repository + Service pattern** di semua modul — bukan logic langsung di Controller. Sebagian besar behavior CRUD generik (index, data-table, show, store, update, destroy) disediakan lewat base class + trait, supaya Controller/Service tiap modul cukup mengisi beberapa method kecil, bukan menulis ulang CRUD dari nol.

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── BaseCrudController.php   # Gabungan trait Has*Action di bawah — extend ini untuk controller CRUD baru
│   │   ├── Concerns/                 # Trait per-aksi controller, dipakai BaseCrudController
│   │   │   ├── HasIndexView.php          # index() -> return view()
│   │   │   ├── HasTableAction.php        # data() -> JSON untuk data-table (search/sort/paginate)
│   │   │   ├── HasShowAction.php         # show($id) -> JSON detail
│   │   │   ├── HasStoreAction.php        # store() -> validasi via FormRequest + create
│   │   │   ├── HasUpdateAction.php       # update($id) -> validasi via FormRequest + update
│   │   │   ├── HasDestroyAction.php      # destroy($id) -> delete
│   │   │   └── ValidatesWithFormRequest.php  # helper validasi + pesan sukses standar
│   │   └── ...                       # Controller per-modul, extends BaseCrudController
│   ├── Middleware/
│   │   └── CheckPermission.php       # Middleware inti sistem otorisasi (lihat bagian Permission System)
│   └── Requests/                     # FormRequest untuk validasi, semua extends BaseFormRequest
├── Models/                # Eloquent model, murni relasi & accessor — tanpa query logic kompleks
├── Repositories/
│   ├── BaseRepository.php        # Implementasi CRUD dasar (all/paginate/find/create/update/delete) + paginateFiltered()
│   └── Contracts/
│       └── BaseRepositoryInterface.php   # Kontrak dasar, di-extend oleh interface tiap modul
├── Services/
│   ├── BaseService.php            # Gabungan trait HasCrud + HasTable — extend ini untuk service CRUD baru
│   ├── Concerns/
│   │   ├── HasCrud.php                # find/create/update/delete/list, delegasi ke repository()
│   │   ├── HasTable.php               # table(), butuh searchableColumns()/sortableColumns()/formatRow() dari modul
│   │   └── HandlesForeignKeyViolation.php  # Ubah error FK constraint jadi pesan validasi yang rapi
│   ├── Contracts/
│   │   └── Exportable.php         # Interface untuk Service yang punya fitur export CSV
│   ├── MenuAliasResolver.php      # Resolve link_alias menu -> menu_id, dengan cache
│   └── SidebarService.php         # Build struktur sidebar dari data menu + permission user
├── Support/
│   ├── CsvExporter.php            # Util reusable untuk stream CSV, dipakai semua modul
│   └── TableResponseFormatter.php # Format response paginated konsisten (data + meta)
└── Traits/
    └── LogsActivity.php   # Trait — tinggal `use LogsActivity` di model, otomatis tercatat ke activity_logs

database/
└── seeders/
    ├── UserSeeder.php                 # Akun default untuk login awal
    ├── MenuSeeder.php                 # Data menu (link_alias, icon_id, parent_id untuk nested)
    └── RoleMenuPermissionSeeder.php   # Mapping role -> menu -> permission (matrix eksplisit)

routes/
└── web.php   # Semua route modul didaftarkan di sini, dibungkus middleware `permission:{aksi},{link_alias}`

resources/
├── js/
│   ├── alpine-loader.js   # Registrasi module JS per halaman — daftarkan module baru di sini (lihat data-module)
│   ├── components/
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
2. **Repository** — `extends BaseRepository`, `implements XxxRepositoryInterface` sendiri (`extends BaseRepositoryInterface`). Tambahkan method custom di interface kalau butuh query khusus di luar CRUD dasar.
3. **Service** — `extends BaseService`, inject Repository lewat constructor, lalu implementasikan:
   - `repository(): object` — kembalikan instance repository
   - `searchableColumns(): array` — kolom yang boleh dicari
   - `sortableColumns(): array` — kolom yang boleh dipakai sort (whitelist)
   - `formatRow(mixed $item): array` — bentuk row untuk response data-table
   - Override `baseQuery()` kalau butuh filter tambahan di atas query dasar (dipakai bareng oleh `table()` dan `export()` kalau modul punya export).
4. **FormRequest** — `StoreXxxRequest`/`UpdateXxxRequest`, keduanya extends `BaseFormRequest` (otomatis balas JSON 422 untuk request AJAX, redirect+flash untuk request form biasa).
5. **Controller** — `extends BaseCrudController`, implementasikan:
   - `service(): object`
   - `viewName(): string`
   - `storeRequestClass(): string` / `updateRequestClass(): string`
   - `messages(): array` (pesan created/updated/deleted)
6. **Route** — daftarkan di `routes/web.php`, ikuti format permission di bawah.
7. **View** — buat folder di `resources/views/pages/{modul}/`, isi `index.blade.php` + `partials/table.blade.php` + `partials/form-modal.blade.php`, compose dari komponen `x-molecules.data-table`, `x-molecules.table-toolbar`, dll yang sudah ada — jangan tulis ulang markup tabel/pagination/modal dari nol.
8. **JS** — buat `resources/js/pages/{modul}-management.js` (isi `window.xxxManagement = function () {...}` untuk `x-data`), lalu daftarkan di `resources/js/alpine-loader.js` supaya ke-load otomatis lewat atribut `data-module` di view.

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

## Testing

Test suite untuk project ini **belum tersedia** — lihat bagian [Yang Masih Perlu Ditambahkan](#yang-masih-perlu-ditambahkan). Prioritas utama saat menambahkannya adalah middleware `CheckPermission`, karena itu satu-satunya lapisan yang mencegah route ter-expose tanpa otorisasi.

Setelah test suite ditambahkan, jalankan dengan:

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

Command `php artisan make:module` untuk generate module CRUD lengkap
mengikuti arsitektur:

- `BaseCrudController` (trait `HasIndexView`, `HasTableAction`, `HasShowAction`,
  `HasStoreAction`, `HasUpdateAction`, `HasDestroyAction`)
- `BaseService` (trait `HasCrud`, `HasTable`)
- `BaseRepository` / `BaseRepositoryInterface`
- View Blade + Alpine.js (`data-module`, `x-data`, modal form) seperti pola
  `icon-management.js`

## Prasyarat di project

Command ini **mengasumsikan** base class/trait berikut sudah ada di project
(sesuai yang kamu kirim):

- `App\Http\Controllers\BaseCrudController`
- `App\Http\Controllers\Concerns\{HasIndexView,HasTableAction,HasShowAction,HasStoreAction,HasUpdateAction,HasDestroyAction,ValidatesWithFormRequest}`
- `App\Services\BaseService`
- `App\Services\Concerns\{HasCrud,HasTable,HandlesForeignKeyViolation}`
- `App\Repositories\BaseRepository`
- `App\Repositories\Contracts\BaseRepositoryInterface`
- `resources/js/alpine-loader.js` + helper global `apiUtils`, `formatUtils`, `tableUtils`
- Komponen Blade `x-layouts.app`, `x-molecules.table-toolbar`,
  `x-molecules.data-table`, `x-molecules.table-row-actions`,
  `x-molecules.modal`, `x-molecules.modal-form-actions`, `x-atoms.input`

Kalau salah satu belum ada, generate akan tetap jalan tapi hasilnya error
saat dipakai (karena extends/reference class yang belum ada).

## Instalasi

1. Copy `app/Console/Commands/MakeModule.php` ke project.
2. Copy folder `stubs/module/` ke root project (`stubs/module/*.stub`).

## Cara pakai

```bash
php artisan make:module Icon
```

Menghasilkan:

```
app/Http/Controllers/IconController.php
app/Http/Requests/StoreIconRequest.php
app/Http/Requests/UpdateIconRequest.php
app/Services/IconService.php
app/Repositories/Contracts/IconRepositoryInterface.php
app/Repositories/IconRepository.php
app/Models/Icon.php                          (skip dengan --no-model)
resources/views/pages/icons/index.blade.php
resources/views/pages/icons/partials/table.blade.php
resources/views/pages/icons/partials/form-modal.blade.php
resources/js/pages/icon-management.js
```

Nama module-key & fungsi Alpine otomatis mengikuti pola project:
`icon-management` / `window.iconManagement`, `product-category-management` /
`window.productCategoryManagement`, dst — supaya tinggal didaftarkan ke
`alpine-loader.js`.

Opsi:

- `--force` — timpa file yang sudah ada.
- `--no-model` — jangan generate Model (kalau model sudah ada duluan).

## Setelah generate (langkah manual, juga diprint di terminal)

1. Pastikan migration tabel sudah ada & dijalankan (kolom minimal: `name`).
2. Bind interface ke repository di `AppServiceProvider::register()`:

   ```php
   $this->app->bind(
       \App\Repositories\Contracts\IconRepositoryInterface::class,
       \App\Repositories\IconRepository::class
   );
   ```

3. Tambahkan route di `routes/web.php`:

   ```php
   Route::prefix('icons')->name('icons.')->group(function () {
       Route::get('/', [IconController::class, 'index'])->name('index');
       Route::get('/data', [IconController::class, 'data'])->name('data');
       Route::post('/', [IconController::class, 'store'])->name('store');
       Route::get('/{id}', [IconController::class, 'show'])->name('show');
       Route::put('/{id}', [IconController::class, 'update'])->name('update');
       Route::delete('/{id}', [IconController::class, 'destroy'])->name('destroy');
   });
   ```

4. Daftarkan module ke `resources/js/alpine-loader.js`:

   ```javascript
   'icon-management': () => import('./pages/icon-management.js'),
   ```

5. **Field default cuma `name`.** Sesuaikan kolom sesungguhnya di:
   `Store/UpdateRequest` (rules), `Service` (searchableColumns/sortableColumns/formatRow),
   view `partials/table.blade.php` & `partials/form-modal.blade.php`, dan
   `resources/js/pages/{module}.js` (form state, openEdit, submitForm payload)
   — sama seperti bedanya `IconService`/`icon-management.js` (value, section,
   is_active) dari default generic ini.

## Kustomisasi

Semua template ada di `stubs/module/*.stub`. Placeholder:

| Placeholder            | Contoh (`Icon`)      | Contoh (`ProductCategory`)          |
|-------------------------|-----------------------|---------------------------------------|
| `__CLASS__`             | `Icon`                | `ProductCategory`                     |
| `__CLASS_PLURAL__`      | `Icons`               | `ProductCategories`                   |
| `__VARIABLE__`          | `icon`                | `productCategory`                     |
| `__VARIABLE_PLURAL__`   | `icons`               | `productCategories`                   |
| `__KEBAB__`              | `icon`                | `product-category`                    |
| `__KEBAB_PLURAL__`       | `icons`               | `product-categories`                  |
| `__MODULE_KEY__`         | `icon-management`     | `product-category-management`         |
| `__ALPINE_FN__`          | `iconManagement`      | `productCategoryManagement`           |

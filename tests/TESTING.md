# Test Suite — Catatan & Cara Pakai

## Cara pasang ke project

1. Copy semua file di sini ke lokasi yang sama di project Laravel kamu:
   - `tests/Feature/**` → `tests/Feature/`
   - `tests/Unit/**` → `tests/Unit/`
   - `tests/Concerns/GrantsPermissions.php` → `tests/Concerns/`
   - `database/factories/*.php` → `database/factories/` (skip `UserFactory.php` kalau project kamu sudah punya sendiri — cek dulu isinya konsisten dengan field `status`, `password` yang dipakai test)

2. Set koneksi database testing di `phpunit.xml` (kalau belum ada), pakai SQLite in-memory supaya cepat dan tidak menyentuh database development:

```xml
<php>
    <env name="DB_CONNECTION" value="sqlite"/>
    <env name="DB_DATABASE" value=":memory:"/>
</php>
```

3. Jalankan:

```bash
php artisan test
# atau
./vendor/bin/phpunit
```

## Cakupan

| File | Yang diuji |
|---|---|
| `Feature/Auth/PermissionMiddlewareTest.php` | `CheckPermission` middleware — kombinasi role/menu/aksi, user nonaktif, alias menu tidak valid (fail-safe) |
| `Feature/Auth/BaseFormRequestTest.php` | Response 422 JSON vs redirect+session errors tergantung header `Accept` |
| `Feature/Users/UserCrudTest.php` | CRUD User + regression test bug `roles`/password yang pernah diperbaiki, bulk-delete, filter, search |
| `Feature/Roles/RoleCrudTest.php` | CRUD Role, sync permission matrix (termasuk penghapusan kombinasi lama), bulk-delete |
| `Feature/Menus/MenuCrudTest.php` | CRUD Menu, validasi parent tidak boleh diri sendiri, filter by parent |
| `Feature/Icons/IconCrudTest.php` | CRUD Icon dasar |
| `Feature/Permissions/PermissionCrudTest.php` | CRUD Permission dasar |
| `Feature/ActivityLogs/ActivityLogReadOnlyTest.php` | Read-only enforcement, auto-logging via trait, redaksi password, dedup update tanpa perubahan nyata |
| `Unit/Services/MenuServiceTreeTest.php` | Regression test bug rekursi tree menu (dulu cuma tembus 1-2 level) |
| `Unit/Services/MenuAliasResolverTest.php` | Exact-match resolusi alias, caching, invalidasi cache |
| `Unit/Support/CsvExporterTest.php` | Header CSV, escaping, content-type, nama file |
| `Unit/Support/TableResponseFormatterTest.php` | Struktur `data`+`meta`, kalkulasi pagination |

## Asumsi yang perlu dicek ulang

Test ini ditulis berdasarkan struktur & behavior yang sudah dikonfirmasi sepanjang pengembangan project — tapi beberapa detail berikut **perlu diverifikasi ulang** terhadap kode aktual sebelum dianggap 100% akurat, karena saya tidak selalu punya akses langsung ke isi file terbaru saat menulis test ini:

- **Nama route** — saya asumsikan route mengikuti pola yang sudah dikonfirmasi sebelumnya (`/users`, `/users/{id}`, `/users/bulk-delete`, dst). Kalau ada penyesuaian nama route setelahnya, sesuaikan juga di test.
- **Response shape `table()`** — saya asumsikan key `data` di response `/xxx/data` berisi array/collection hasil `TableResponseFormatter::format()`. Kalau formatnya beda, sesuaikan assertion `$response->json('data')`.
- **`grantFull()` di `GrantsPermissions`** — helper ini otomatis `firstOrCreate` menu berdasarkan `link_alias`. Kalau ada unique constraint tambahan di tabel `menus` (misal `name` juga unique), sesuaikan helper-nya.
- **UserFactory** — saya tidak menulis ulang `UserFactory.php` karena project sudah punya (lihat struktur folder `database/factories/`). Pastikan factory yang ada punya field `status` dengan default `'active'`, karena beberapa test override `status` secara eksplisit tapi yang lain mengandalkan default factory.
- **`ActivityLogController`** — saya asumsikan tidak ada route `POST /activity-logs` atau `DELETE /activity-logs/{id}` terdaftar sama sekali (test mengharapkan 404). Kalau controller ternyata punya method itu meski tidak dipakai UI, route-nya perlu benar-benar dihapus dari `routes/web.php`, bukan cuma tidak dipanggil dari frontend.

## Yang belum dicakup (di luar scope permintaan ini)

- Test untuk `SidebarService` (build sidebar dari permission user)
- Test untuk halaman auth (login/logout) — belum ada `AuthController` test
- Browser/Dusk test untuk interaksi Alpine.js (search debounce, sort klik header, dsb) — test di atas semua level HTTP/Service, bukan level browser
- Test untuk export CSV lewat endpoint HTTP sungguhan (baru diuji `CsvExporter` di level unit) — bisa ditambahkan `Feature/.../ExportTest.php` per modul kalau dibutuhkan lebih detail

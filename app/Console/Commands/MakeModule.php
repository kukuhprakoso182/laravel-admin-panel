<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModule extends Command
{
    /**
     * php artisan make:module Icon
     * php artisan make:module ProductCategory --force
     * php artisan make:module ProductCategory --no-tests
     */
    protected $signature = 'make:module {name : Nama module singular, misal: Icon atau ProductCategory}
                            {--force : Timpa file yang sudah ada}
                            {--no-model : Jangan generate Model & Factory}
                            {--no-tests : Jangan generate Feature/Unit test}';

    protected $description = 'Membuat module CRUD baru: Controller, Service, Repository, FormRequest, View (Alpine.js), JS, dan Test';

    protected string $stubPath;

    public function __construct()
    {
        parent::__construct();
        $this->stubPath = base_path('stubs/module');
    }

    public function handle(): int
    {
        $class        = Str::studly($this->argument('name'));        // Icon | ProductCategory
        $classPlural  = Str::plural($class);                          // Icons | ProductCategories
        $variable     = Str::camel($class);                           // icon | productCategory
        $variablePlur = Str::camel($classPlural);                     // icons | productCategories
        $kebab        = Str::kebab($class);                           // icon | product-category
        $kebabPlural  = Str::kebab($classPlural);                     // icons | product-categories
        $moduleKey    = $kebab . '-management';                       // icon-management | product-category-management
        $alpineFn     = $variable . 'Management';                     // iconManagement | productCategoryManagement
        $table        = Str::snake($classPlural);                     // icons | product_categories

        $replacements = [
            '__CLASS__'          => $class,
            '__CLASS_PLURAL__'   => $classPlural,
            '__VARIABLE__'       => $variable,
            '__VARIABLE_PLURAL__'=> $variablePlur,
            '__KEBAB__'          => $kebab,
            '__KEBAB_PLURAL__'   => $kebabPlural,
            '__MODULE_KEY__'     => $moduleKey,
            '__ALPINE_FN__'      => $alpineFn,
            '__TABLE__'          => $table,
        ];

        if (! File::exists($this->stubPath)) {
            $this->error("Folder stub tidak ditemukan di: {$this->stubPath}");
            return self::FAILURE;
        }

        $targets = [
            [
                'stub'   => 'controller.stub',
                'target' => app_path("Http/Controllers/{$class}Controller.php"),
            ],
            [
                'stub'   => 'request.store.stub',
                'target' => app_path("Http/Requests/Store{$class}Request.php"),
            ],
            [
                'stub'   => 'request.update.stub',
                'target' => app_path("Http/Requests/Update{$class}Request.php"),
            ],
            [
                'stub'   => 'service.stub',
                'target' => app_path("Services/{$class}Service.php"),
            ],
            [
                'stub'   => 'repository.interface.stub',
                'target' => app_path("Repositories/Contracts/{$class}RepositoryInterface.php"),
            ],
            [
                'stub'   => 'repository.stub',
                'target' => app_path("Repositories/{$class}Repository.php"),
            ],
            [
                'stub'   => 'view.index.stub',
                'target' => resource_path("views/pages/{$kebabPlural}/index.blade.php"),
            ],
            [
                'stub'   => 'view.table.stub',
                'target' => resource_path("views/pages/{$kebabPlural}/partials/table.blade.php"),
            ],
            [
                'stub'   => 'view.form-modal.stub',
                'target' => resource_path("views/pages/{$kebabPlural}/partials/form-modal.blade.php"),
            ],
            [
                'stub'   => 'js.stub',
                'target' => resource_path("js/pages/{$moduleKey}.js"),
            ],
        ];

        if (! $this->option('no-model')) {
            $targets[] = [
                'stub'   => 'model.stub',
                'target' => app_path("Models/{$class}.php"),
            ];
            $targets[] = [
                'stub'   => 'factory.stub',
                'target' => database_path("factories/{$class}Factory.php"),
            ];
        }

        if (! $this->option('no-tests')) {
            // Trait helper permission dipakai bareng oleh semua module — dibuat sekali.
            $targets[] = [
                'stub'   => 'test.permissions-trait.stub',
                'target' => base_path('tests/Concerns/InteractsWithPermissions.php'),
            ];
            $targets[] = [
                'stub'   => 'test.feature.stub',
                'target' => base_path("tests/Feature/{$classPlural}Test.php"),
            ];
            $targets[] = [
                'stub'   => 'test.service.stub',
                'target' => base_path("tests/Unit/{$class}ServiceTest.php"),
            ];
            $targets[] = [
                'stub'   => 'test.repository.stub',
                'target' => base_path("tests/Unit/{$class}RepositoryTest.php"),
            ];
        }

        foreach ($targets as $item) {
            $this->generate($item['stub'], $item['target'], $replacements);
        }

        $this->newLine();
        $this->info("✅ Module {$class} berhasil dibuat.");
        $this->newLine();
        $this->warn('Langkah manual yang masih perlu dilakukan:');

        $this->line("1. Pastikan migration untuk tabel '{$table}' sudah ada & dijalankan (kolom minimal: name).");

        $this->line("2. Bind interface ke repository di App\\Providers\\AppServiceProvider::register():");
        $this->line("   \$this->app->bind(");
        $this->line("       \\App\\Repositories\\Contracts\\{$class}RepositoryInterface::class,");
        $this->line("       \\App\\Repositories\\{$class}Repository::class");
        $this->line("   );");

        $this->line("3. Tambahkan route di routes/web.php (bungkus middleware permission!):");
        $this->line("   Route::prefix('{$kebabPlural}')->name('{$kebabPlural}.')->group(function () {");
        $this->line("       Route::middleware('permission:view,{$kebabPlural}.index')->get('/', [{$class}Controller::class, 'index'])->name('index');");
        $this->line("       Route::middleware('permission:view,{$kebabPlural}.index')->get('/data', [{$class}Controller::class, 'data'])->name('data');");
        $this->line("       Route::middleware('permission:create,{$kebabPlural}.index')->post('/', [{$class}Controller::class, 'store'])->name('store');");
        $this->line("       Route::middleware('permission:view,{$kebabPlural}.index')->get('/{id}', [{$class}Controller::class, 'show'])->name('show');");
        $this->line("       Route::middleware('permission:edit,{$kebabPlural}.index')->put('/{id}', [{$class}Controller::class, 'update'])->name('update');");
        $this->line("       Route::middleware('permission:delete,{$kebabPlural}.index')->delete('/{id}', [{$class}Controller::class, 'destroy'])->name('destroy');");
        $this->line("   });");

        $this->line("4. Daftarkan module JS di resources/js/alpine-loader.js:");
        $this->line("   '{$moduleKey}': () => import('./pages/{$moduleKey}.js'),");

        $this->line("5. Tambahkan menu di database/seeders/MenuSeeder.php dengan link_alias PERSIS '{$kebabPlural}.index',");
        $this->line("   lalu mapping izin role-nya di RoleMenuPermissionSeeder.php.");

        if (! $this->option('no-tests')) {
            $this->line("6. Cek tests/Concerns/InteractsWithPermissions.php — sesuaikan nama model/kolom Role/Menu/Permission");
            $this->line("   kalau berbeda dari asumsi (lihat komentar di file tsb), lalu jalankan:");
            $this->line("   php artisan test --filter={$classPlural}Test");
        }

        $this->line("7. Field default cuma 'name' — sesuaikan di Request, Service, View, JS, dan Test sesuai kolom asli model kamu.");

        return self::SUCCESS;
    }

    protected function generate(string $stub, string $target, array $replacements): void
    {
        if (File::exists($target) && ! $this->option('force')) {
            $this->warn('Dilewati (sudah ada): ' . str_replace(base_path() . '/', '', $target));
            return;
        }

        $stubFile = $this->stubPath . '/' . $stub;

        if (! File::exists($stubFile)) {
            $this->error("Stub tidak ditemukan: {$stub}");
            return;
        }

        $content = strtr(File::get($stubFile), $replacements);

        File::ensureDirectoryExists(dirname($target));
        File::put($target, $content);

        $this->line('Created: ' . str_replace(base_path() . '/', '', $target));
    }
}

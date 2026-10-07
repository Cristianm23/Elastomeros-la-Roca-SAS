<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Categories\Models\Category;
use App\Modules\Products\Models\Product;
use App\Modules\Services\Models\Service;
use App\Modules\Users\Models\Permission;
use App\Modules\Users\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $permissions = collect([
            'products.view', 'products.create', 'products.update', 'products.delete',
            'quotes.view', 'quotes.create', 'quotes.update', 'quotes.delete',
            'users.view', 'users.manage',
        ])->mapWithKeys(fn (string $slug) => [$slug => Permission::query()->updateOrCreate(
            ['slug' => $slug],
            ['name' => $slug, 'description' => "Permiso {$slug}"],
        )]);

        $roles = collect([
            'guest' => 'GUEST',
            'customer' => 'CUSTOMER',
            'employee' => 'EMPLOYEE',
            'admin' => 'ADMIN',
        ])->mapWithKeys(fn (string $name, string $slug) => [$slug => Role::query()->updateOrCreate(
            ['slug' => $slug],
            ['name' => $name],
        )]);

        $roles['admin']->permissions()->sync($permissions->values()->pluck('id'));
        $roles['employee']->permissions()->sync($permissions->filter(fn ($permission, string $slug) => ! str_starts_with($slug, 'users.'))->pluck('id'));
        $roles['customer']->permissions()->sync(Permission::query()->whereIn('slug', ['quotes.create', 'products.view'])->pluck('id'));

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $user->roles()->sync([$roles['customer']->id]);

        $categories = Category::factory(3)->create();
        $categories->each(fn (Category $category) => Product::factory(3)->create(['category_id' => $category->id]));
        Service::factory(3)->create();
    }
}

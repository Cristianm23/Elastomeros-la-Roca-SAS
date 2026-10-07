<?php

namespace Database\Factories;

use App\Modules\Services\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);
        return ['name' => $name, 'slug' => Str::slug($name), 'description' => fake()->paragraph(), 'is_active' => true];
    }
}

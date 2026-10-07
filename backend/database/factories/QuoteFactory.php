<?php

namespace Database\Factories;

use App\Modules\Quotes\Models\Quote;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class QuoteFactory extends Factory
{
    protected $model = Quote::class;

    public function definition(): array
    {
        return [
            'public_reference' => 'COT-'.strtoupper(Str::random(10)),
            'company_name' => fake()->company(),
            'contact_name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'message' => fake()->sentence(),
            'status' => 'pending',
        ];
    }
}

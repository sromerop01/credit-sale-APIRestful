<?php

namespace Database\Factories;

use App\Models\LoanRoad;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    return [
        'identification' => fake()->numberBetween(10000000, 99999999),
        'name' => fake()->name(),
        'address' => fake()->address(),
        'phone' => fake()->phoneNumber(),
        'detail' => fake()->sentence(),
        'delinquent' => fake()->boolean(10),
        'quota' => fake()->numberBetween(100, 5000),
        'interest' => fake()->randomFloat(2, 1, 20),
        'order' => fake()->numberBetween(1, 100),
        'loan_road_id' => LoanRoad::factory(),
    ];
}
}

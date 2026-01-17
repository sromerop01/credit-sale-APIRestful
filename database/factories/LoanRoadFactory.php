<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LoanRoad>
 */
class LoanRoadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Ruta ' . fake()->city(),
            'detail' => fake()->sentence(),
            'start_date' => fake()->date(),
            'sales_commission' => fake()->randomFloat(2, 1, 15),
            'length' => fake()->randomFloat(2, 10, 500),
            'latitude' => fake()->latitude(),
            'inactive' => fake()->boolean(),

            'user_id' => User::factory(),
            'supervisor_id' => User::factory(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\ServiceOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\ServiceOrder>
 */
class ServiceOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_date' => fake()->dateTimeBetween('now', '+1 year'),
            'service_description' => fake()->sentence(),
            'client_id' => Client::factory(),
        ];
    }
}

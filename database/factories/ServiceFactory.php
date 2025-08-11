<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cordinates = fake()->localCoordinates();
        return [
            'name' => fake()->word(),
            'description' => fake()->sentence(),
            'image' => fake()->imageUrl(640, 480, 'business'),
            'listings' => fake()->numberBetween(1, 100),
            'type' => fake()->randomElement(['daily', 'freelance']),
            'status' => fake()->randomElement(['busy', 'available']),
            'type' => fake()->randomElement(['timecount', 'freelance']),
            'address' => fake()->city(),
            'lat' => $cordinates['latitude'],
            'lng' => $cordinates['longitude'],
            'rating' => fake()->randomFloat(2, 0, 5),
            'user_id' => fake()->numberBetween(1, 15),
            'category_id' => fake()->numberBetween(1, 15),
            'global_service_id' =>fake()->numberBetween(1, 15)
        ];
    }
}

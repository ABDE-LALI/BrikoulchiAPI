<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServicesReviews>
 */
class ServiceReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'text' => fake()->text(),
            'service_id' => fake()->numberBetween(1, 15),
            'user_id' => fake()->numberBetween(1, 15),
            'likes' => fake()->numberBetween(10, 60),
            'rating' => fake()->randomFloat(2, 0, 5),
            'rating_count' => fake()->numberBetween(0, 100),
        ];
    }
}

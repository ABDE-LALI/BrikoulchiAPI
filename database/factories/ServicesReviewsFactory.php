<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServicesReviews>
 */
class ServicesReviewsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'text'=>fake()->text(),
            'raiting' =>fake()->randomFloat(2, 0, 5),
            'user_id' =>fake()->numberBetween(1, 15)
        ];
    }
}

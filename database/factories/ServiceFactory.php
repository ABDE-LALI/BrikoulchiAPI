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
            'title' => substr($this->faker->sentence(3), 0, 20),
            'description' => $this->faker->paragraph(),
            'image' => $this->faker->imageUrl(640, 480, 'business', true),
            'listings' => $this->faker->numberBetween(1, 100),
            'workDays' => $this->faker->randomElement(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']),
            'workHours' => $this->faker->sentence(2),
            'status' => $this->faker->randomElement(['busy', 'available']),
            'type' => $this->faker->randomElement(['timecount', 'freelance', 'fulltime', 'parttime']),
            'category_id' => $this->faker->numberBetween(1, 10),
            'global_service_id' => $this->faker->numberBetween(1, 10),
            'initial_service_id' => $this->faker->numberBetween(1, 10),
            'user_id' => $this->faker->numberBetween(1, 10),
            'lat' => 1.3454356,
            'lng' => 1.345432546356,
            'rating' => $this->faker->randomFloat(1, 0, 5),
        ];
    }
}

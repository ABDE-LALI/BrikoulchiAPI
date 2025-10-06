<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ChatMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'conversation_id' => fake()->numberBetween(1, 10),
            'sender_id' => fake()->numberBetween(1, 15),
            'receiver_id' => fake()->numberBetween(1, 15),
            'message' => fake()->text(50)
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->realTextBetween($minNbChars = 10, $maxNbChars = 100),
            'content' => fake()->paragraph(),
            'user_id' => User::factory(),
            'views' => fake()->numberBetween(0, 10),
        ];
    }
}

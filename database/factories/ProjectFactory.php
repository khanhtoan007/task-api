<?php

namespace Database\Factories;

use App\Http\Enums\ProjectStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
final class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(),
            'description' => fake()->sentence(),
            'status' => fake()->randomElement(ProjectStatusEnum::cases()),
            'start_date' => fake()->dateTime(),
            'end_date' => fake()->dateTime(),
        ];
    }
}

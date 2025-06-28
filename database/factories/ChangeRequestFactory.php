<?php

namespace Database\Factories;

use App\Models\ChangeRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChangeRequest>
 */
class ChangeRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected', 'draft']),
            'new_data' => json_encode([
                'additions' => [
                    'added-country_' . fake()->randomNumber(3) => [
                        'name' => fake()->country(),
                        'iso2' => fake()->countryCode(),
                        'iso3' => fake()->randomLetter() . fake()->randomLetter() . fake()->randomLetter(),
                    ]
                ]
            ]),
            'approved_by' => null,
            'approved_at' => null,
            'rejected_by' => null,
            'rejected_at' => null,
            'rejection_reason' => null,
        ];
    }

    /**
     * Indicate that the change request is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
            'rejected_by' => null,
            'rejected_at' => null,
        ]);
    }

    /**
     * Indicate that the change request is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'approved_by' => User::factory(),
            'approved_at' => now(),
            'rejected_by' => null,
            'rejected_at' => null,
        ]);
    }

    /**
     * Indicate that the change request is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'approved_by' => null,
            'approved_at' => null,
            'rejected_by' => User::factory(),
            'rejected_at' => now(),
            'rejection_reason' => fake()->sentence(),
        ]);
    }

    /**
     * Indicate that the change request is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'approved_by' => null,
            'approved_at' => null,
            'rejected_by' => null,
            'rejected_at' => null,
        ]);
    }

    /**
     * Create a change request with modification data.
     */
    public function withModifications(): static
    {
        return $this->state(fn (array $attributes) => [
            'new_data' => json_encode([
                'modifications' => [
                    'countries' => [
                        fake()->randomNumber(2) => [
                            'id' => fake()->randomNumber(2),
                            'name' => fake()->country(),
                            'iso2' => fake()->countryCode(),
                        ]
                    ]
                ]
            ]),
        ]);
    }

    /**
     * Create a change request with deletion data.
     */
    public function withDeletions(): static
    {
        return $this->state(fn (array $attributes) => [
            'new_data' => json_encode([
                'deletions' => [
                    'countries_' . fake()->randomNumber(2),
                    'states_' . fake()->randomNumber(2),
                ]
            ]),
        ]);
    }
}

<?php

namespace Database\Factories;

use App\Models\Event;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = CarbonImmutable::instance(
            fake()->dateTimeBetween('+1 day', '+6 months'),
        )->utc();

        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraphs(3, true),
            'location' => fake()->streetAddress(),
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->addHours(2),
        ];
    }
}

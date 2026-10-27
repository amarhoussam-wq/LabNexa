<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Request_reservation>
 */
class Request_reservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'phone' => '05' . $this->faker->numerify('########'),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'birth_date' => $this->faker->date('Y-m-d', '-20 years'),
            'preferred_date' => $this->faker->dateTimeBetween('+1 days', '+1 week')->format('Y-m-d'),
            'preferred_time' => $this->faker->time('H:i'),
            'status' => 'pending',
        ];
    }
}

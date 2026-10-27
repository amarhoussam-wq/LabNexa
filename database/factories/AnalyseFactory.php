<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Analyse>
 */
class AnalyseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'name_fr' => $this->faker->word,
            'description' => $this->faker->sentence,
            'normal_range' => '10-20',
            'code' => $this->faker->unique()->numerify('AN-####'),
            'price' => $this->faker->numberBetween(500, 5000),
            'duration' => '24h',
            'availability' => true,
        ];
    }
}

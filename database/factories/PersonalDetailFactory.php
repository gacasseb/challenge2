<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PersonalDetail>
 */
class PersonalDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'title' => $this->faker->jobTitle,
            'avatar' => $this->faker->imageUrl(),
            'linkedin_url' => $this->faker->url,
            'companyName' => $this->faker->company,
            'companyLikedinUrl' => $this->faker->url,
            'companyEmployees' => $this->faker->numberBetween(1, 1000),
        ];
    }
}

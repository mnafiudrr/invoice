<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->company();

        return [
            'user_id' => User::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(4),
            'client_name' => $this->faker->name(),
            'client_email' => $this->faker->safeEmail(),
            'client_company' => $this->faker->company(),
            'description' => $this->faker->sentence(),
            'access_password_hash' => bcrypt('secret-password'),
        ];
    }
}

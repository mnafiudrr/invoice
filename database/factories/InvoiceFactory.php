<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'invoice_number' => 'INV-'.$this->faker->unique()->year().'-'.str_pad((string) $this->faker->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
            'language' => 'en',
            'currency' => 'IDR',
            'subtotal' => 1000000,
            'tax' => 0,
            'total' => 1000000,
            'issued_at' => now(),
            'status' => Invoice::STATUS_DRAFT,
        ];
    }
}

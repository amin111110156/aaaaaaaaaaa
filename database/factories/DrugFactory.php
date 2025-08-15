<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Drug;

class DrugFactory extends Factory
{
    protected $model = Drug::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word(),
            'description' => $this->faker->sentence(),
            'category' => $this->faker->randomElement(['مسكنات', 'مضادات حيوية', 'فيتامينات', 'مهدئات', 'مضادات التهاب']),
            'manufacturer' => $this->faker->company(),
            'price' => $this->faker->randomFloat(2, 10, 500),
            'stock_quantity' => $this->faker->numberBetween(0, 1000),
            'expiry_date' => $this->faker->dateTimeBetween('+6 months', '+2 years'),
            'batch_number' => 'BATCH-' . strtoupper($this->faker->lexify('??????')),
        ];
    }
}
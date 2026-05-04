<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $indianProducts = ['Compost', 'Vermicompost', 'Organic Fertilizer', 'Soil Conditioner', 'Plant Growth Promoter', 'Bio-Pesticide', 'Mulch', 'Peat Moss', 'Coir Pith', 'Sand', 'Garden Soil', 'Neem Oil', 'Cattle Feed'];

        return [
            'user_id' => \App\Models\User::factory(),
            'name' => $this->faker->randomElement($indianProducts),
            'sku' => $this->faker->unique()->regexify('[A-Z]{3}[0-9]{5}'),
            'price' => $this->faker->numberBetween(500, 50000),
            'description' => $this->faker->sentence(),
        ];
    }
}

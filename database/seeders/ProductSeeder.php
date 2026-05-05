<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User;
use App\Models\Unit;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a default user for products
        $user = User::first() ?? User::factory()->create();

        // Get or create base unit (kg)
        $kgUnit = Unit::firstOrCreate(
            ['name' => 'kg'],
            ['description' => 'Kilogram - base unit for weight']
        );

        // Create products
        $products = [
            [
                'name' => 'Raw Compost',
                'sku' => 'RC-001',
                'price' => 3.00,
                'description' => 'Raw compost material',
                'base_unit_id' => $kgUnit->id,
            ],
            [
                'name' => 'Compost',
                'sku' => 'C-001',
                'price' => 8.00,
                'description' => 'Processed compost',
                'base_unit_id' => $kgUnit->id,
            ],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['name' => $product['name']],
                array_merge($product, ['user_id' => $user->id])
            );
        }
    }
}

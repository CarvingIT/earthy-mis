<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Consumable;

class ConsumableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $consumables = [
            ['item' => 'Bags', 'description' => 'Packaging bags for products'],
            ['item' => 'Handgloves', 'description' => 'Safety handgloves for workers'],
            ['item' => 'Petrol', 'description' => 'Fuel for machinery'],
            ['item' => 'Diesel', 'description' => 'Fuel for vehicles and equipment'],
            ['item' => 'Grease', 'description' => 'Lubrication grease for machinery'],
            ['item' => 'Gumboots', 'description' => 'Protective footwear for workers'],
            ['item' => 'Engine Oil', 'description' => 'Engine lubricant for vehicles'],
            ['item' => 'Stationary', 'description' => 'Office and administrative supplies'],
        ];

        foreach ($consumables as $consumable) {
            Consumable::firstOrCreate($consumable);
        }
    }
}

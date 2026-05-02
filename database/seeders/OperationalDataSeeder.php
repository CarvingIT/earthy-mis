<?php

namespace Database\Seeders;

use App\Models\Stock;
use App\Models\Sale;
use App\Models\SupplyItem;
use App\Models\Trips;
use App\Models\Consumable;
use App\Models\Turning;
use App\Models\Windrow;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Vehicle;
use App\Models\Weight;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class OperationalDataSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create consumables for supply items
        $consumableItems = [
            'Fertilizer',
            'Pesticide',
            'Seeds',
            'Tools',
            'Fuel',
            'Packaging',
            'Labor',
            'Transport'
        ];
        
        $consumables = [];
        foreach ($consumableItems as $item) {
            $consumables[] = Consumable::create([
                'item' => $item,
                'description' => 'Operational consumable: ' . $item,
            ]);
        }

        // Get all products and customers
        $products = Product::all();
        $customers = Customer::all();
        $vehicles = Vehicle::all();

        if ($products->isEmpty() || $customers->isEmpty()) {
            $this->command->error('Please run: php artisan db:seed --class=DatabaseSeeder first');
            return;
        }

        // Create some windrow entries for turning operations
        $windrows = [];
        for ($i = 0; $i < 5; $i++) {
            $windrows[] = Windrow::create([
                'windrow_number' => 'W-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'start_date' => now()->subDays(60 + $i * 10)->format('Y-m-d'),
                'end_date' => now()->subDays(50 + $i * 10)->format('Y-m-d'),
                'weight_in' => rand(5000, 15000),
            ]);
        }

        // Create 90 days of historical data
        for ($i = 90; $i > 0; $i--) {
            $date = now()->subDays($i);
            $dateStr = $date->format('Y-m-d');

            // Stock entries - create 2-3 per day
            for ($j = 0; $j < rand(2, 3); $j++) {
                Stock::create([
                    'Date' => $dateStr,
                    'product_id' => $products->random()->id,
                    'quantity' => rand(100, 500),
                ]);
            }

            // Sales entries - create 1-3 per day
            for ($j = 0; $j < rand(1, 3); $j++) {
                $product = $products->random();
                $quantity = rand(10, 100);
                $rate = rand(500, 2000);
                $amount = $quantity * $rate;

                Sale::create([
                    'Date' => $dateStr,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'customer_id' => $customers->random()->id,
                    'rate' => $rate,
                    'amount' => $amount,
                ]);

                // Also create a stock entry for sold quantity
                Stock::create([
                    'Date' => $dateStr,
                    'product_id' => $product->id,
                    'quantity' => rand(0, 50),
                ]);
            }

            // Supply items entries - create 1-2 per day
            for ($j = 0; $j < rand(1, 2); $j++) {
                $quantity = rand(5, 50);
                $cost = rand(500, 5000);

                SupplyItem::create([
                    'Date' => $dateStr,
                    'quantity' => $quantity,
                    'consumable_id' => $consumables[array_rand($consumables)]->id,
                    'description' => collect([
                        'Monthly supplies for operations',
                        'Field maintenance supplies',
                        'Regular operational expenses',
                        'Equipment maintenance cost',
                        'Transportation charges',
                    ])->random(),
                    'cost' => $cost,
                ]);
            }

            // Vehicle trips - create 0-2 per day
            if (rand(0, 1) === 1 && !$vehicles->isEmpty()) {
                for ($j = 0; $j < rand(1, 2); $j++) {
                    Trips::create([
                        'Date' => $dateStr,
                        'vehicle_id' => $vehicles->random()->id,
                        'purpose' => collect([
                            'Delivery',
                            'Pickup',
                            'Maintenance',
                            'Supply Collection',
                            'Field Work',
                            'Market Trip'
                        ])->random(),
                        'km' => rand(10, 100),
                    ]);
                }
            }

            // Create weight entries for operations
            if (rand(0, 1) === 1 && !$vehicles->isEmpty()) {
                Weight::create([
                    'Date' => $dateStr,
                    'vehicle_id' => $vehicles->random()->id,
                    'gross_weight' => rand(500, 2000),
                    'tare_weight' => rand(100, 300),
                    'net_weight' => rand(300, 1800),
                    'number_of_buckets' => rand(1, 10),
                ]);
            }

            // Create turning entries
            if (rand(0, 1) === 1 && !empty($windrows)) {
                Turning::create([
                    'Date' => $dateStr,
                    'windrow_id' => $windrows[array_rand($windrows)]->id,
                    'duration' => rand(1, 8),
                ]);
            }
        }

        $this->command->info('✅ Operational data seeded successfully!');
        $this->command->info('   - Stock entries: ' . Stock::count());
        $this->command->info('   - Sales entries: ' . Sale::count());
        $this->command->info('   - Supply items: ' . SupplyItem::count());
        $this->command->info('   - Trip entries: ' . Trips::count());
        $this->command->info('   - Weight entries: ' . Weight::count());
        $this->command->info('   - Turning entries: ' . Turning::count());
        $this->command->info('   - Windrow entries: ' . Windrow::count());
    }
}

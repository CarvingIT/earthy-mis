<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'is_admin' => true,
        ]);

        // Create data for admin user
        \App\Models\Customer::factory(15)->create(['user_id' => $adminUser->id]);
        \App\Models\Society::factory(8)->create(['user_id' => $adminUser->id]);
        \App\Models\Vehicle::factory(12)->create(['user_id' => $adminUser->id]);
        \App\Models\Product::factory(20)->create(['user_id' => $adminUser->id]);

        // Create regular users with their own data
        User::factory(10)->create()->each(function ($user) {
            \App\Models\Customer::factory(3)->create(['user_id' => $user->id]);
            \App\Models\Society::factory(2)->create(['user_id' => $user->id]);
            \App\Models\Vehicle::factory(2)->create(['user_id' => $user->id]);
            \App\Models\Product::factory(4)->create(['user_id' => $user->id]);
        });
    }
}

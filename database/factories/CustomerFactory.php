<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $indianFirstNames = ['Raj', 'Priya', 'Amit', 'Anjali', 'Vikram', 'Neha', 'Arjun', 'Divya', 'Rohan', 'Sneha', 'Aditya', 'Pooja', 'Nitin', 'Swati', 'Kunal'];
        $indianLastNames = ['Sharma', 'Singh', 'Patel', 'Kumar', 'Verma', 'Gupta', 'Reddy', 'Nair', 'Rao', 'Desai', 'Iyer', 'Menon'];
        $puneMohallaNames = ['Kothrud', 'Deccan', 'Aundh', 'Baner', 'Pashan', 'Kondhwa', 'Viman Nagar', 'Kalyani Nagar', 'Wakad', 'Hinjewadi', 'Hadapsar', 'Undri', 'Magarpatta', 'Katraj', 'Bibvewadi'];

        return [
            'user_id' => \App\Models\User::factory(),
            'name' => $this->faker->randomElement($indianFirstNames) . ' ' . $this->faker->randomElement($indianLastNames),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => '9' . $this->faker->numerify('##########'),
            'address' => $this->faker->numerify('###') . ', ' . $this->faker->randomElement($puneMohallaNames) . ', Pune',
        ];
    }
}

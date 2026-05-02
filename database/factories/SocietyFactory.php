<?php

namespace Database\Factories;

use App\Models\Society;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Society>
 */
class SocietyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $societyNames = ['Shriram', 'Rajshree', 'Sanman', 'Akshay', 'Vikram', 'Ganesh', 'Lakshmi', 'Saraswati', 'Hari', 'Om', 'Shanti', 'Sarvottam'];
        $suffixes = ['Heights', 'Residency', 'Complex', 'Apartments', 'Housing Society', 'Vihar', 'Nagar', 'Colony', 'Enclave', 'Plaza'];
        $puneMohallaNames = ['Kothrud', 'Deccan', 'Aundh', 'Baner', 'Pashan', 'Kondhwa', 'Viman Nagar', 'Kalyani Nagar', 'Wakad', 'Hinjewadi', 'Hadapsar', 'Undri', 'Magarpatta', 'Katraj', 'Bibvewadi'];
        $indianFirstNames = ['Raj', 'Priya', 'Amit', 'Anjali', 'Vikram', 'Neha', 'Arjun', 'Divya', 'Rohan', 'Sneha'];
        $indianLastNames = ['Sharma', 'Singh', 'Patel', 'Kumar', 'Verma', 'Gupta', 'Reddy', 'Nair', 'Rao', 'Desai'];

        return [
            'user_id' => \App\Models\User::factory(),
            'name' => $this->faker->randomElement($societyNames) . ' ' . $this->faker->randomElement($suffixes),
            'address' => $this->faker->numerify('###') . ', ' . $this->faker->randomElement($puneMohallaNames),
            'city' => 'Pune',
            'joining_month' => $this->faker->monthName(),
            'flats_families' => $this->faker->numberBetween(20, 200),
            'chairman_name' => $this->faker->randomElement($indianFirstNames) . ' ' . $this->faker->randomElement($indianLastNames),
            'secretary_name' => $this->faker->randomElement($indianFirstNames) . ' ' . $this->faker->randomElement($indianLastNames),
            'contact_person_email' => $this->faker->safeEmail(),
            'phone' => '9' . $this->faker->numerify('##########'),
        ];
    }
}

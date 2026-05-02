<?php

namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $registrationPrefixes = ['MH-02'];
        $indianBrands = ['Mahindra', 'Tata', 'Ashok Leyland', 'ISUZU', 'Hyundai', 'Maruti', 'Hero', 'Bajaj', 'TVS', 'Force'];
        $vehicleTypes = ['Truck', 'Van', 'Tempo', 'Auto', 'Bus', '3-wheeler'];
        $colors = ['White', 'Blue', 'Red', 'Yellow', 'Green', 'Black', 'Silver', 'Orange', 'Green'];

        return [
            'user_id' => \App\Models\User::factory(),
            'registration_number' => 'MH-02' . $this->faker->regexify('[A-Z]{2}[0-9]{4}'),
            'type' => $this->faker->randomElement($vehicleTypes),
            'brand' => $this->faker->randomElement($indianBrands),
            'model' => $this->faker->word(),
            'color' => $this->faker->randomElement($colors),
            'purchased_on' => $this->faker->dateTimeBetween('-5 years', 'now'),
        ];
    }
}

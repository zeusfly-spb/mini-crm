<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        $faker = fake('ru_RU');

        return [
            'name' => $faker->name(),
            'email' => $faker->unique()->safeEmail(),
            'phone' => $faker->phoneNumber(),
        ];
    }
}

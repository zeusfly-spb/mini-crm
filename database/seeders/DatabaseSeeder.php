<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        User::factory(3)->create();

        $customers = Customer::factory(5)->create();

        foreach ($customers as $customer) {
            Ticket::factory(fake()->numberBetween(1, 4))->create([
                'customer_id' => $customer->id,
            ]);
        }

        Ticket::factory()->create([
            'customer_id' => $customers->first()->id,
            'subject' => 'Не открывается личный кабинет',
            'description' => 'После авторизации отображается пустая страница.',
            'status' => 'new',
            'reply_date' => null,
        ]);

        Ticket::factory()->create([
            'customer_id' => $customers->last()->id,
            'subject' => 'Ошибка в счете',
            'description' => 'В выставленном счете неверная сумма НДС.',
            'status' => 'pending',
            'reply_date' => null,
        ]);
    }
}

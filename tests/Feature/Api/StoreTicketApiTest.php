<?php

namespace Tests\Feature\Api;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTicketApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_ticket_and_returns_201(): void
    {
        $payload = [
            'name' => 'Иван Иванов',
            'email' => 'ivan@example.com',
            'phone' => '+79991234567',
            'subject' => 'Проблема с формой',
            'description' => 'Не отправляется заявка',
        ];

        $response = $this->postJson('/api/tickets', $payload);

        $response
            ->assertCreated()
            ->assertJsonStructure(['message', 'ticket_id']);

        $this->assertDatabaseHas('customers', [
            'email' => 'ivan@example.com',
            'name' => 'Иван Иванов',
            'phone' => '+79991234567',
        ]);

        $customerId = Customer::where('email', 'ivan@example.com')->value('id');

        $this->assertDatabaseHas('tickets', [
            'customer_id' => $customerId,
            'subject' => 'Проблема с формой',
            'description' => 'Не отправляется заявка',
            'status' => 'new',
        ]);
    }

    public function test_it_returns_422_when_required_fields_are_missing(): void
    {
        $response = $this->postJson('/api/tickets', []);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'phone', 'subject', 'description']);
    }

    public function test_it_returns_429_when_same_email_sends_second_ticket_in_a_day(): void
    {
        $firstPayload = [
            'name' => 'Иван Иванов',
            'email' => 'ivan@example.com',
            'phone' => '+79991234567',
            'subject' => 'Первая заявка',
            'description' => 'Текст первой заявки',
        ];

        $secondPayload = [
            'name' => 'Иван Иванов',
            'email' => 'ivan@example.com',
            'phone' => '+79990000000',
            'subject' => 'Вторая заявка',
            'description' => 'Текст второй заявки',
        ];

        $this->postJson('/api/tickets', $firstPayload)->assertCreated();

        $response = $this->postJson('/api/tickets', $secondPayload);

        $response
            ->assertStatus(429)
            ->assertJsonPath('message', 'Вы уже отправляли заявку сегодня с этого email или номера телефона.');
    }

    public function test_it_returns_429_when_same_phone_sends_second_ticket_in_a_day(): void
    {
        $firstPayload = [
            'name' => 'Иван Иванов',
            'email' => 'ivan@example.com',
            'phone' => '+79991234567',
            'subject' => 'Первая заявка',
            'description' => 'Текст первой заявки',
        ];

        $secondPayload = [
            'name' => 'Петр Петров',
            'email' => 'petr@example.com',
            'phone' => '+79991234567',
            'subject' => 'Вторая заявка',
            'description' => 'Текст второй заявки',
        ];

        $this->postJson('/api/tickets', $firstPayload)->assertCreated();

        $response = $this->postJson('/api/tickets', $secondPayload);

        $response
            ->assertStatus(429)
            ->assertJsonPath('message', 'Вы уже отправляли заявку сегодня с этого email или номера телефона.');
    }
}

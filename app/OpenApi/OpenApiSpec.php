<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'Mini-CRM',
    version: '1.0.0',
    description: 'API documentation description'
)]
#[OA\Server(
    url: '/',
    description: 'API server'
)]
#[OA\PathItem(path: '/')]
class OpenApiSpec
{
    // This class only holds OpenAPI annotations.
}

#[OA\Schema(
    schema: 'Customer',
    required: ['id', 'name', 'email', 'phone'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Иван Иванов'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'ivan@example.com'),
        new OA\Property(property: 'phone', type: 'string', example: '+79991234567'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ],
    type: 'object'
)]
class CustomerSchema
{
    // OpenAPI schema container.
}

#[OA\Schema(
    schema: 'Ticket',
    required: ['id', 'customer_id', 'subject', 'description', 'status'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 10),
        new OA\Property(property: 'customer_id', type: 'integer', example: 1),
        new OA\Property(property: 'subject', type: 'string', example: 'Ошибка при сохранении'),
        new OA\Property(property: 'description', type: 'string', example: 'Форма не отправляет данные'),
        new OA\Property(property: 'status', type: 'string', enum: ['new', 'pending', 'resolved'], example: 'new'),
        new OA\Property(property: 'reply_date', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'customer', ref: '#/components/schemas/Customer', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ],
    type: 'object'
)]
class TicketSchema
{
    // OpenAPI schema container.
}

#[OA\Schema(
    schema: 'TicketCreateResponse',
    required: ['message', 'ticket_id'],
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Заявка успешно отправлена.'),
        new OA\Property(property: 'ticket_id', type: 'integer', example: 10),
    ],
    type: 'object'
)]
class TicketCreateResponseSchema
{
    // OpenAPI schema container.
}

<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        $faker = fake('ru_RU');
        $status = $faker->randomElement(['new', 'pending', 'resolved']);
        $subjects = [
            'Не могу войти в личный кабинет',
            'Ошибка при создании заявки',
            'Некорректно отображается отчет',
            'Не приходит письмо подтверждения',
            'Проблема с загрузкой документов',
            'Система зависает при сохранении',
            'Неверно рассчитывается сумма',
            'Не работает фильтр по клиентам',
        ];

        $descriptions = [
            'После ввода логина и пароля страница обновляется, но вход не выполняется.',
            'При нажатии на кнопку отправки появляется сообщение об ошибке сервера.',
            'В отчете отсутствуют данные за текущий период, хотя записи в системе есть.',
            'Письмо с подтверждением операции не пришло в течение 30 минут.',
            'Файл загружается до 100 процентов, после чего появляется ошибка обработки.',
            'При сохранении карточки клиента интерфейс перестает отвечать на действия.',
            'В итоговой сумме отображается значение, которое не совпадает с расчетом вручную.',
            'При выборе фильтра список клиентов не обновляется и остается прежним.',
        ];

        return [
            'customer_id' => Customer::factory(),
            'subject' => $faker->randomElement($subjects),
            'description' => $faker->randomElement($descriptions),
            'status' => $status,
            'reply_date' => $status === 'resolved'
                ? $faker->dateTimeBetween('-30 days', 'now')
                : null,
        ];
    }
}

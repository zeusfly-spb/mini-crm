# Mini CRM

## Запуск (Docker Sail)

```bash
cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
```

После запуска:
- CRM: `http://localhost`
- Админка заявок: `http://localhost/admin/tickets`
- Виджет: `http://localhost/widget`
- Альтернативный URL виджета: `http://localhost/feedback-widget`

## Тестовые данные

- Менеджер:
  - email: `manager@example.com`
  - password: `password`
- Также создаются случайные клиенты и заявки через factory/seeder.

## Встраивание виджета (iframe)

```html
<iframe
  src="http://localhost/widget"
  width="100%"
  height="700"
  style="border:0;"
  loading="lazy"
></iframe>
```

## Локальный внешний сайт для теста iframe

1) Откройте папку `widget-host`

2) Запустите локальный сервер в этой папке:

```bash
python3 -m http.server 8081
```

3) Откройте:
- внешний сайт: `http://localhost:8081`
- встроенный в него виджет: `http://localhost/widget`


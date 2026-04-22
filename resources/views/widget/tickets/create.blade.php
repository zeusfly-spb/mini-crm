<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Виджет обратной связи</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 16px; background: #f6f7f9; }
        .card { max-width: 680px; margin: 0 auto; background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 8px 24px rgba(0,0,0,.08); }
        h1 { margin: 0 0 16px; font-size: 22px; }
        .field { margin-bottom: 12px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; font-size: 14px; }
        input, textarea { width: 100%; box-sizing: border-box; padding: 10px 12px; border: 1px solid #d5d7de; border-radius: 8px; font-size: 14px; }
        textarea { min-height: 110px; resize: vertical; }
        .error { color: #c62828; font-size: 12px; margin-top: 4px; }
        .status { margin-bottom: 12px; border-radius: 8px; padding: 10px 12px; font-size: 14px; display: none; }
        .status.success { background: #e8f5e9; color: #1b5e20; display: block; }
        .status.fail { background: #ffebee; color: #b71c1c; display: block; }
        .btn { border: 0; border-radius: 8px; padding: 10px 14px; font-size: 14px; background: #0a7f40; color: #fff; cursor: pointer; }
        .btn[disabled] { opacity: 0.65; cursor: not-allowed; }
        .hint { font-size: 12px; color: #666; margin-top: 6px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Обратная связь</h1>

        <div id="statusBox" class="status"></div>

        <form id="widgetTicketForm" novalidate>
            <div class="field">
                <label for="name">Имя</label>
                <input id="name" name="name" type="text" required>
                <div data-error-for="name" class="error"></div>
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" required>
                <div data-error-for="email" class="error"></div>
            </div>

            <div class="field">
                <label for="phone">Телефон</label>
                <input id="phone" name="phone" type="text" required>
                <div data-error-for="phone" class="error"></div>
            </div>

            <div class="field">
                <label for="subject">Тема</label>
                <input id="subject" name="subject" type="text" required>
                <div data-error-for="subject" class="error"></div>
            </div>

            <div class="field">
                <label for="description">Описание</label>
                <textarea id="description" name="description" required></textarea>
                <div data-error-for="description" class="error"></div>
            </div>

            <div class="field">
                <label for="attachment">Файл (необязательно)</label>
                <input id="attachment" name="attachment" type="file">
                <div class="hint">Максимальный размер: 10 МБ.</div>
                <div data-error-for="attachment" class="error"></div>
            </div>

            <button id="submitBtn" class="btn" type="submit">Отправить заявку</button>
        </form>
    </div>

    <script>
        const form = document.getElementById('widgetTicketForm');
        const submitBtn = document.getElementById('submitBtn');
        const statusBox = document.getElementById('statusBox');

        function setStatus(type, text) {
            statusBox.className = 'status ' + type;
            statusBox.textContent = text;
        }

        function clearErrors() {
            document.querySelectorAll('[data-error-for]').forEach((node) => {
                node.textContent = '';
            });
        }

        function showErrors(errors) {
            Object.entries(errors).forEach(([field, messages]) => {
                const node = document.querySelector('[data-error-for="' + field + '"]');
                if (node && Array.isArray(messages) && messages.length > 0) {
                    node.textContent = messages[0];
                }
            });
        }

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            clearErrors();
            setStatus('', '');
            submitBtn.disabled = true;

            try {
                const response = await fetch('/api/tickets', {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const payload = await response.json();

                if (!response.ok) {
                    if (payload.errors) {
                        showErrors(payload.errors);
                        setStatus('fail', 'Проверьте корректность заполнения формы.');
                    } else {
                        setStatus('fail', payload.message || 'Не удалось отправить заявку.');
                    }
                    return;
                }

                form.reset();
                setStatus('success', payload.message || 'Заявка отправлена.');
            } catch (error) {
                setStatus('fail', 'Сервер недоступен. Попробуйте позже.');
            } finally {
                submitBtn.disabled = false;
            }
        });
    </script>
</body>
</html>

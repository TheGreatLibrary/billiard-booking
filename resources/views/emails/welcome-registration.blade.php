<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif">
<table width="100%" cellpadding="0" cellspacing="0" style="padding:32px 16px">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.06)">

    <tr><td style="background:linear-gradient(135deg,#6366f1,#4f46e5);padding:32px 40px;text-align:center">
        <h1 style="margin:0;color:#fff;font-size:24px;font-weight:700">🎱 Добро пожаловать!</h1>
        <p style="margin:8px 0 0;color:rgba(255,255,255,.85);font-size:14px">СтолБронь — бронирование бильярдных столов</p>
    </td></tr>

    <tr><td style="padding:32px 40px">
        <p style="margin:0 0 20px;font-size:16px;color:#374151">
            Здравствуйте, <strong>{{ $user->name }}</strong>!
        </p>
        <p style="margin:0 0 24px;font-size:15px;color:#4b5563;line-height:1.6">
            Ваш аккаунт успешно создан. Теперь вы можете бронировать бильярдные столы онлайн — выбирайте удобное время, место и оплачивайте прямо на сайте.
        </p>

        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;border-radius:12px;margin-bottom:24px">
            <tr><td style="padding:16px 20px;border-bottom:1px solid #e5e7eb">
                <span style="color:#6b7280;font-size:13px">Имя</span><br>
                <strong style="color:#111827;font-size:15px">{{ $user->name }}</strong>
            </td></tr>
            <tr><td style="padding:16px 20px;border-bottom:1px solid #e5e7eb">
                <span style="color:#6b7280;font-size:13px">Email</span><br>
                <strong style="color:#111827;font-size:15px">{{ $user->email }}</strong>
            </td></tr>
            @if($user->phone)
            <tr><td style="padding:16px 20px">
                <span style="color:#6b7280;font-size:13px">Телефон</span><br>
                <strong style="color:#111827;font-size:15px">{{ $user->phone }}</strong>
            </td></tr>
            @endif
        </table>

        <p style="margin:0;color:#6b7280;font-size:14px;text-align:center">
            Приятного отдыха! 🎱
        </p>
    </td></tr>

    <tr><td style="padding:24px 40px;background:#f9fafb;text-align:center;border-top:1px solid #e5e7eb">
        <p style="margin:0;color:#9ca3af;font-size:12px">Это автоматическое сообщение от системы СтолБронь.</p>
    </td></tr>

</table>
</td></tr>
</table>
</body>
</html>
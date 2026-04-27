<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif">
<table width="100%" cellpadding="0" cellspacing="0" style="padding:32px 16px">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.06)">

    <tr><td style="background:linear-gradient(135deg,#f59e0b,#d97706);padding:32px 40px;text-align:center">
        <h1 style="margin:0;color:#fff;font-size:24px;font-weight:700">⏰ Напоминание о визите</h1>
        <p style="margin:8px 0 0;color:rgba(255,255,255,.85);font-size:14px">Через 1 час</p>
    </td></tr>

    <tr><td style="padding:32px 40px">
        <p style="margin:0 0 24px;font-size:16px;color:#374151">
            {{ $clientName }}, напоминаем — ваше бронирование <strong>#{{ $booking->id }}</strong> начинается через час!
        </p>

        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;border-radius:12px;padding:20px;margin-bottom:24px">
            <tr><td style="padding:12px 20px;border-bottom:1px solid #e5e7eb">
                <span style="color:#6b7280;font-size:13px">Куда</span><br>
                <strong style="color:#111827;font-size:15px">{{ $place->name ?? '—' }}</strong>
                @if($place->address ?? null)
                    <br><span style="color:#9ca3af;font-size:12px">📍 {{ $place->address }}</span>
                @endif
            </td></tr>
            <tr><td style="padding:12px 20px;border-bottom:1px solid #e5e7eb">
                <span style="color:#6b7280;font-size:13px">Когда</span><br>
                <strong style="color:#111827;font-size:15px">{{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}, {{ $slots->first() }} — {{ $slots->last() }}:59</strong>
            </td></tr>
            <tr><td style="padding:12px 20px">
                <span style="color:#6b7280;font-size:13px">Столы</span><br>
                <strong style="color:#111827;font-size:15px">{{ $resources->pluck('code')->implode(', ') }}</strong>
            </td></tr>
        </table>

        <p style="margin:0;color:#6b7280;font-size:14px;text-align:center">Приятного отдыха! 🎱</p>
    </td></tr>

    <tr><td style="padding:24px 40px;background:#f9fafb;text-align:center;border-top:1px solid #e5e7eb">
        <p style="margin:0;color:#9ca3af;font-size:12px">Это автоматическое напоминание.</p>
    </td></tr>

</table>
</td></tr>
</table>
</body>
</html>
<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif">
<table width="100%" cellpadding="0" cellspacing="0" style="padding:32px 16px">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.06)">

    <tr><td style="background:linear-gradient(135deg,#ef4444,#dc2626);padding:32px 40px;text-align:center">
        <h1 style="margin:0;color:#fff;font-size:24px;font-weight:700">
            @if($reason === 'expired') ⏱ Бронирование истекло
            @elseif($reason === 'refunded') 💸 Возврат средств
            @else ❌ Бронирование отменено
            @endif
        </h1>
        <p style="margin:8px 0 0;color:rgba(255,255,255,.85);font-size:14px">Заказ #{{ $booking->id }}</p>
    </td></tr>

    <tr><td style="padding:32px 40px">
        <p style="margin:0 0 24px;font-size:16px;color:#374151">
            {{ $clientName }},
            @if($reason === 'expired')
                к сожалению, время оплаты вашего бронирования истекло и оно было автоматически отменено.
            @elseif($reason === 'refunded')
                средства по вашему бронированию были возвращены.
            @else
                ваше бронирование было отменено.
            @endif
        </p>

        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;border-radius:12px;padding:20px;margin-bottom:24px">
            <tr><td style="padding:12px 20px;border-bottom:1px solid #e5e7eb">
                <span style="color:#6b7280;font-size:13px">Заведение</span><br>
                <strong style="color:#111827;font-size:15px">{{ $place->name ?? '—' }}</strong>
            </td></tr>
            <tr><td style="padding:12px 20px;border-bottom:1px solid #e5e7eb">
                <span style="color:#6b7280;font-size:13px">Было запланировано</span><br>
                <strong style="color:#111827;font-size:15px">{{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}, {{ $slots->implode(', ') }}</strong>
            </td></tr>
            <tr><td style="padding:12px 20px">
                <span style="color:#6b7280;font-size:13px">Столы</span><br>
                <strong style="color:#111827;font-size:15px">{{ $resources->pluck('code')->implode(', ') }}</strong>
            </td></tr>
        </table>

        @if($reason === 'expired')
            <p style="margin:0;color:#6b7280;font-size:14px;text-align:center">
                Вы всегда можете создать новое бронирование на нашем сайте.
            </p>
        @endif
    </td></tr>

    <tr><td style="padding:24px 40px;background:#f9fafb;text-align:center;border-top:1px solid #e5e7eb">
        <p style="margin:0;color:#9ca3af;font-size:12px">Это автоматическое сообщение.</p>
    </td></tr>

</table>
</td></tr>
</table>
</body>
</html>
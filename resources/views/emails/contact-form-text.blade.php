НОВАЯ ЗАЯВКА С САЙТА ФОТОГРАФА РОМАНА ЮНА
==================================================

@if(!empty($data['package']))
ФОРМАТ СЪЁМКИ: {{ $data['package'] }}
@endif

Имя: {{ $data['name'] ?? '—' }}
Email: {{ $data['email'] ?? '—' }}
@if(!empty($data['phone']))
Телефон: {{ $data['phone'] }}
@endif
@if(!empty($data['telegram']))
Telegram: {{ $data['telegram'] }}
@endif
Дата: {{ now()->timezone('Asia/Irkutsk')->format('d.m.Y H:i') }} (Иркутск, UTC+8)

СООБЩЕНИЕ / ПОЖЕЛАНИЯ:
--------------------------------------------------
{{ $data['message'] ?? '—' }}

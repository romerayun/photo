НОВАЯ ЗАЯВКА С САЙТА ФОТОГРАФА РОМАНА ЮНА
==================================================

@if(!empty($data['package']))
ФОРМАТ СЪЁМКИ: {{ $data['package'] }}
@endif

Имя: {{ $data['name'] ?? '—' }}
Способ связи: {{ $data['contact_method_label'] ?? 'Способ связи' }}
Контакт: {{ $data['contact_value'] ?? ($data['email'] ?? ($data['phone'] ?? ($data['telegram'] ?? '—'))) }}
Дата: {{ now()->timezone('Asia/Irkutsk')->format('d.m.Y H:i') }} (Иркутск, UTC+8)

СООБЩЕНИЕ / ПОЖЕЛАНИЯ:
--------------------------------------------------
{{ $data['message'] ?? '—' }}

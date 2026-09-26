<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Новая заявка с сайта</title>
</head>
<body style="margin: 0; padding: 0; background-color: #0d0d0d; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1a1a1a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #0d0d0d; padding: 30px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width: 600px; background-color: #ffffff; border-radius: 4px; overflow: hidden; border: 1px solid #262626;">
                    
                    {{-- Header --}}
                    <tr>
                        <td style="background-color: #000000; padding: 24px 30px; border-bottom: 2px solid #b91c1c;">
                            <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 2px; color: #b91c1c; font-weight: bold; display: block; margin-bottom: 4px;">Сайт фотографа</span>
                            <h1 style="margin: 0; font-size: 20px; font-weight: 800; color: #ffffff; text-transform: uppercase; letter-spacing: 1px;">Новая заявка на съёмку</h1>
                        </td>
                    </tr>

                    {{-- Content --}}
                    <tr>
                        <td style="padding: 30px;">
                            
                            @if(!empty($data['package']))
                            <div style="margin-bottom: 24px; padding: 14px 18px; background-color: #fef2f2; border-left: 4px solid #b91c1c;">
                                <span style="font-size: 10px; text-transform: uppercase; letter-spacing: 1.5px; color: #991b1b; font-weight: bold; display: block; margin-bottom: 2px;">Выбранный формат съёмки</span>
                                <strong style="font-size: 16px; color: #7f1d1d;">{{ $data['package'] }}</strong>
                            </div>
                            @endif

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 24px;">
                                <tr>
                                    <td width="35%" style="padding: 8px 0; font-size: 13px; color: #737373; text-transform: uppercase; letter-spacing: 1px;">Имя клиента:</td>
                                    <td style="padding: 8px 0; font-size: 15px; font-weight: 700; color: #171717;">{{ $data['name'] ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td width="35%" style="padding: 8px 0; font-size: 13px; color: #737373; text-transform: uppercase; letter-spacing: 1px;">Способ связи:</td>
                                    <td style="padding: 8px 0; font-size: 15px; font-weight: 700; color: #b91c1c;">
                                        {{ $data['contact_method_label'] ?? 'Способ связи' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td width="35%" style="padding: 8px 0; font-size: 13px; color: #737373; text-transform: uppercase; letter-spacing: 1px;">Контактные данные:</td>
                                    <td style="padding: 8px 0; font-size: 16px; font-weight: 700; color: #171717;">
                                        @if(($data['contact_method'] ?? '') === 'email')
                                            <a href="mailto:{{ $data['contact_value'] ?? ($data['email'] ?? '') }}" style="color: #b91c1c; text-decoration: none; font-weight: 700;">{{ $data['contact_value'] ?? ($data['email'] ?? '') }}</a>
                                        @elseif(($data['contact_method'] ?? '') === 'phone')
                                            <a href="tel:{{ preg_replace('/[^\d+]/', '', $data['contact_value'] ?? ($data['phone'] ?? '')) }}" style="color: #171717; text-decoration: none; font-weight: 700;">{{ $data['contact_value'] ?? ($data['phone'] ?? '') }}</a>
                                        @elseif(($data['contact_method'] ?? '') === 'telegram')
                                            <a href="https://t.me/{{ ltrim($data['contact_value'] ?? ($data['telegram'] ?? ''), '@') }}" style="color: #0284c7; text-decoration: none; font-weight: 700;">{{ $data['contact_value'] ?? ($data['telegram'] ?? '') }}</a>
                                        @else
                                            <span>{{ $data['contact_value'] ?? '—' }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; font-size: 13px; color: #737373; text-transform: uppercase; letter-spacing: 1px;">Дата и время:</td>
                                    <td style="padding: 8px 0; font-size: 13px; color: #525252;">{{ now()->timezone('Asia/Irkutsk')->format('d.m.Y H:i') }} (Иркутск, UTC+8)</td>
                                </tr>
                            </table>

                            <div style="border-top: 1px solid #e5e5e5; padding-top: 20px;">
                                <span style="font-size: 12px; text-transform: uppercase; letter-spacing: 1.5px; color: #737373; font-weight: bold; display: block; margin-bottom: 8px;">Сообщение / пожелания:</span>
                                <div style="background-color: #fafafa; border: 1px solid #e5e5e5; padding: 16px; font-size: 14px; line-height: 1.6; color: #262626; white-space: pre-wrap;">{{ $data['message'] ?? '—' }}</div>
                            </div>

                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #fafafa; padding: 16px 30px; border-top: 1px solid #e5e5e5; text-align: center; font-size: 11px; color: #a3a3a3;">
                            Письмо отправлено с формы обратной связи сайта фотографа Романа Юна.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>

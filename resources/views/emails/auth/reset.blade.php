@component('emails.layouts.apple')
    <h2 style="font-size: 20px; font-weight: 600; color: #1d1d1f; margin-top: 0; margin-bottom: 16px;">
        Восстановление пароля
    </h2>
    
    <p style="margin: 0 0 16px 0;">
        Вы получили это письмо, так как был запрошен сброс пароля для вашей учетной записи на платформе Exereto.
    </p>

    <a href="{{ $url }}" class="email-btn">
        Сбросить пароль
    </a>

    <p style="margin: 24px 0 0 0; font-size: 13px; color: #86868b; line-height: 1.5;">
        Ссылка для сброса пароля действительна в течение 60 минут. Если вы не запрашивали сброс пароля, никаких дополнительных действий не требуется.
    </p>
@endcomponent
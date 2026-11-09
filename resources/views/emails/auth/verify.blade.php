@component('emails.layouts.apple')
    <h2 style="font-size: 20px; font-weight: 600; color: #1d1d1f; margin-top: 0; margin-bottom: 16px;">
        Здравствуйте, {{ $user->name }}!
    </h2>
    
    <p style="margin: 0 0 16px 0;">
        Спасибо за регистрацию на платформе Exereto! Для активации вашего аккаунта и получения доступа к управлению заказами, пожалуйста, подтвердите ваш адрес электронной почты.
    </p>

    <a href="{{ $url }}" class="email-btn">
        Подтвердить email
    </a>

    <p style="margin: 24px 0 0 0; font-size: 13px; color: #86868b; line-height: 1.5;">
        Если вы не создавали аккаунт на нашем сайте, вы можете смело проигнорировать это письмо.
    </p>
@endcomponent
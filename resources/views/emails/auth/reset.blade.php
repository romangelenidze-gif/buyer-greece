@component('emails.layouts.apple')
    <h2 style="font-size: 20px; font-weight: 600; color: #1d1d1f; margin-top: 0; margin-bottom: 16px;">
        {{ __('auth.reset_title') }}
    </h2>
    
    <p style="margin: 0 0 16px 0;">
        {{ __('auth.reset_body') }}
    </p>

    <a href="{{ $url }}" class="email-btn">
        {{ __('auth.reset_button') }}
    </a>

    <p style="margin: 24px 0 0 0; font-size: 13px; color: #86868b; line-height: 1.5;">
        {{ __('auth.reset_footer') }}
    </p>
@endcomponent
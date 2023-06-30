@component('mail::message')
# Hello<span class="splash">,</span> {{$name}},

Two-Step login has been enabled on your account.

## Your account recovery token is below

@component('mail::panel')
    {{$token}}
@endcomponent

In the event that you lose access to your authenticator app, your recovery token will be required to regain access to your account

@component('mail::subcopy')
    If you did not enable Two-Step login for your account, please use your recovery token to login and <a href="{{env('ACCOUNT_APP_URL')}}/account/support" target="_blank" rel="noreferrer">contact support</a> immediately
@endcomponent

With love,<br>
{{ config('app.name') }}
@endcomponent

@component('mail::message')
# Hello {{$name}},

Two-Factor Authentication (2FA) has been enabled on your account.

## Your account recovery token is below

@component('mail::panel')
    {{$token}}
@endcomponent

In the event that you lose access to your authenticator app, your recovery token will be required to regain access to your account

@component('mail::subcopy')
    If you did not enable 2FA for your account, please use your recovery token to login and <a href="http://{{env('BASE_APP')}}/account/support" target="_blank" rel="noreferrer">contact support</a> immediately
@endcomponent

With Love,<br>
{{ config('app.name') }}
@endcomponent

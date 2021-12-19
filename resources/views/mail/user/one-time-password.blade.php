@component('mail::message')
# Hello {{$name}},

A One Time Password has been requested for your account.

## Requested At: {{$time}}
## Request IP: {{$ip}}

@component('mail::panel')
    {{$otp}}
@endcomponent

This password will expire 5 minutes from the date of creation
@component('mail::subcopy')
    If you did not recently request a one time password you, it is suggested that you change your password <a href="http://{{env('BASE_APP')}}/account/security" target="_blank" rel="noreferrer">here</a>
@endcomponent

With Love,<br>
{{ config('app.name') }}
@endcomponent

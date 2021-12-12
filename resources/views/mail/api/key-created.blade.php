@component('mail::message')
# Hello {{$name}},

An API Key has been created for your account.

## Key Name: {{$key_name}}

@component('mail::panel')
{{$token}}
@endcomponent

This will expire 5 years from the date of creation and can be revoked at any time using the <a href="http://{{env('BASE_APP')}}/account/security" target="_blank" rel="noreferrer">account security page</a>

@component('mail::subcopy')
    If you did not recently create an API Key on your account, please revoke it <a href="http://{{env('BASE_APP')}}/account/security" target="_blank" rel="noreferrer">here</a> and change your password immediately
@endcomponent

With Love,<br>
{{ config('app.name') }}
@endcomponent

@component('mail::message')
# Welcome To Nonverse,

## You have been invited to join the Nonverse Network

Please follow the link below to activate your account and begin the registration process

## Email: {{$email}}

@component('mail::panel')
    {{$key}}
@endcomponent

This activation key is can only be activated by the email above, and will expire in 30 days

@component('mail::button', ['url' => 'http://' . env('AUTH_SERVER') . '/register?email=' . $email])
    Activate Account
@endcomponent

@component('mail::subcopy')
    If you're having trouble clicking the "Activate Account" button, copy and paste the URL below into your web browser: <a
        href="http://{{env('AUTH_SERVER')}}/register?email={{$email}}">http://{{env('AUTH_SERVER')}}/register?email={{$email}}</a>
@endcomponent

With Love,<br>
{{ config('app.name') }}
@endcomponent

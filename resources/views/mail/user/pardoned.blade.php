@component('mail::message')
# Welcome back, {{$name}}

Your account has been pardoned, and you may now log in to the Nonverse members' area
<br>
Your access to game servers will remain restricted until your profile is pardoned

@component('mail::button', ['url' => 'http://' . env('BASE_APP')])
Members Area
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent

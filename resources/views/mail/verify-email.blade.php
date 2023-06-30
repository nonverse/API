<x-mail::message>
# Hello<span class="splash">,</span> {{$name}}

This e-mail address has been linked with a new or existing Nonverse account.
    <br>
Please click the link below to verify ownership of this e-mail

<x-mail::button :url="$url">
Verify E-Mail
</x-mail::button>

<x-mail::subcopy>
If you did not recently create or update a Nonverse account with this e-mail and would like to remove your e-mail from the linked account, please click
    <a href="">here</a>
</x-mail::subcopy>
With love,<br>
{{ config('app.name') }}
</x-mail::message>

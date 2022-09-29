@component('mail::message')
# Hi, {{ $user->username }}

A request has been received to change the password for your account.
<br><br>
You can send us a request to reset your password at:

@component('mail::table')
| Url                        | Method |
| -------------------------- | ------ |
| {{ $update_password_url }} | Patch  |
@endcomponent

with the following fields:

@component('mail::table')
| Field                 | Value                          |
| --------------------- | ------------------------------ |
| token                 | {{ $token }}                   |
| password              | YOUR_NEW_PASSWORD              |
| password_confirmation | YOUR_NEW_PASSWORD_CONFIRMATION |
@endcomponent

If you don't use this link within 1 hour, it will expire.
To get a new password reset link, send us a request at:

@component('mail::table')
| Url                       | Method |
| ------------------------- | ------ |
| {{ $reset_password_url }} | Post  |
@endcomponent

with the following fields:

@component('mail::table')
| Field | Value      |
| ----- | ---------- |
| email | YOUR_EMAIL |
@endcomponent

Thanks,<br>
{{ $app_name }}
@endcomponent

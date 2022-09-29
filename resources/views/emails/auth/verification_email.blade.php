@component('mail::message')
# Hi, {{ $user->username }}

You're almost done. To complete your {{ $app_name }} register,
we just need to verify your email address: {{ $user->email }}
<br><br><br>
You can use the following link to verify your email address:

@component('mail::button', ['url' => $verify_url])
Verify Email
@endcomponent

Thanks,<br>
{{ $app_name }}
@endcomponent

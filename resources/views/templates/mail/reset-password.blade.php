@component('mail::message')
Hai {{ $notifiable->name }},

Kami menerima permintaan reset password, klik link di bawah ini untuk melanjutkan reset:

@component('mail::button', ['url' => $url])
Reset Password
@endcomponent

Abaikan email ini jika kamu tidak melakukan permintaan reset password.

@include('templates.mail.regards')

@slot('subcopy')
Jika kamu kesulitan untuk klik tombol "Reset Password", copy dan paste URL di bawah ini ke browser pilihan anda: [{{ $url }}]({{ $url }})
@endslot
@endcomponent

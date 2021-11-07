@component('mail::message')

Hello {{$name}}


@component('mail::button', ['url' => route('getResetPassword', $token)])
Click here to reset your password
@endcomponent
<p>Or copy & paste the follwing link to your browser</p>
<p><a href="{{route('getResetPassword', $token)}}">
        {{route('getResetPassword', $token)}}</a></p>


Thanks,<br>
{{ config('app.name') }}
@endcomponent

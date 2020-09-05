@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url').'/gizmo'])
            {{-- <img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('/images/logo-blue.svg')))}}" alt="{{ config('app.name') }}" height="65px;"> --}}
            Gizmo
        @endcomponent
    @endslot

    Hey there, <br/>
    we have received password reset request for your account.
    <br/>
    <a href="{{$url}}" style="background-color: #eeeeee; color: #333333; padding: 15px 32px; margin: 15px auto; text-align: center; font-weight: bold; text-decoration: none; display: inline-block; font-size: 16px;">Reset Your Password</a>
    <br/>
    If you didn't make the request, just ignore this email.
    <br/><br/>
    Best regards, Health Numeracy Project team.

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        @endcomponent
    @endslot
@endcomponent

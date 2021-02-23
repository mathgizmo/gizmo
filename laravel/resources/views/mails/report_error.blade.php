@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url').'/gizmo'])
            {{-- <img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('/images/logo-blue.svg')))}}" alt="{{ config('app.name') }}" height="65px;"> --}}
            Gizmo
        @endcomponent
    @endslot

    You received a new error report from {{ $student->email }}.

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

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    
</head>
<body class="min-h-screen">

{{ $slot }}

@fluxScripts
@livewireScripts
@stack('scripts')
</body>
</html>

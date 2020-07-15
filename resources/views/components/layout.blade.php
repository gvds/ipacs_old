<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}

  <title>{{ config('app.name') }}</title>

  <!-- Scripts -->
  <script src="{{ asset('js/app.js') }}" defer></script>

  <!-- Styles -->
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">

</head>

<body>

    <div class="flex flex-col min-h-screen">
      @include('layouts.nav')
      <main class="flex-1 p-6 bg-gray-50 lg:px-8">
        {{ $slot }}
      </main>
    </div>

</body>

</html>
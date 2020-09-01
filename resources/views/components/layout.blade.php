<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style='font-size:16px'>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name') }}</title>

  <!-- Scripts -->
  <script src="{{ secure_asset('js/app.js') }}" defer></script>

  <!-- Styles -->
  <link href="{{ secure_asset('css/app.css') }}" rel="stylesheet">

</head>

<body>
  @php
  $currentProject = \App\project::find(session('currentProject', null));
  @endphp
  <div class="flex flex-col min-h-screen">
    <header class='bg-gray-600'>
      @if ($currentProject)
      <div class="text-xl font-bold text-white px-6">
        {{ $currentProject->project }}
      </div>
      @endif
      @include('layouts.nav')
    </header>
    <main class="flex flex-1 bg-gray-50 py-6 px-6">
      {{ $centred ?? ''}}
      <div class="w-max-content">
        {{ $slot }}
      </div>
    </main>
    <footer class='bg-gray-600'>
      <div class='text-white text-opacity-75 text-xs font-semibold text-right italic'>
        Created by: GD van der Spuy
      </div>
    </footer>
  </div>

</body>

</html>
{{-- resources/views/components/app-layout.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name','Laravel') }}</title>

  <!-- Fuentes -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

  <!-- Tailwind y tu JS via Vite -->
  @vite(['resources/css/app.css','resources/js/app.js'])

  {{-- slot para inyectar scripts en el <head> (A-Frame, Alpine) --}}
  {{ $head ?? '' }}
</head>
<body class="font-sans antialiased">
  <div class="flex flex-col w-full min-h-screen bg-white">

    <x-navbar />

    <main class="flex-grow">
      {{ $slot }}
    </main>

    {{-- Footer removido --}}
    {{-- <x-footer /> --}}
  </div>

  {{-- slot para inyectar JS al final del body --}}
  {{ $scripts ?? '' }}
</body>
</html>

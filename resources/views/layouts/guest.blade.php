{{-- resources/views/components/guest-layout.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script src="https://aframe.io/releases/1.4.2/aframe.min.js"></script>

</head>
<body class="font-sans text-gray-900 antialiased">

  {{-- Contenedor flex-col para empujar el footer --}}
  <div class="flex flex-col min-h-screen bg-white w-full">

    {{-- 1) Cabecera --}}
    <x-navbar />

    {{-- 2) Contenido principal --}}
    <main class="flex-grow">
      {{ $slot }}
    </main>

    {{-- 3) Pie de página --}}
    <x-footer />


  </div>
</body>
</html>

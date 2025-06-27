{{-- layouts/app.blade.php --}}
<body class="font-sans antialiased">
  <div class="flex flex-col min-h-screen">
    <x-navbar />

    <main class="flex-grow">
      {{ $slot }}
    </main>

    <x-footer />
  </div>
</body>
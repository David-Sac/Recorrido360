{{-- layouts/app.blade.php --}}
<html>
  <body>
    <x-navbar />
    <main>
      {{ $slot }}
    </main>
  </body>
</html>

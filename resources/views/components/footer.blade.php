{{-- resources/views/components/footer.blade.php --}}
@props(['visible' => true])

@if (! filter_var($visible, FILTER_VALIDATE_BOOLEAN))
  {{-- oculto --}}
@else
<footer class="py-10 text-white bg-green-700">
  <div class="grid grid-cols-1 gap-8 px-6 mx-auto max-w-7xl sm:grid-cols-2 md:grid-cols-4">

    {{-- Logo + Social --}}
    <div class="space-y-4">
      <img src="{{ asset('images/logo.png') }}" alt="Logo Ecomuseo" class="h-20">
      <div class="flex space-x-4">
        <a href="#" aria-label="TikTok"><img src="{{ asset('icons/tiktok.png') }}" alt="TikTok" class="w-8 h-8"></a>
      </div>
    </div>

    {{-- Use cases --}}
    <div>
      <h4 class="mb-2 font-semibold">Use cases</h4>
      <ul class="space-y-1">
        <li><a href="#" class="hover:underline">UI design</a></li>
        <li><a href="#" class="hover:underline">UX design</a></li>
        <li><a href="#" class="hover:underline">Wireframing</a></li>
        <li><a href="#" class="hover:underline">Diagramming</a></li>
        <li><a href="#" class="hover:underline">Brainstorming</a></li>
        <li><a href="#" class="hover:underline">Online whiteboard</a></li>
        <li><a href="#" class="hover:underline">Team collaboration</a></li>
      </ul>
    </div>

    {{-- Explore --}}
    <div>
      <h4 class="mb-2 font-semibold">Explore</h4>
      <ul class="space-y-1">
        <li><a href="#" class="hover:underline">Design</a></li>
        <li><a href="#" class="hover:underline">Prototyping</a></li>
        <li><a href="#" class="hover:underline">Development features</a></li>
        <li><a href="#" class="hover:underline">Design systems</a></li>
        <li><a href="#" class="hover:underline">Collaboration features</a></li>
        <li><a href="#" class="hover:underline">Design process</a></li>
        <li><a href="#" class="hover:underline">FigJam</a></li>
      </ul>
    </div>

    {{-- Resources --}}
    <div>
      <h4 class="mb-2 font-semibold">Resources</h4>
      <ul class="space-y-1">
        <li><a href="#" class="hover:underline">Blog</a></li>
        <li><a href="#" class="hover:underline">Best practices</a></li>
        <li><a href="#" class="hover:underline">Colors</a></li>
        <li><a href="#" class="hover:underline">Color wheel</a></li>
        <li><a href="#" class="hover:underline">Support</a></li>
        <li><a href="#" class="hover:underline">Developers</a></li>
        <li><a href="#" class="hover:underline">Resource library</a></li>
      </ul>
    </div>

  </div>
</footer>
@endif

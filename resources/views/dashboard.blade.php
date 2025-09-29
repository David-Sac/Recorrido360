{{-- resources/views/dashboard.blade.php --}}
<x-app-layout :show-footer="false">
  {{-- TOOLBOX: solo título, sin acciones --}}
  <x-ui.toolbox title="Panel de administración" />

  <div class="py-8">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

      {{-- Bienvenida / mensaje del día --}}
      <div class="p-6 mb-8 overflow-hidden bg-white border shadow-sm border-slate-200 sm:rounded-lg">
        <p class="text-slate-800">¡Bienvenido, {{ Auth::user()->name }}!</p>
      </div>

      {{-- Accesos rápidos en tarjetas --}}
      <section aria-labelledby="quick-actions">
        <h2 id="quick-actions" class="mb-3 text-sm font-medium tracking-wide text-slate-500">
          Accesos rápidos
        </h2>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
          <a href="{{ route('recorridos.index') }}"
             class="block p-5 transition bg-white border rounded-lg shadow-sm group border-slate-200 hover:shadow-md hover:border-slate-300">
            <div class="flex items-start gap-3">
              <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-emerald-50 text-emerald-700">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M3 5h18v2H3m0 6h12v2H3m0 6h18v2H3"/></svg>
              </span>
              <div>
                <h3 class="font-semibold text-slate-900">Recorridos</h3>
                <p class="mt-1 text-sm text-slate-600">Estructura de recorridos 360°.</p>
              </div>
            </div>
          </a>

          <a href="{{ route('componentes.index') }}"
             class="block p-5 transition bg-white border rounded-lg shadow-sm group border-slate-200 hover:shadow-md hover:border-slate-300">
            <div class="flex items-start gap-3">
              <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-emerald-50 text-emerald-700">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M7 3h10v2H7M5 7h14v2H5M3 11h18v2H3M7 15h10v2H7"/></svg>
              </span>
              <div>
                <h3 class="font-semibold text-slate-900">Componentes</h3>
                <p class="mt-1 text-sm text-slate-600">Bloques/categorías de contenido.</p>
              </div>
            </div>
          </a>

          <a href="{{ route('elementos.index') }}"
             class="block p-5 transition bg-white border rounded-lg shadow-sm group border-slate-200 hover:shadow-md hover:border-slate-300">
            <div class="flex items-start gap-3">
              <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-emerald-50 text-emerald-700">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M4 6h16v12H4zM6 8v8h12V8z"/></svg>
              </span>
              <div>
                <h3 class="font-semibold text-slate-900">Elementos</h3>
                <p class="mt-1 text-sm text-slate-600">Texto, imagen, video o audio.</p>
              </div>
            </div>
          </a>

          <a href="{{ route('panoramas.index') }}"
             class="block p-5 transition bg-white border rounded-lg shadow-sm group border-slate-200 hover:shadow-md hover:border-slate-300">
            <div class="flex items-start gap-3">
              <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-emerald-50 text-emerald-700">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M21 5H3v14h18V5zM5 7h14v10H5z"/></svg>
              </span>
              <div>
                <h3 class="font-semibold text-slate-900">Panoramas</h3>
                <p class="mt-1 text-sm text-slate-600">Gestiona imágenes 360°.</p>
              </div>
            </div>
          </a>
        </div>
      </section>
    </div>
  </div>
</x-app-layout>

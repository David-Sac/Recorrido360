{{-- resources/views/dashboard.blade.php --}}
<x-app-layout :show-footer="false">
  {{-- App bar minimal: solo breadcrumbs + título --}}
  <x-admin.toolbar
    title="Panel de administración"
    :breadcrumbs="[
      ['label'=>'Dashboard']
    ]"
  />

  <div class="py-8">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

      {{-- Bienvenida --}}
      <div class="p-6 mb-8 overflow-hidden bg-white shadow-sm sm:rounded-lg">
        <p class="text-slate-800">
          {{ __("¡Bienvenido, :name!", ['name' => Auth::user()->name]) }}
        </p>
      </div>

      @role('Admin|Super Admin')
      {{-- Accesos rápidos (cards consistentes) --}}
      <section aria-labelledby="quick-actions">
        <h2 id="quick-actions" class="mb-3 text-sm font-medium tracking-wide text-slate-500">
          Accesos rápidos
        </h2>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
          {{-- Recorridos --}}
          <a href="{{ route('recorridos.index') }}"
             class="block p-6 transition bg-white border rounded-lg shadow-sm group border-slate-200 hover:border-slate-300 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-400">
            <div class="flex items-start gap-3">
              <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-emerald-50 text-emerald-700">
                {{-- icono --}}
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M3 5h18v2H3zm0 6h12v2H3zm0 6h18v2H3z"/></svg>
              </span>
              <div>
                <h3 class="font-semibold text-slate-900">Recorridos</h3>
                <p class="mt-1 text-sm text-slate-600">Estructura de los recorridos 360°.</p>
              </div>
            </div>
          </a>

          {{-- Componentes --}}
          <a href="{{ route('componentes.index') }}"
             class="block p-6 transition bg-white border rounded-lg shadow-sm group border-slate-200 hover:border-slate-300 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-400">
            <div class="flex items-start gap-3">
              <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-emerald-50 text-emerald-700">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M7 3h10v2H7zm-2 4h14v2H5zm-2 4h18v2H3zm4 4h10v2H7z"/></svg>
              </span>
              <div>
                <h3 class="font-semibold text-slate-900">Componentes</h3>
                <p class="mt-1 text-sm text-slate-600">Categorías o bloques de contenido.</p>
              </div>
            </div>
          </a>

          {{-- Elementos --}}
          <a href="{{ route('elementos.index') }}"
             class="block p-6 transition bg-white border rounded-lg shadow-sm group border-slate-200 hover:border-slate-300 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-400">
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

          {{-- Panoramas --}}
          <a href="{{ route('panoramas.index') }}"
             class="block p-6 transition bg-white border rounded-lg shadow-sm group border-slate-200 hover:border-slate-300 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-400">
            <div class="flex items-start gap-3">
              <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-emerald-50 text-emerald-700">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M21 5H3v14h18V5zm-2 2v10H5V7h14z"/></svg>
              </span>
              <div>
                <h3 class="font-semibold text-slate-900">Panoramas</h3>
                <p class="mt-1 text-sm text-slate-600">Sube y administra imágenes 360°.</p>
              </div>
            </div>
          </a>
        </div>
      </section>
      @endrole

    </div>
  </div>
</x-app-layout>

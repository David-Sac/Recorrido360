{{-- resources/views/dashboard.blade.php --}}
<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800">
      {{ __('Dashboard') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

      {{-- Bienvenida --}}
      <div class="p-6 mb-8 overflow-hidden bg-white shadow-sm sm:rounded-lg">
        {{ __("¡Bienvenido, :name!", ['name' => Auth::user()->name]) }}
      </div>

      @role('Admin|Super Admin')
      <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        {{-- Recorridos --}}
        <a href="{{ route('recorridos.index') }}"
           class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50">
          <h3 class="text-lg font-semibold text-gray-900">Recorridos</h3>
          <p class="mt-2 text-gray-600">Gestiona alta, edición y baja de los componentes.</p>
        </a>
        {{-- Componentes --}}
        <a href="{{ route('componentes.index') }}"
           class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50">
          <h3 class="text-lg font-semibold text-gray-900">Componentes</h3>
          <p class="mt-2 text-gray-600">Gestiona alta, edición y baja de los componentes.</p>
        </a>

        {{-- Elementos --}}
        <a href="{{ route('elementos.index') }}"
           class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50">
          <h3 class="text-lg font-semibold text-gray-900">Elementos</h3>
          <p class="mt-2 text-gray-600">Crea, edita y elimina los elementos asociados a componentes.</p>
        </a>

        {{-- Panoramas --}}
        <a href="{{ route('panoramas.index') }}"
           class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50">
          <h3 class="text-lg font-semibold text-gray-900">Panoramas</h3>
          <p class="mt-2 text-gray-600">Sube y administra imágenes 360° de los recorridos.</p>
        </a>
      </div>
      @endrole

    </div>
  </div>
</x-app-layout>

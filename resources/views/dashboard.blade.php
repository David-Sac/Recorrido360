{{-- resources/views/dashboard.blade.php --}}
<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('Dashboard') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

      {{-- Bienvenida --}}
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8">
        {{ __("¡Bienvenido, :name!", ['name' => Auth::user()->name]) }}
      </div>

      @role('Admin|Super Admin')
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- Componentes --}}
        <a href="{{ route('componentes.index') }}"
           class="block p-6 bg-white rounded-lg border border-gray-200 shadow hover:bg-gray-50">
          <h3 class="text-lg font-semibold text-gray-900">Componentes</h3>
          <p class="mt-2 text-gray-600">Gestiona alta, edición y baja de los componentes.</p>
        </a>

        {{-- Elementos --}}
        <a href="{{ route('elementos.index') }}"
           class="block p-6 bg-white rounded-lg border border-gray-200 shadow hover:bg-gray-50">
          <h3 class="text-lg font-semibold text-gray-900">Elementos</h3>
          <p class="mt-2 text-gray-600">Crea, edita y elimina los elementos asociados a componentes.</p>
        </a>

        {{-- Panoramas --}}
        <a href="{{ route('panoramas.index') }}"
           class="block p-6 bg-white rounded-lg border border-gray-200 shadow hover:bg-gray-50">
          <h3 class="text-lg font-semibold text-gray-900">Panoramas</h3>
          <p class="mt-2 text-gray-600">Sube y administra imágenes 360° de los recorridos.</p>
        </a>
      </div>
      @endrole

    </div>
  </div>
</x-app-layout>

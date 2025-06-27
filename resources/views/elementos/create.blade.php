{{-- resources/views/elementos/create.blade.php --}}
<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Crear Elemento</h2>
  </x-slot>

  <main class="py-6 max-w-3xl mx-auto px-4">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold">Nuevo Elemento</h1>
      <a href="{{ route('elementos.index') }}"
         class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
        ← Volver
      </a>
    </div>

    <x-alert type="success"/>
    <x-alert type="warning"/>
    <x-alert type="error"/>

    <form action="{{ route('elementos.store') }}"
          method="POST"
          class="bg-white p-6 rounded-lg shadow space-y-6">
      @csrf
      @include('elementos._form')
      <div class="flex justify-end">
        <x-primary-button>Guardar</x-primary-button>
      </div>
    </form>
  </main>
</x-app-layout>

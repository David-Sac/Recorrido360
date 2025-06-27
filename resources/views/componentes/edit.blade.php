<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800">Editar Componente</h2>
  </x-slot>

  <main class="py-6 max-w-3xl mx-auto px-4">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold mb-4">Editar: {{ $componente->titulo }}</h1>
        <a href="{{ route('componentes.index') }}"
            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-gray-800">
            ← Volver al listado
        </a>

    </div>
        <form action="{{ route('componentes.update', $componente) }}"
            method="POST" enctype="multipart/form-data"
            class="space-y-6 bg-white p-6 rounded shadow">
        @csrf @method('PUT')
        @include('componentes._form')
        <div class="flex justify-end">
            <x-primary-button>Actualizar</x-primary-button>
        </div>
        </form>
    
  </main>
</x-app-layout>

{{-- resources/views/panoramas/create.blade.php --}}
<x-app-layout>

  {{-- 1) Cargamos A-Frame y Alpine en el <head> --}}
  <x-slot name="head">
    <script src="https://aframe.io/releases/1.4.0/aframe.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
      [x-cloak] { display: none!important; }
      .scene-container { width: 100%; height: 400px; margin-top: 1rem; }
    </style>
  </x-slot>

  {{-- 2) Cabecera --}}
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Crear Panorama 360°</h2>
  </x-slot>

  {{-- 3) Formulario + Preview --}}
  <main x-data="panoramaPreview()" class="py-6 max-w-3xl mx-auto px-4 space-y-6">

    {{-- Paso 1: Selección de datos básicos --}}
    <form @submit.prevent class="bg-white p-6 rounded-lg shadow space-y-4">

      {{-- Nombre --}}
      <div>
        <x-input-label for="nombre" :value="__('Nombre')" />
        <x-text-input id="nombre" name="nombre" type="text"
                      class="mt-1 block w-full"
                      x-model="form.nombre"
                      placeholder="Ej. Recorrido Museo"
                      required />
      </div>

      {{-- Componente --}}
      <div>
        <x-input-label for="componente_id" :value="__('Componente asociado')" />
        <select id="componente_id" name="componente_id"
                class="mt-1 block w-full border rounded"
                x-model="form.componente_id" required>
          <option value="">— Selecciona componente —</option>
          @foreach($componentes as $id => $titulo)
            <option value="{{ $id }}">{{ $titulo }}</option>
          @endforeach
        </select>
      </div>

      {{-- Imagen 360° --}}
      <div>
        <x-input-label for="imagen_path" :value="__('Imagen 360° (jpg/png)')" />
        <input id="imagen_path" name="imagen_path" type="file"
               accept="image/jpeg,image/png"
               class="mt-1 block w-full"
               @change="onFileChange"
               required />
      </div>

      {{-- Nota de pasos --}}
      <p class="text-sm text-gray-600 mt-2">
        <strong>Paso a Paso:</strong><br>
        1. Rellena nombre y componente.<br>
        2. Selecciona tu imagen 360°.<br>
        3. La previsualización aparecerá abajo.
      </p>

    </form>

    {{-- Paso 2: Mensaje inicial --}}
    <div x-show="!previewUrl" x-cloak class="bg-yellow-100 border-l-4 border-yellow-500 p-4">
      <p class="font-medium text-yellow-800">
        ● <span class="italic">Paso 3:</span> Selecciona un archivo para ver la vista 360° aquí.
      </p>
    </div>

    {{-- Paso 3: Vista 360° --}}
    <div x-show="previewUrl" x-cloak>
      <h3 class="font-semibold">Previsualización Interactiva</h3>
      <div class="scene-container">
        <a-scene embedded style="width:100%; height:100%;" x-ref="scene">
          <a-sky :src="previewUrl" material="shader: flat" rotation="0 -100 0"></a-sky>
          <a-camera wasd-controls-enabled="false" look-controls="true">
            <a-cursor rayOrigin="mouse" material="color: white; shader: flat"></a-cursor>
          </a-camera>
        </a-scene>
      </div>
    </div>

  </main>

  {{-- 4) Alpine + lógica --}}
  <x-slot name="scripts">
    <script>
      function panoramaPreview() {
        return {
          form: { nombre:'', componente_id:'', imagen_path:null },
          previewUrl: null,

          onFileChange(e) {
            const file = e.target.files[0];
            if (!file) {
              alert('No se ha seleccionado ningún archivo.');
              return;
            }
            // Paso 3: Generando URL local
            alert(`Archivo "${file.name}" seleccionado. Generando vista...`);
            this.form.imagen_path = file;
            this.previewUrl = URL.createObjectURL(file);
          }
        }
      }
    </script>
  </x-slot>

</x-app-layout>

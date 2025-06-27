<x-app-layout>

  {{-- inyecta en <head> --}}
  <x-slot name="head">
    <script src="https://aframe.io/releases/1.4.0/aframe.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important;}</style>
  </x-slot>

  {{-- título de la página --}}
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Crear Panorama</h2>
  </x-slot>

  {{-- cuerpo --}}
  <main x-data="newPanoramaEditor()" class="py-6 max-w-3xl mx-auto px-4 space-y-8">
    {{-- título + volver --}}
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold">Nuevo Panorama 360°</h1>
      <a href="{{ route('panoramas.index') }}"
         class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">← Volver</a>
    </div>

    {{-- alertas --}}
    <x-alert type="success"/>
    <x-alert type="warning"/>
    <x-alert type="error"/>

    {{-- formulario --}}
    <form @submit.prevent="submit()"
          enctype="multipart/form-data"
          class="bg-white p-6 rounded-lg shadow space-y-6">
      @csrf

      {{-- nombre --}}
      <div>
        <x-input-label for="nombre" :value="__('Nombre')" />
        <x-text-input id="nombre" name="nombre" type="text"
                      class="mt-1 block w-full"
                      x-model="form.nombre" required />
        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
      </div>

      {{-- componente asociado --}}
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
        <x-input-error :messages="$errors->get('componente_id')" class="mt-2" />
      </div>

      {{-- imagen 360° --}}
      <div>
        <x-input-label for="imagen_path" :value="__('Imagen 360° (jpg/png)')" />
        <input id="imagen_path" name="imagen_path" type="file"
               accept="image/jpeg,image/png"
               class="mt-1 block w-full"
               @change="onFileChange" required />
        <x-input-error :messages="$errors->get('imagen_path')" class="mt-2" />
      </div>

      {{-- preview + hotspots --}}
      <div x-show="previewUrl" x-cloak class="space-y-4">
        <h3 class="font-medium">Previsualización Interactiva</h3>

        {{-- alternar modo añadir hotspot --}}
        <button type="button"
                @click="adding = !adding"
                :class="adding ? 'bg-red-600' : 'bg-green-600'"
                class="text-white px-3 py-1 rounded">
          <template x-if="!adding">+ Añadir Hotspot</template>
          <template x-if="adding">Cancelar</template>
        </button>

        {{-- visor 360° --}}
        <div class="relative" style="height:400px;">
          <a-scene x-ref="scene" embedded style="height:100%">
            <a-sky :src="previewUrl" rotation="0 -100 0"></a-sky>
            <a-camera wasd-controls-enabled="false" look-controls="true">
              <a-cursor rayOrigin="mouse"
                        material="color: white; shader: flat"></a-cursor>
            </a-camera>
            <template x-for="h in hotspots" :key="h.position">
              <a-image :position="h.position"
                       src="{{ asset('images/hotspot-icon.png') }}"
                       look-at="#camera"
                       scale="0.5 0.5 0.5"></a-image>
            </template>
          </a-scene>
        </div>

        {{-- después de hacer click, elegir elemento --}}
        <div x-show="adding && selectedPos" x-cloak
             class="mt-2 bg-gray-50 p-4 rounded">
          <p class="mb-2">Elige el elemento para el hotspot:</p>
          <select x-model="selectedElemento"
                  class="block w-full border rounded p-2">
            <template x-for="(nombre,id) in elementos" :key="id">
              <option :value="id" x-text="nombre"></option>
            </template>
          </select>
          <div class="mt-3 flex justify-end space-x-2">
            <button type="button" @click="cancelHotspot()"
                    class="px-4 py-2 bg-gray-300 rounded">Cancelar</button>
            <button type="button" @click="addHotspot()"
                    class="px-4 py-2 bg-blue-600 text-white rounded">
              Confirmar
            </button>
          </div>
        </div>
      </div>

      {{-- guardar --}}
      <div class="flex justify-end">
        <x-primary-button>Guardar</x-primary-button>
      </div>
    </form>
  </main>

  {{-- inyecta al final del body --}}
  <x-slot name="scripts">
    <script>
    function newPanoramaEditor() {
      return {
        form: { nombre:'', componente_id:'', imagen_path:null },
        previewUrl: null,
        hotspots: [],
        adding: false,
        selectedPos: null,
        selectedElemento: null,

        // este mapa viene del controlador
        elementosByComponent: @json($elementosByComponent),

        // getter reactivo según componente seleccionado
        get elementos() {
          return this.elementosByComponent[this.form.componente_id] || {};
        },

        onFileChange(e) {
          const file = e.target.files[0];
          if (!file) return;
          this.form.imagen_path = file;
          this.previewUrl      = URL.createObjectURL(file);
          this.$nextTick(() => {
            this.$refs.scene
                .addEventListener('click', this.onSceneClick.bind(this));
          });
        },

        onSceneClick(evt) {
          if (!this.adding) return;
          const inter = evt.detail.intersection;
          if (!inter) return;
          this.selectedPos = [inter.point.x,inter.point.y,inter.point.z]
                                 .map(n=>n.toFixed(2)).join(' ');
        },

        cancelHotspot() {
          this.selectedPos     = null;
          this.selectedElemento= null;
          this.adding          = false;
        },

        addHotspot() {
          if (!this.selectedPos || !this.selectedElemento) {
            return alert('Falta posición o elemento.');
          }
          this.hotspots.push({
            position:    this.selectedPos,
            elemento_id: this.selectedElemento
          });
          this.cancelHotspot();
        },

        async submit() {
          const data = new FormData();
          data.append('nombre',        this.form.nombre);
          data.append('componente_id', this.form.componente_id);
          data.append('imagen_path',   this.form.imagen_path);
          data.append('hotspots',      JSON.stringify(this.hotspots));

          const res = await fetch(
            '{{ route("panoramas.store") }}',
            {
              method: 'POST',
              headers:{ 'X-CSRF-TOKEN':'{{ csrf_token() }}' },
              body: data
            }
          );
          if (res.ok) window.location='{{ route("panoramas.index") }}';
          else          alert('Error al guardar');
        }
      }
    }
    </script>
  </x-slot>

</x-app-layout>

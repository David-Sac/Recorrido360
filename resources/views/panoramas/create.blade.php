{{-- resources/views/panoramas/create.blade.php --}}
<x-app-layout>

  {{-- 1) Inyectamos A-Frame y Alpine en el <head> --}}
  <x-slot name="head">
    <script src="https://aframe.io/releases/1.4.0/aframe.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
      [x-cloak] { display: none !important; }
      .scene-container { height: 400px; position: relative; }
      .loader {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%,-50%);
        background: rgba(0,0,0,0.6);
        color: white;
        padding: .5rem 1rem;
        border-radius: .25rem;
        font-weight: bold;
      }
    </style>
  </x-slot>

  {{-- Cabecera --}}
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Crear Panorama</h2>
  </x-slot>

  {{-- Main con Alpine --}}
  <main
    x-data="newPanoramaEditor()"
    x-init="init()"
    class="py-6 max-w-3xl mx-auto px-4 space-y-8"
  >
    {{-- Título + volver --}}
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold">Nuevo Panorama 360°</h1>
      <a href="{{ route('panoramas.index') }}"
         class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">← Volver</a>
    </div>

    {{-- Alertas --}}
    <x-alert type="success"/>
    <x-alert type="warning"/>
    <x-alert type="error"/>

    {{-- Formulario --}}
    <form @submit.prevent="submit()"
          enctype="multipart/form-data"
          class="bg-white p-6 rounded-lg shadow space-y-6">
      @csrf

      {{-- Nombre --}}
      <div>
        <x-input-label for="nombre" :value="__('Nombre')" />
        <x-text-input id="nombre"
                      name="nombre"
                      type="text"
                      class="mt-1 block w-full"
                      x-model="form.nombre"
                      required />
        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
      </div>

      {{-- Componente --}}
      <div>
        <x-input-label for="componente_id" :value="__('Componente asociado')" />
        <select id="componente_id"
                name="componente_id"
                class="mt-1 block w-full border rounded"
                x-model="form.componente_id"
                required>
          <option value="">— Selecciona componente —</option>
          @foreach($componentes as $id => $titulo)
            <option value="{{ $id }}">{{ $titulo }}</option>
          @endforeach
        </select>
        <x-input-error :messages="$errors->get('componente_id')" class="mt-2" />
      </div>

      {{-- Imagen 360° --}}
      <div>
        <x-input-label for="imagen_path" :value="__('Imagen 360° (jpg/png)')" />
        <input id="imagen_path"
               name="imagen_path"
               type="file"
               accept="image/jpeg,image/png"
               class="mt-1 block w-full"
               @change="onFileChange"
               required />
        <x-input-error :messages="$errors->get('imagen_path')" class="mt-2" />
      </div>

      {{-- Visor + loader --}}
      <div class="space-y-4">
        <h3 class="font-medium">Previsualización Interactiva</h3>
        <button type="button"
                @click="adding = !adding"
                :class="adding ? 'bg-red-600' : 'bg-green-600'"
                class="text-white px-3 py-1 rounded">
          <template x-if="!adding">+ Añadir Hotspot</template>
          <template x-if="adding">Cancelar</template>
        </button>

        <div class="scene-container">
          {{-- loader --}}
          <div x-show="loading" class="loader">Cargando…</div>

          <a-scene x-ref="scene" embedded style="height:100%;">
            {{-- NOTA: no usamos :src aquí --}}
            <a-sky
              x-ref="sky"
              material="shader: flat; src: url({{ 'https://cdn.aframe.io/360-image-gallery-boilerplate/img/sechelt.jpg' }})"
              rotation="0 -100 0">
            </a-sky>
            <a-camera wasd-controls-enabled="false" look-controls="true">
              <a-cursor rayOrigin="mouse" material="color: white; shader: flat"></a-cursor>
            </a-camera>
            <template x-for="h in hotspots" :key="h.position">
              <a-image :position="h.position"
                       src="{{ asset('images/hotspot-icon.png') }}"
                       look-at="#camera" scale="0.5 0.5 0.5"></a-image>
            </template>
          </a-scene>
        </div>

        {{-- Selector de elemento --}}
        <div x-show="adding && selectedPos" x-cloak class="mt-2 bg-gray-50 p-4 rounded">
          <p class="mb-2">Elige el elemento para el hotspot:</p>
          <select x-model="selectedElemento" class="block w-full border rounded p-2">
            <option value="">— Selecciona elemento —</option>
            @foreach($elementos as $id => $nombre)
              <option value="{{ $id }}">{{ $nombre }}</option>
            @endforeach
          </select>
          <div class="mt-3 flex justify-end space-x-2">
            <button type="button" @click="cancelHotspot()" class="px-4 py-2 bg-gray-300 rounded">Cancelar</button>
            <button type="button" @click="addHotspot()"   class="px-4 py-2 bg-blue-600 text-white rounded">Confirmar</button>
          </div>
        </div>
      </div>

      {{-- Guardar --}}
      <div class="flex justify-end">
        <x-primary-button>Guardar</x-primary-button>
      </div>
    </form>
  </main>

  {{-- 2) Scripts al final --}}
  <x-slot name="scripts">
    <script>
      function newPanoramaEditor() {
        return {
          form: {
            nombre: '',
            componente_id: '',
            imagen_path: null
          },
          defaultUrl: 'https://cdn.aframe.io/360-image-gallery-boilerplate/img/sechelt.jpg',
          hotspots: [],
          adding: false,
          selectedPos: null,
          selectedElemento: null,
          loading: true,

          init() {
            // 1) Listener para ocultar loader cuando la textura termine de cargar
            this.$refs.sky.addEventListener('materialtextureloaded', () => {
              this.loading = false;
            });
            // 2) Listener del click para hotspots
            this.$refs.scene.addEventListener('click', this.onSceneClick.bind(this));
          },

          onFileChange(e) {
            const file = e.target.files[0];
            if (!file) return;
            this.loading = true;
            this.form.imagen_path = file;
            const url = URL.createObjectURL(file);

            // 3) ¡Forzamos a A-Frame a recargar la textura!
            this.$refs.sky.setAttribute('material', `shader: flat; src: url(${url})`);

            // 4) Seguimos con el mismo listener para acabar el loader
            this.$refs.sky.addEventListener('materialtextureloaded', () => {
              this.loading = false;
            });
          },

          onSceneClick(evt) {
            if (!this.adding) return;
            const inter = evt.detail.intersection;
            if (!inter) return;
            this.selectedPos = [inter.point.x, inter.point.y, inter.point.z]
                                  .map(n => n.toFixed(2)).join(' ');
          },

          cancelHotspot() {
            this.selectedPos = null;
            this.selectedElemento = null;
            this.adding = false;
          },

          addHotspot() {
            if (!this.selectedPos || !this.selectedElemento) {
              return alert('Falta posición o elemento.');
            }
            this.hotspots.push({
              position: this.selectedPos,
              elemento_id: this.selectedElemento
            });
            this.cancelHotspot();
          },

          async submit() {
            const data = new FormData();
            data.append('nombre',         this.form.nombre);
            data.append('componente_id',  this.form.componente_id);
            data.append('imagen_path',    this.form.imagen_path);
            data.append('hotspots',       JSON.stringify(this.hotspots));

            const res = await fetch('{{ route("panoramas.store") }}', {
              method: 'POST',
              headers:{ 'X-CSRF-TOKEN':'{{ csrf_token() }}' },
              body: data
            });

            if (res.ok) {
              window.location = '{{ route("panoramas.index") }}';
            } else {
              alert('Error al guardar panorama');
            }
          }
        }
      }
    </script>
  </x-slot>

</x-app-layout>

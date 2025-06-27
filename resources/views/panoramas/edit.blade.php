<!-- resources/views/panoramas/edit.blade.php -->
<x-app-layout>
  <x-slot name="head">
    <script src="https://aframe.io/releases/1.4.0/aframe.min.js"></script>
  </x-slot>

  <x-slot name="header">
    <h2 class="font-semibold text-xl">Editar Panorama</h2>
  </x-slot>

  <main x-data="panoramaEditor()" x-init="init()" class="py-6 max-w-4xl mx-auto px-4 space-y-8">
    {{-- Formulario básico --}}
    <div class="bg-white p-6 rounded-lg shadow space-y-6">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Editar: {{ $panorama->nombre }}</h1>
        <a href="{{ route('panoramas.index') }}" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
          ← Volver
        </a>
      </div>
      <form action="{{ route('panoramas.update', $panorama) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf @method('PUT')
        @include('panoramas._form')
        <div class="flex justify-end">
          <x-primary-button>Actualizar</x-primary-button>
        </div>
      </form>
    </div>

    {{-- Hotspots interactivos con modo añadir --}}
    <section class="bg-white p-6 rounded-lg shadow relative">
      <h3 class="text-xl font-semibold mb-4">Hotspots Interactivos</h3>

      {{-- Botón + para modo añadir --}}
      <button @click="addingMode = !addingMode"
              :class="addingMode ? 'bg-red-600' : 'bg-green-600'"
              class="absolute top-6 right-6 text-white px-3 py-1 rounded shadow-lg">
        <template x-if="!addingMode">+ Añadir Hotspot</template>
        <template x-if="addingMode">Cancelar</template>
      </button>

      {{-- Visor 360° --}}
      <div class="relative" style="height:400px;">
        <a-scene x-ref="scene" embedded style="height:100%;">
          <a-sky src="{{ asset('storage/'.$panorama->imagen_path) }}" rotation="0 -100 0"></a-sky>
          <a-camera wasd-controls-enabled="false" look-controls="true">
            <a-cursor rayOrigin="mouse" material="color: white; shader: flat"></a-cursor>
          </a-camera>
          <template x-for="h in hotspots" :key="h.position">
            <a-image :position="h.position"
                     src="{{ asset('images/hotspot-icon.png') }}"
                     look-at="#camera" scale="0.5 0.5 0.5"></a-image>
          </template>
        </a-scene>
        <p class="mt-2 text-sm text-gray-500">Arrastra para mirar alrededor. Click en + y luego en la imagen para crear.</p>
      </div>

      {{-- Selector de elemento tras click --}}
      <div x-show="showSelector" x-cloak class="mt-4 bg-gray-50 p-4 rounded">
        <p class="mb-2">Elige el elemento para el nuevo hotspot:</p>
        <select x-model="selectedElement" class="block w-full border rounded p-2">
          <option value="">— Selecciona elemento —</option>
          @foreach($panorama->componente->elementos as $e)
            <option value="{{ $e->id }}">{{ $e->nombre }}</option>
          @endforeach
        </select>
        <div class="mt-3 flex justify-end space-x-2">
          <button @click="cancelAdd()" class="px-4 py-2 bg-gray-300 rounded">Cancelar</button>
          <button @click="confirmAdd()" class="px-4 py-2 bg-blue-600 text-white rounded">Guardar Hotspot</button>
        </div>
      </div>
    </section>
  </main>

  <script>
    function panoramaEditor() {
      return {
        addingMode: false,
        showSelector: false,
        selectedElement: null,
        newPos: null,
        hotspots: @json($panorama->hotspots->map(fn($h) => ['position' => $h->posicion])),
        init() {
          this.$nextTick(() => {
            this.$refs.scene.addEventListener('click', this.handleClick.bind(this));
          });
        },
        handleClick(evt) {
          if (!this.addingMode) return;
          const inter = evt.detail.intersection;
          if (!inter) return;
          // Capturar posición y mostrar selector
          const p = inter.point;
          this.newPos = [p.x, p.y, p.z].map(n => n.toFixed(2)).join(' ');
          this.showSelector = true;
        },
        cancelAdd() {
          this.showSelector = false;
          this.addingMode = false;
          this.selectedElement = null;
          this.newPos = null;
        },
        confirmAdd() {
          if (!this.selectedElement) {
            return alert('Selecciona un elemento antes de guardar.');
          }
          // UI
          this.hotspots.push({ position: this.newPos });
          // API
          fetch(`{{ url('panoramas/' . $panorama->id . '/hotspots') }}`, {
            method: 'POST',
            headers: { 'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}' },
            body: JSON.stringify({ elemento_id: this.selectedElement, posicion: this.newPos })
          }).then(res => res.json()).then(json => {
            if (!json.success) alert('Error guardando hotspot');
            this.cancelAdd();
          });
        }
      }
    }
  </script>
</x-app-layout>
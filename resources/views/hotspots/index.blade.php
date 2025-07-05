{{-- resources/views/hotspots/index.blade.php --}}
<x-app-layout>

  {{-- 1) Inyectamos A-Frame + Alpine --}}
  <x-slot name="head">
    <script src="https://aframe.io/releases/1.4.0/aframe.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
      .scene-container {
        width: 100%;
        height: 600px;
        position: relative;
      }
    </style>
  </x-slot>

  {{-- 2) Título --}}
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Hotspots de “{{ $panorama->nombre }}”</h2>
  </x-slot>

  @php
    // Preparamos el JSON para inyectar sin romper Blade
    $hotspotsJson = $hotspots
      ->map(fn($h) => [
        'id'              => $h->id,
        'posicion'        => $h->posicion,
        'elemento_nombre' => $h->elemento->nombre,
      ])
      ->values()
      ->toJson();
  @endphp

  {{-- 3) Contenedor principal --}}
  <main
    id="hotspot-root"
    data-hotspots='{{ $hotspotsJson }}'
    x-data="hotspotManager()"
    x-init="init()"
    class="py-6 max-w-5xl mx-auto space-y-6"
  >

    {{-- Coordenadas 3D --}}
    <div class="text-gray-700">
      <strong>Coordenadas 3D:</strong>
      <span x-text="hover || '—, —, —'"></span>
    </div>

    {{-- Botones Añadir / Cancelar --}}
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-bold">Visor 360°</h1>
      <div>
        <button @click="startAdd()"
                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded mr-2">
          ＋ Añadir
        </button>
        <button @click="cancelAdd()"
                class="px-4 py-2 bg-gray-400 hover:bg-gray-500 text-white rounded">
          ✕ Cancelar
        </button>
      </div>
    </div>

    {{-- Escena 360° --}}
    <div class="scene-container rounded-lg overflow-hidden bg-black">
      <a-scene x-ref="scene" embedded style="width:100%;height:100%;">

        {{-- El cielo con id para raycaster --}}
        <a-sky id="sky"
               src="{{ asset('storage/'.$panorama->imagen_path) }}"
               rotation="0 -100 0">
        </a-sky>

        {{-- Cámara + cursor configurado para raycaster --}}
        <a-camera wasd-controls-enabled="false" look-controls="true">
          <a-entity
            cursor="rayOrigin: mouse"
            raycaster="objects: #sky"
            @raycaster-intersection="onHover($event)"
            @click="onClick($event)"
            material="color: white; shader: flat">
          </a-entity>
        </a-camera>

        {{-- Hotspots existentes: círculos naranja ligeramente desplazados --}}
        <template x-for="h in hotspots" :key="h.id">
          <a-circle
            :position="offset(h.posicion, 0.99)"
            radius="1"
            side="double"
            material="color: orange; opacity: 0.9"
            look-at="#camera">
          </a-circle>
        </template>

        {{-- Ícono clicable para borrar --}}
        <template x-for="h in hotspots" :key="h.id">
          <a-image
            :position="h.posicion"
            src="{{ asset('images/hotspot-icon.png') }}"
            look-at="#camera"
            scale="0.5 0.5 0.5"
            @click.stop="deleteHotspot(h.id)">
          </a-image>
        </template>

        {{-- Punto provisional + icono verde confirmar --}}
        <template x-if="adding && newPos">
          <a-circle
            :position="offset(newPos, 0.99)"
            radius="1"
            side="double"
            material="color: lime; opacity: 0.9"
            look-at="#camera">
          </a-circle>
          <a-image
            :position="newPos"
            src="{{ asset('images/hotspot-icon-add.png') }}"
            look-at="#camera"
            scale="0.5 0.5 0.5"
            @click.stop="confirmAdd()">
          </a-image>
        </template>

      </a-scene>
    </div>

    {{-- Tabla de Hotspots --}}
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Elemento</th>
            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Posición</th>
            <th class="px-6 py-3"></th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <template x-for="h in hotspots" :key="h.id">
            <tr>
              <td class="px-6 py-4 whitespace-nowrap" x-text="h.elemento_nombre"></td>
              <td class="px-6 py-4 whitespace-nowrap" x-text="h.posicion"></td>
              <td class="px-6 py-4 text-right">
                <button @click="deleteHotspot(h.id)"
                        class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded text-sm">
                  −
                </button>
              </td>
            </tr>
          </template>
          <template x-if="hotspots.length === 0">
            <tr>
              <td colspan="3" class="px-6 py-4 text-center text-gray-500">No hay hotspots aún.</td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

  </main>

  {{-- 4) Lógica Alpine / JavaScript --}}
  <x-slot name="scripts">
    <script>
      function hotspotManager() {
        return {
          hotspots: JSON.parse(document.getElementById('hotspot-root').dataset.hotspots),
          adding: false,
          newPos: null,
          hover: null,

          init() {
            // Nada aquí; usamos @raycaster-intersection en el cursor
          },

          // Raycaster-intersection del cursor: actualiza hover
          onHover(event) {
            const p = event.detail.intersections[0].point;
            this.hover = `${p.x.toFixed(2)}, ${p.y.toFixed(2)}, ${p.z.toFixed(2)}`;
          },

          // Click sobre el sky (evento del cursor): define newPos si estamos en modo añadir
          onClick(event) {
            if (!this.adding) return;
            const inter = event.detail.intersections[0];
            if (!inter) return;
            const p = inter.point;
            this.newPos = `${p.x.toFixed(2)} ${p.y.toFixed(2)} ${p.z.toFixed(2)}`;
          },

          // Desplaza ligeramente la posición hacia el centro
          offset(posString, factor) {
            const [x,y,z] = posString.split(' ').map(Number);
            return `${x*factor} ${y*factor} ${z*factor}`;
          },

          startAdd() {
            this.adding = true;
            this.newPos = null;
          },
          cancelAdd() {
            this.adding = false;
            this.newPos = null;
          },

          confirmAdd() {
            if (!this.newPos) return;
            fetch(`{{ url("panoramas/{$panorama->id}/hotspots") }}`, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: JSON.stringify({
                elemento_id: null,      // Ajusta aquí si quieres seleccionar elemento
                posicion:    this.newPos
              })
            }).then(() => location.reload());
          },

          deleteHotspot(id) {
            if (!confirm('¿Eliminar hotspot?')) return;
            fetch(`/hotspots/${id}`, {
              method: 'DELETE',
              headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(() => location.reload());
          }
        }
      }
    </script>
  </x-slot>

</x-app-layout>

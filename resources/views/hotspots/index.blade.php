{{-- resources/views/hotspots/index.blade.php --}}
<x-app-layout>

  {{-- 1) Inyectamos A-Frame + Alpine --}}
  <x-slot name="head">
    <script src="https://aframe.io/releases/1.4.0/aframe.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  </x-slot>

  {{-- 2) Título --}}
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Hotspots de “{{ $panorama->nombre }}”</h2>
  </x-slot>

  @php
    // Serializamos los hotspots a JSON sin romper Blade
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
    class="py-6 max-w-4xl mx-auto px-4 space-y-6"
  >

    {{-- Coordenadas al pasar el ratón --}}
    <div class="text-sm text-gray-600">
      Coordenadas 3D: <span x-text="hover || '—, —, —'"></span>
    </div>

    {{-- Botones Añadir / Cancelar --}}
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold">Visor 360°</h1>
      <div>
        <button @click="startAdd()"
                class="px-3 py-1 bg-green-600 text-white rounded mr-2">＋ Añadir</button>
        <button @click="cancelAdd()"
                class="px-3 py-1 bg-gray-400 text-white rounded">✕ Cancelar</button>
      </div>
    </div>

    {{-- Escena 360° --}}
    <div class="relative h-96 bg-black rounded-lg overflow-hidden">
      <a-scene x-ref="scene" embedded style="height:100%;">
        {{-- Cielo --}}
        <a-sky src="{{ asset('storage/'.$panorama->imagen_path) }}"
               rotation="0 -100 0"></a-sky>
        {{-- Cámara + cursor --}}
        <a-camera wasd-controls-enabled="false" look-controls="true">
          <a-cursor rayOrigin="mouse"
                    material="color: white; shader: flat"></a-cursor>
        </a-camera>

        {{-- Marcadores rojos --}}
        <template x-for="h in hotspots" :key="h.id">
          <a-entity
            :position="h.posicion"
            geometry="primitive: sphere; radius: 0.15"
            material="color: red; opacity: 0.8"
            look-at="#camera">
          </a-entity>
        </template>

        {{-- Iconos clicables para eliminar --}}
        <template x-for="h in hotspots" :key="h.id">
          <a-image
            :position="h.posicion"
            src="{{ asset('images/hotspot-icon.png') }}"
            look-at="#camera"
            scale="0.5 0.5 0.5"
            @click.stop="deleteHotspot(h.id)">
          </a-image>
        </template>

        {{-- Punto provisional + confirmar --}}
        <template x-if="adding && newPos">
          <a-entity
            :position="newPos"
            geometry="primitive: sphere; radius: 0.15"
            material="color: lime; opacity: 0.8"
            look-at="#camera">
          </a-entity>
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
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <button @click="deleteHotspot(h.id)"
                        class="px-2 py-1 bg-red-500 text-white rounded text-sm">−</button>
              </td>
            </tr>
          </template>
          <template x-if="hotspots.length === 0">
            <tr>
              <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                No hay hotspots aún.
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

  </main>

  {{-- 4) Script de gestión --}}
  <x-slot name="scripts">
    <script>
      function hotspotManager() {
        return {
          hotspots: JSON.parse(document.getElementById('hotspot-root').dataset.hotspots),
          adding: false,
          newPos: null,
          hover: null,

          init() {
            const sceneEl = this.$refs.scene;
            // Al cargar la escena, vinculamos el mousemove sobre el canvas
            sceneEl.addEventListener('loaded', () => {
              const canvas = sceneEl.canvas;
              canvas.addEventListener('mousemove', this.onMouseMove.bind(this));
            });
            // Click para colocar hotspots
            sceneEl.addEventListener('click', e => {
              if (!this.adding) return;
              const inter = e.detail.intersection;
              if (!inter) return;
              this.newPos = [inter.point.x, inter.point.y, inter.point.z]
                .map(n => n.toFixed(2)).join(' ');
            });
          },

          onMouseMove(evt) {
            const sceneEl = this.$refs.scene;
            const canvas  = sceneEl.canvas;
            const rect    = canvas.getBoundingClientRect();
            // Coordenadas NDC
            const x_ndc = ((evt.clientX - rect.left) / rect.width) * 2 - 1;
            const y_ndc = -((evt.clientY - rect.top ) / rect.height) * 2 + 1;
            // Raycaster Three.js
            const mouse    = new THREE.Vector2(x_ndc, y_ndc);
            const camera   = sceneEl.camera.el.getObject3D('camera');
            const raycaster= new THREE.Raycaster();
            raycaster.setFromCamera(mouse, camera);
            const skyObj   = sceneEl.querySelector('a-sky').object3D;
            const inters   = raycaster.intersectObject(skyObj, true);
            if (inters.length) {
              const p = inters[0].point;
              this.hover = `${p.x.toFixed(2)}, ${p.y.toFixed(2)}, ${p.z.toFixed(2)}`;
            } else {
              this.hover = null;
            }
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
              body: JSON.stringify({ elemento_id: null, posicion: this.newPos })
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

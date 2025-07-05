{{-- resources/views/hotspots/index.blade.php --}}
<x-app-layout>

  <x-slot name="head">
    <script src="https://aframe.io/releases/1.4.0/aframe.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
      .scene-container { width:100%; height:600px; position:relative; }
    </style>
  </x-slot>

  <x-slot name="header">
    <h2 class="font-semibold text-xl">Hotspots de “{{ $panorama->nombre }}”</h2>
  </x-slot>

  @php
    $hotspotsJson = $hotspots
      ->map(fn($h) => [
        'id'              => $h->id,
        'posicion'        => $h->posicion,
        'elemento_id'     => $h->elemento->id,
        'elemento_nombre' => $h->elemento->nombre,
      ])
      ->values()
      ->toJson();

    $elementosJson = \App\Models\Elemento::where('componente_id',$panorama->componente_id)
      ->get()
      ->map(fn($e)=>['id'=>$e->id,'nombre'=>$e->nombre])
      ->toJson();
  @endphp

  <main
    id="hotspot-root"
    data-hotspots='{{ $hotspotsJson }}'
    data-elementos='{{ $elementosJson }}'
    x-data="hotspotManager()"
    x-init="init()"
    class="py-6 max-w-4xl mx-auto px-4 space-y-6"
  >

    <div class="text-sm text-gray-600">
      Coordenadas 3D: <span x-text="hover || '—, —, —'"></span>
    </div>

    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold">Visor 360°</h1>
      <div>
        <button @click="startAdd()"
                class="px-3 py-1 bg-green-600 text-white rounded mr-2">＋ Añadir</button>
        <button @click="cancelAdd()"
                class="px-3 py-1 bg-gray-400 text-white rounded">✕ Cancelar</button>
      </div>
    </div>

    <div class="scene-container rounded-lg overflow-hidden bg-black">
      <a-scene x-ref="scene" embedded>

        <a-assets>
          <img id="pano" src="{{ asset('storage/'.$panorama->imagen_path) }}" />
        </a-assets>

        <a-sky src="#pano" rotation="0 -100 0"></a-sky>

        <a-camera id="camera" wasd-controls-enabled="false" look-controls="true">
          <a-cursor rayOrigin="mouse" material="color:white;shader:flat" raycaster= "objects: a-sky"></a-cursor>
        </a-camera>

        <template x-for="h in hotspots" :key="h.id">
          <a-entity :position="h.posicion.split(' ').map(n => parseFloat(n))">
            <a-sphere radius="0.15"
                      color="red"
                      opacity="0.8"
                      look-at="#camera"
                      @click.stop="deleteHotspot(h.id)">
            </a-sphere>
            <a-text :value="h.posicion"
                    align="center"
                    color="white"
                    position="0 0.25 0"
                    look-at="#camera">
            </a-text>
          </a-entity>
        </template>

        <template x-if="adding && newPos">
          <a-entity :position="newPos.split(' ').map(n => parseFloat(n))">
            <a-sphere radius="0.15"
                      color="lime"
                      opacity="0.8"
                      look-at="#camera">
            </a-sphere>
            <a-text :value="newPos"
                    align="center"
                    color="lime"
                    position="0 0.25 0"
                    look-at="#camera">
            </a-text>
            <a-image src="{{ asset('images/hotspot-icon-add.png') }}"
                     scale="0.5 0.5 0.5"
                     @click.stop="confirmAdd()"
                     look-at="#camera">
            </a-image>
          </a-entity>
        </template>

      </a-scene>
    </div>

    <div x-show="adding && newPos" class="mt-4 p-4 bg-gray-50 rounded shadow">
      <div class="mb-2 text-sm">
        Nueva posición: <strong x-text="newPos"></strong>
      </div>
      <label class="block mb-2 text-sm font-medium">Selecciona elemento:</label>
      <select x-model="selectedElemento" class="w-full border rounded p-2 mb-3">
        <option value="">— Elige elemento —</option>
        <template x-for="e in elementos" :key="e.id">
          <option :value="e.id" x-text="e.nombre"></option>
        </template>
      </select>
      <button
        @click="confirmAdd()"
        class="px-4 py-2 bg-blue-600 text-white rounded disabled:opacity-50"
        :disabled="!selectedElemento"
      >Guardar Hotspot</button>
    </div>

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

  <x-slot name="scripts">
    <script>
      function hotspotManager() {
        return {
          hotspots: JSON.parse(document.getElementById('hotspot-root').dataset.hotspots),
          elementos: JSON.parse(document.getElementById('hotspot-root').dataset.elementos),
          adding: false,
          newPos: null,
          selectedElemento: null,
          hover: null,

          init() {
          sceneEl.addEventListener('click', e => {
            if (!this.adding) return
            const inter = e.detail.intersection
            if (!inter) return
            const sky = sceneEl.querySelector('a-sky')
            const radius = sky.getAttribute('radius') || 500     // radio del cielo
            const p = inter.point.clone().normalize().multiplyScalar(radius - 5) // dentro del cielo
            this.newPos = `${p.x.toFixed(2)} ${p.y.toFixed(2)} ${p.z.toFixed(2)}`
          });
          },

          onMouseMove(evt) {
            const sceneEl = this.$refs.scene;
            const rect    = sceneEl.canvas.getBoundingClientRect();
            const x_ndc   = ((evt.clientX - rect.left) / rect.width) * 2 - 1;
            const y_ndc   = -((evt.clientY - rect.top) / rect.height) * 2 + 1;
            const mouse   = new AFRAME.THREE.Vector2(x_ndc, y_ndc);
            const camera  = sceneEl.camera.el.getObject3D('camera');
            const raycaster = new AFRAME.THREE.Raycaster();
            raycaster.setFromCamera(mouse, camera);
            const skyObj = sceneEl.querySelector('a-sky').object3D;
            const inters = raycaster.intersectObject(skyObj, true);
            this.hover = inters.length
              ? `${inters[0].point.x.toFixed(2)}, ${inters[0].point.y.toFixed(2)}, ${inters[0].point.z.toFixed(2)}`
              : null;
          },

          startAdd() {
            this.adding = true;
            this.newPos = null;
            this.selectedElemento = null;
          },
          cancelAdd() {
            this.adding = false;
            this.newPos = null;
            this.selectedElemento = null;
          },

          confirmAdd() {
            if (!this.newPos || !this.selectedElemento) return;
            fetch(`{{ url("panoramas/{$panorama->id}/hotspots") }}`, {
              method: 'POST',
              headers: {
                'Content-Type':'application/json',
                'X-CSRF-TOKEN':'{{ csrf_token() }}'
              },
              body: JSON.stringify({
                elemento_id: this.selectedElemento,
                posicion: this.newPos
              })
            })
            .then(res => res.ok ? location.reload() : Promise.reject());
          },

          deleteHotspot(id) {
            if (!confirm('¿Eliminar hotspot?')) return;
            fetch(`/hotspots/${id}`, {
              method:'DELETE',
              headers:{ 'X-CSRF-TOKEN':'{{ csrf_token() }}' }
            })
            .then(res => res.ok ? location.reload() : Promise.reject());
          }
        }
      }
    </script>
  </x-slot>

</x-app-layout>

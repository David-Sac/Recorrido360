{{-- resources/views/hotspots/index.blade.php --}}
<x-app-layout>

  <x-slot name="head">
    <script src="https://aframe.io/releases/1.4.0/aframe.min.js"></script>
    <script src="https://unpkg.com/aframe-look-at-component/dist/aframe-look-at-component.min.js"></script>
    <script src="https://unpkg.com/aframe-event-set-component/dist/aframe-event-set-component.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
      .scene-container { width:100%; height:600px; position:relative; }
    </style>
    <script>
      AFRAME.registerComponent('toggle-color', {
        schema: {
          color1: { type: 'string', default: 'red' },
          color2: { type: 'string', default: 'green' }
        },
        init: function () {
          this.toggled = false;
          this.el.addEventListener('click', () => {
            this.toggled = !this.toggled;
            this.el.setAttribute('color', this.toggled ? this.data.color2 : this.data.color1);
          });
        }
      });
    </script>
  </x-slot>

  <x-slot name="header">
    <h2 class="text-xl font-semibold">Hotspots de “{{ $panorama->nombre }}”</h2>
  </x-slot>

  @php
    // Serializamos los hotspots y elementos a JSON
    $hotspotsJson = $hotspots->map(fn($h) => [
      'id'              => $h->id,
      'posicion'        => $h->posicion,      // "x y z"
      'elemento_id'     => $h->elemento->id,
      'elemento_nombre' => $h->elemento->nombre,
    ])->values()->toJson();

    $elementosJson = \App\Models\Elemento::where('componente_id', $panorama->componente_id)
      ->get()
      ->map(fn($e) => ['id'=>$e->id,'nombre'=>$e->nombre])
      ->toJson();
  @endphp

  <main
    id="hotspot-root"
    data-hotspots='{{ $hotspotsJson }}'
    data-elementos='{{ $elementosJson }}'
    x-data="hotspotManager()"
    x-init="init()"
    class="max-w-4xl px-4 py-6 mx-auto space-y-6"
  >

    <div class="text-sm text-gray-600">
      Coordenadas 3D: <span x-text="hover || '—, —, —'"></span>
    </div>

    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold">Visor 360°</h1>
      <div>
        <button @click="startAdd()"
                class="px-3 py-1 mr-2 text-white bg-green-600 rounded">＋ Añadir</button>
        <button @click="cancelAdd()"
                class="px-3 py-1 text-white bg-gray-400 rounded">✕ Cancelar</button>
      </div>
    </div>

    <div class="overflow-hidden bg-black rounded-lg scene-container">
      <a-scene x-ref="scene" embedded>
        <a-assets>
          <img id="pano" src="{{ asset('storage/'.$panorama->imagen_path) }}" />
        </a-assets>

        <!-- Domo reducido para debugging -->
        <a-entity id="sky"
                  geometry="primitive: sphere; radius: 100; segmentsWidth: 64; segmentsHeight: 64"
                  material="shader: flat; side: back; src: #pano"
                  rotation="0 -100 0"
                  scale="-1 1 1">
        </a-entity>

        <a-entity id="camera" camera look-controls position="0 1.6 0">
          <a-entity
            cursor="fuse: false"
            raycaster="objects: .clickable"
            position="0 0 -1"
            geometry="primitive: ring; radiusInner: 0.02; radiusOuter: 0.03"
            material="color: white; shader: flat">
          </a-entity>
        </a-entity>

        <!-- Hotspots existentes -->
        <template x-for="h in hotspots" :key="h.id">
          <a-circle
            class="clickable"
            :position="h.posArr.join(' ')"
            radius="5"
            color="#454545"+
            transparent="true"
            opacity="0.8"
            look-at="#camera"
            event-set__enter="_event: mouseenter; scale: 1.3 1.3 1.3"
            event-set__leave="_event: mouseleave; scale: 1 1 1"
            toggle-color="color1: red; color2: green">
          </a-circle>
        </template>

        <!-- Esfera verde dinámica al añadir -->
        <template x-if="adding && newPosArr">
          <a-circle
            :position="newPosArr.join(' ')"
            radius="5"
            color="#454545"+
            transparent="true"
            opacity="0.8"
            look-at="#camera"
            event-set__enter="_event: mouseenter; scale: 1.3 1.3 1.3"
            event-set__leave="_event: mouseleave; scale: 1 1 1"
            toggle-color="color1: lime; color2: yellow"
            @click.stop="confirmAdd()">
          </a-circle>
        </template>

      </a-scene>
    </div>

    <!-- Formulario de nuevo hotspot -->
    <div x-show="adding && newPos" class="p-4 mt-4 rounded shadow bg-gray-50">
      <div class="mb-2 text-sm">Nueva posición: <strong x-text="newPos"></strong></div>
      <label class="block mb-2 text-sm font-medium">Selecciona elemento:</label>
      <select x-model="selectedElemento" class="w-full p-2 mb-3 border rounded">
        <option value="">— Elige elemento —</option>
        <template x-for="e in elementos" :key="e.id">
          <option :value="e.id" x-text="e.nombre"></option>
        </template>
      </select>
      <button @click="confirmAdd()"
              class="px-4 py-2 text-white bg-blue-600 rounded disabled:opacity-50"
              :disabled="!selectedElemento">Guardar Hotspot</button>
    </div>

    <!-- Tabla de hotspots -->
    <div class="overflow-hidden bg-white rounded-lg shadow">
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
                        class="px-2 py-1 text-sm text-white bg-red-500 rounded">−</button>
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

  <x-slot name="scripts">
    <script>
      function hotspotManager() {
        return {
          hotspots: JSON.parse(document.getElementById('hotspot-root').dataset.hotspots)
            .map(h => {
              const [x,y,z] = h.posicion.split(' ').map(Number);
              return { ...h, posArr: [ x, y, z ] };
            }),
          elementos: JSON.parse(document.getElementById('hotspot-root').dataset.elementos),

          adding: false,
          newPos: null,
          newPosArr: null,
          selectedElemento: null,
          hover: null,

          init() {
            console.log('[hotspot] init() fired');
            const sceneEl = this.$refs.scene;
            sceneEl.addEventListener('loaded', () => {
              sceneEl.canvas.addEventListener('mousemove', this.onMouseMove.bind(this));
            });
            sceneEl.addEventListener('click', this.onClick.bind(this));
          },

          startAdd() {
            console.log('[hotspot] startAdd() called');
            this.adding = true;
            this.newPos = this.newPosArr = this.selectedElemento = null;
          },

          onClick(evt) {
            if (!this.adding) return;
            console.log('[hotspot] click event, adding=', this.adding);
            
            // Convertir posición de ratón a coordenadas
            const rect = this.$refs.scene.canvas.getBoundingClientRect();
            const x_ndc = ((evt.clientX - rect.left) / rect.width) * 2 - 1;
            const y_ndc = -((evt.clientY - rect.top) / rect.height) * 2 + 1;
            const mouse = new AFRAME.THREE.Vector2(x_ndc, y_ndc);

            // Crea rayo para llegar al sky
            const camera = this.$refs.scene.camera.el.getObject3D('camera');
            const raycaster = new AFRAME.THREE.Raycaster();
            raycaster.setFromCamera(mouse, camera);

            // intersecta con el domo 360
            const skyObj = this.$refs.scene.querySelector('#sky').object3D;
            const intersects = raycaster.intersectObject(skyObj, true);
            console.log('[hotspot] intersects:', intersects);
            
            // Si intersecta toma el punto 3D
            if (intersects.length > 0) {
              const p = intersects[0].point;
              console.log('[hotspot] point:', p);

              // OFFSET: colocamos la esfera ligeramente hacia dentro del domo
              const eps = 0.98;
              this.newPosArr = [
                (p.x * eps).toFixed(2),
                (p.y * eps).toFixed(2),
                (p.z * eps).toFixed(2)
              ].map(Number);
              this.newPos = this.newPosArr.join(' ');
            }
          },

          onMouseMove(evt) {
            const rect = this.$refs.scene.canvas.getBoundingClientRect();
            const x_ndc = ((evt.clientX - rect.left) / rect.width) * 2 - 1;
            const y_ndc = -((evt.clientY - rect.top) / rect.height) * 2 + 1;
            const mouse = new AFRAME.THREE.Vector2(x_ndc, y_ndc);
            const camera = this.$refs.scene.camera.el.getObject3D('camera');
            const ray = new AFRAME.THREE.Raycaster(); ray.setFromCamera(mouse, camera);
            const skyObj = this.$refs.scene.querySelector('#sky').object3D;
            const inters = ray.intersectObject(skyObj, true);
            this.hover = inters.length
              ? `${inters[0].point.x.toFixed(2)}, ${inters[0].point.y.toFixed(2)}, ${inters[0].point.z.toFixed(2)}`
              : null;
          },

          cancelAdd() {
            this.adding = false;
            this.newPos = this.newPosArr = this.selectedElemento = null;
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

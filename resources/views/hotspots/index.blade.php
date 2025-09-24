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

<main
  x-data='hotspotManager({
    hotspots: @json($hotspots),
    elementos: @json($elementos),
    postUrl: @json(route("panoramas.hotspots.store", $panorama)),
    // OJO: como usas shallow(), el DELETE es /hotspots/{id}
    deleteUrlBase: @json(url("/hotspots"))
  })'
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
            :color="h.color ? h.color : '#454545'"
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
            color="#454545"
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
    function hotspotManager(props) {
      return {
        // ✅ usa las p rops correctas
        hotspots: (props.hotspots || []).map(h => ({
          ...h,
          // por si llega sin posArr, lo derivamos de 'posicion'
          posArr: Array.isArray(h.posArr) && h.posArr.length === 3
            ? h.posArr.map(Number)
            : String(h.posicion || '0 0 0').split(' ').map(Number)
        })),
        elementos: props.elementos || [],
        postUrl: props.postUrl,
        deleteUrlBase: props.deleteUrlBase,

        adding: false,
        newPos: null,
        newPosArr: null,
        selectedElemento: null,
        hover: null,

        init() {
          const sceneEl = this.$refs.scene;
          sceneEl.addEventListener('loaded', () => {
            sceneEl.canvas.addEventListener('mousemove', this.onMouseMove.bind(this));
          });
          sceneEl.addEventListener('click', this.onClick.bind(this));
        },

        startAdd() {
          this.adding = true;
          this.newPos = this.newPosArr = this.selectedElemento = null;
        },

        onClick(evt) {
          if (!this.adding) return;

          const rect = this.$refs.scene.canvas.getBoundingClientRect();
          const x_ndc = ((evt.clientX - rect.left) / rect.width) * 2 - 1;
          const y_ndc = -((evt.clientY - rect.top) / rect.height) * 2 + 1;
          const mouse = new AFRAME.THREE.Vector2(x_ndc, y_ndc);

          const camera = this.$refs.scene.camera.el.getObject3D('camera');
          const raycaster = new AFRAME.THREE.Raycaster();
          raycaster.setFromCamera(mouse, camera);

          const skyObj = this.$refs.scene.querySelector('#sky').object3D;
          const hit = raycaster.intersectObject(skyObj, true)[0];

          if (hit) {
            const p = hit.point, eps = 0.98;
            this.newPosArr = [(p.x*eps),(p.y*eps),(p.z*eps)].map(v=>+v.toFixed(2));
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
          const hit = ray.intersectObject(skyObj, true)[0];
          this.hover = hit ? `${hit.point.x.toFixed(2)}, ${hit.point.y.toFixed(2)}, ${hit.point.z.toFixed(2)}` : null;
        },

        cancelAdd() {
          this.adding = false;
          this.newPos = this.newPosArr = this.selectedElemento = null;
        },

        confirmAdd() {
          if (!this.newPos || !this.selectedElemento) return;
          fetch(this.postUrl, {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':'{{ csrf_token() }}' },
            body: JSON.stringify({ elemento_id: this.selectedElemento, posicion: this.newPos })
          }).then(res => res.ok ? location.reload() : Promise.reject());
        },

        deleteHotspot(id) {
          if (!confirm('¿Eliminar hotspot?')) return;
          fetch(`${this.deleteUrlBase}/${id}`, {
            method:'DELETE',
            headers:{ 'X-CSRF-TOKEN':'{{ csrf_token() }}' }
          }).then(res => res.ok ? location.reload() : Promise.reject());
        }
      }
    }
    </script>
</x-slot>


</x-app-layout>

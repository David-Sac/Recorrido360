{{-- resources/views/hotspots/index.blade.php --}}
<x-app-layout>
  <x-slot name="head">
    <script src="https://aframe.io/releases/1.4.0/aframe.min.js"></script>
    <script src="https://unpkg.com/aframe-look-at-component/dist/aframe-look-at-component.min.js"></script>
    <script src="https://unpkg.com/aframe-event-set-component/dist/aframe-event-set-component.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
      .scene-container { width:100%; height:600px; position:relative; }
      .modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,.75); display:flex; align-items:center; justify-content:center; z-index:40; }
      .modal-card { width: min(900px, 92vw); max-height: 88vh; background:#0b0f19; color:#e5e7eb; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,.5); overflow:hidden; display:flex; flex-direction:column; }
      .modal-header { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; background:#111827; }
      .modal-body { padding:16px; overflow:auto; }
      .btn-close { width:36px; height:36px; border-radius:8px; background:#b91c1c; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; }
      .media-box { width:100%; aspect-ratio:16/9; background:#000; display:flex; align-items:center; justify-content:center; border-radius:8px; overflow:hidden; }
      .media-box img, .media-box video, .media-box audio { max-width:100%; max-height:100%; display:block; }
    </style>

    <script>
      // Componente A-Frame que emite un CustomEvent al hacer click en el hotspot
      AFRAME.registerComponent('hotspot-click', {
        schema: { id: {type: 'string'} },
        init: function () {
          this.el.addEventListener('click', () => {
            window.dispatchEvent(new CustomEvent('hs-click', { detail: { id: this.data.id } }));
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
      deleteUrlBase: @json(url("/hotspots")),
      csrf: @json(csrf_token())
    })'
    x-init="init()"
    class="relative max-w-4xl px-4 py-6 mx-auto space-y-6"
  >
    <div class="text-sm text-gray-600">
      Coordenadas 3D: <span x-text="hover || '—, —, —'"></span>
    </div>

    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold">Visor 360°</h1>
      <div class="space-x-2">
        <button @click="startAdd()" class="px-3 py-1 text-white bg-green-600 rounded">＋ Añadir</button>
        <button @click="cancelAdd()" class="px-3 py-1 text-white bg-gray-500 rounded">✕ Cancelar</button>
      </div>
    </div>

    <div class="overflow-hidden bg-black rounded-lg scene-container">
      <a-scene x-ref="scene" embedded>
        <a-assets>
          <img id="pano" src="{{ asset('storage/'.$panorama->imagen_path) }}" />
        </a-assets>

        <!-- Domo 360 -->
        <a-entity id="sky"
                  geometry="primitive: sphere; radius: 100; segmentsWidth: 64; segmentsHeight: 64"
                  material="shader: flat; side: back; src: #pano"
                  rotation="0 -100 0"
                  scale="-1 1 1">
        </a-entity>

        <!-- Cámara + cursor (mouse) -->
        <a-entity id="camera" camera look-controls position="0 1.6 0">
          <a-entity
            cursor="rayOrigin: mouse"
            raycaster="objects: .clickable"
            position="0 0 -1"
            geometry="primitive: ring; radiusInner: 0.02; radiusOuter: 0.03"
            material="color: white; shader: flat">
          </a-entity>
        </a-entity>

        <!-- Hotspots (sin cambio de color) -->
        <template x-for="h in hotspots" :key="h.id">
          <a-circle
            class="clickable"
            :hotspot-click="`id: ${h.id}`"
            :position="h.posArr.join(' ')"
            radius="5"
            color="#454545"
            transparent="true"
            opacity="0.85"
            look-at="#camera"
            event-set__enter="_event: mouseenter; scale: 1.2 1.2 1.2"
            event-set__leave="_event: mouseleave; scale: 1 1 1">
          </a-circle>
        </template>

        <!-- Esfera dinámica al añadir -->
        <template x-if="adding && newPosArr">
          <a-circle
            :position="newPosArr.join(' ')"
            radius="5"
            color="#22C55E"
            transparent="true"
            opacity="0.9"
            look-at="#camera"
            event-set__enter="_event: mouseenter; scale: 1.2 1.2 1.2"
            event-set__leave="_event: mouseleave; scale: 1 1 1"
            @click="confirmAdd()">
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
              <td class="px-6 py-4 whitespace-nowrap" x-text="h.elemento?.nombre || '—'"></td>
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

    <!-- Modal overlay (oscurece fondo) -->
    <template x-if="panel.visible">
      <div class="modal-overlay" @click.self="closePanel()">
        <div class="modal-card">
          <div class="modal-header">
            <div class="font-semibold" x-text="panel.title || 'Detalle'"></div>
            <button class="btn-close" @click="closePanel()">✕</button>
          </div>
          <div class="space-y-4 modal-body">
            <!-- DATOS -->
            <template x-if="panel.type === 'datos'">
              <div>
                <p class="text-slate-300" x-text="panel.description || panel.content || 'Sin contenido.'"></p>
              </div>
            </template>

            <!-- IMAGEN -->
            <template x-if="panel.type === 'imagen' && panel.src">
              <div class="media-box">
                <img :src="panel.src" alt="" loading="lazy">
              </div>
            </template>

            <!-- VIDEO -->
            <template x-if="panel.type === 'video' && panel.src">
              <div class="media-box">
                <video x-ref="videoEl" :src="panel.src" playsinline controls autoplay muted></video>
              </div>
            </template>

            <!-- AUDIO -->
            <template x-if="panel.type === 'audio' && panel.src">
              <div class="media-box" style="aspect-ratio:auto;">
                <audio x-ref="audioEl" :src="panel.src" controls autoplay></audio>
              </div>
            </template>

            <!-- OTRO -->
            <template x-if="panel.type === 'otro'">
              <div>
                <p class="text-slate-300" x-text="panel.description || panel.content || 'Sin contenido.'"></p>
                <template x-if="panel.src">
                  <p class="mt-2 text-sm text-slate-400">
                    Recurso: <a :href="panel.src" target="_blank" class="underline">Abrir enlace</a>
                  </p>
                </template>
              </div>
            </template>
          </div>
        </div>
      </div>
    </template>
  </main>

  <x-slot name="scripts">
    <script>
    function hotspotManager(props) {
      return {
        hotspots: (props.hotspots || []).map(h => ({
          ...h,
          posArr: Array.isArray(h.posArr) && h.posArr.length === 3
            ? h.posArr.map(Number)
            : String(h.posicion || '0 0 0').split(' ').map(Number)
        })),
        elementos: props.elementos || [],
        postUrl: props.postUrl,
        deleteUrlBase: props.deleteUrlBase,
        csrf: props.csrf,

        adding: false,
        newPos: null,
        newPosArr: null,
        selectedElemento: null,
        hover: null,

        panel: { visible:false, type:null, title:null, description:null, content:null, src:null },

        init() {
          const sceneEl = this.$refs.scene;
          sceneEl.addEventListener('loaded', () => {
            sceneEl.canvas.addEventListener('mousemove', this.onMouseMove.bind(this));
            // Cerrar con ESC
            window.addEventListener('keydown', (e) => { if (e.key === 'Escape') this.closePanel(); });
          });
          sceneEl.addEventListener('click', this.onClick.bind(this));

          // 🔊 Escucha los clicks emitidos por el componente A-Frame
          window.addEventListener('hs-click', (ev) => {
            const id = String(ev.detail?.id || '');
            const h = this.hotspots.find(x => String(x.id) === id);
            if (h) this.openElement(h);
          });
        },

        // Abrir modal con el elemento
        openElement(h) {
          const e = h.elemento || null;
          if (!e) return;

          const src = e.source_url || (e.media_path ? `${window.location.origin}/storage/${e.media_path}` : (e.url || null));
          const type = e.tipo || 'datos';

          this.panel = {
            visible: true,
            type: type,
            title: e.titulo || e.nombre || 'Elemento',
            description: e.descripcion || '',
            content: e.contenido || '',
            src: src
          };

          this.$nextTick(() => {
            if (type === 'video' && this.$refs.videoEl) {
              try { this.$refs.videoEl.play().catch(()=>{}); } catch {}
            }
            if (type === 'audio' && this.$refs.audioEl) {
              try { this.$refs.audioEl.play().catch(()=>{}); } catch {}
            }
          });
        },

        // Cerrar modal y pausar media
        closePanel() {
          if (this.$refs.videoEl) { try { this.$refs.videoEl.pause(); } catch {} }
          if (this.$refs.audioEl) { try { this.$refs.audioEl.pause(); } catch {} }
          this.panel.visible = false;
        },

        // Añadir hotspot
        startAdd() { this.adding = true; this.newPos = this.newPosArr = this.selectedElemento = null; },
        cancelAdd() { this.adding = false; this.newPos = this.newPosArr = this.selectedElemento = null; },

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

        // Persistencia
        confirmAdd() {
          if (!this.newPos || !this.selectedElemento) return;
          fetch(this.postUrl, {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': this.csrf },
            body: JSON.stringify({ elemento_id: this.selectedElemento, posicion: this.newPos })
          }).then(res => res.ok ? location.reload() : Promise.reject());
        },
        deleteHotspot(id) {
          if (!confirm('¿Eliminar hotspot?')) return;
          fetch(`${this.deleteUrlBase}/${id}`, {
            method:'DELETE',
            headers:{ 'X-CSRF-TOKEN': this.csrf }
          }).then(res => res.ok ? location.reload() : Promise.reject());
        }
      }
    }
    </script>
  </x-slot>
</x-app-layout>

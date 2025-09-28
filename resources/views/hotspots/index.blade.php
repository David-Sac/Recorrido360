{{-- resources/views/hotspots/index.blade.php --}}
<x-app-layout>
  <x-slot name="head">
    <script src="https://aframe.io/releases/1.4.0/aframe.min.js"></script>
    <script src="https://unpkg.com/aframe-look-at-component/dist/aframe-look-at-component.min.js"></script>
    <script src="https://unpkg.com/aframe-event-set-component/dist/aframe-event-set-component.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
      .scene-container { width:100%; height:600px; position:relative; }

      /* Overlay y modal DENTRO del visor 360 */
      .scene-overlay { position:absolute; inset:0; background:rgba(0,0,0,.75); display:flex; align-items:center; justify-content:center; z-index:36; pointer-events:auto; }
      .scene-modal   { width:92%; height:88%; max-width:1100px; max-height:92%; background:#0b0f19; color:#e5e7eb; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,.55); overflow:hidden; display:flex; flex-direction:column; border:1px solid rgba(255,255,255,.06); }
      .scene-modal__header { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; background:#111827; }
      .scene-modal__body   { padding:12px; overflow:auto; }
      .btn-close  { width:32px; height:32px; border-radius:8px; background:#b91c1c; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; }
      .media-box  { width:100%; aspect-ratio:16/9; background:#000; display:flex; align-items:center; justify-content:center; border-radius:8px; overflow:hidden; }
      .media-box img, .media-box video, .media-box audio { max-width:100%; max-height:100%; display:block; }
      .badge { display:inline-flex; align-items:center; gap:6px; font-size:12px; padding:3px 8px; border-radius:999px; background:#0f172a; color:#cbd5e1; }
      .muted { color:#94a3b8; }

      /* Modal confirmación (global, fuera del visor) */
      .modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,.75); display:flex; align-items:center; justify-content:center; z-index:40; }
      .modal-card { width: min(900px, 92vw); max-height: 88vh; background:#0b0f19; color:#e5e7eb; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,.5); overflow:hidden; display:flex; flex-direction:column; }
      .modal-header { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; background:#111827; }
      .modal-body { padding:16px; overflow:auto; }
      .btn { display:inline-flex; align-items:center; justify-content:center; padding:.5rem .75rem; border-radius:.5rem; }
      .btn-red { background:#ef4444; color:#fff; } .btn-red:hover { background:#dc2626; }
      .btn-ghost { background:#374151; color:#e5e7eb; } .btn-ghost:hover { background:#4b5563; }
      .toast { position:fixed; right:16px; bottom:16px; z-index:50; background:#111827; color:#e5e7eb; padding:10px 14px; border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,.35) }
    </style>

    <script>
    // Componente A-Frame: emite evento global hs-click
    AFRAME.registerComponent('hotspot-click', {
      schema: { id: { type: 'string' } },
      init: function () {
        const fire = () => window.dispatchEvent(new CustomEvent('hs-click', { detail: { id: this.data.id } }));
        this.el.addEventListener('click', fire);
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
        <button x-on:click="startAdd()" class="px-3 py-1 text-white bg-green-600 rounded">＋ Añadir</button>
        <button x-on:click="cancelAdd()" class="px-3 py-1 text-white bg-gray-500 rounded">✕ Cancelar</button>
      </div>
    </div>

    <!-- Mensaje guía al añadir -->
    <template x-if="adding">
      <div class="p-3 text-sm rounded bg-emerald-50 text-emerald-800">
        Modo Añadir: <strong>click</strong> en la imagen 360° para fijar la posición, ajusta la <strong>separación</strong> y luego selecciona el <strong>elemento</strong>.
      </div>
    </template>

    <div class="overflow-hidden bg-black rounded-lg scene-container" :class="adding ? 'cursor-crosshair' : 'cursor-default'">
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

        <!-- Cámara + cursor -->
        <a-entity 
          id="camera" 
          camera 
          look-controls 
          position="0 1.6 0"
          cursor="rayOrigin: mouse; fuse: false"
          raycaster="objects: .clickable; far: 800">
        </a-entity>

        <!-- Hotspots -->
        <template x-for="h in hotspots" :key="h.id">
          <a-circle
            class="clickable"
            x-bind:hotspot-click="'id: ' + String(h.id)"
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

        <!-- Punto temporal al añadir -->
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
            x-on:click="confirmAdd()">
          </a-circle>
        </template>
      </a-scene>

      <!-- Overlay + Modal centrados DENTRO del visor -->
      <template x-if="panel.visible">
        <div class="scene-overlay" x-transition x-on:click.self="closePanel()" role="dialog" aria-modal="true" aria-labelledby="panelTitle">
          <section class="scene-modal" x-transition>
            <header class="scene-modal__header">
              <div class="flex items-center gap-3">
                <h3 id="panelTitle" class="font-semibold" x-text="panel.title || 'Elemento'"></h3>
                <span class="badge"><span x-text="panel.type"></span></span>
              </div>
              <button class="btn-close" x-on:click="closePanel()" aria-label="Cerrar">✕</button>
            </header>
            <div class="space-y-3 scene-modal__body">
              <!-- Descripción -->
              <template x-if="panel.description">
                <p class="muted" x-text="panel.description"></p>
              </template>

              <!-- DATOS -->
              <template x-if="panel.type === 'datos'">
                <div class="p-3 rounded bg-slate-800/60">
                  <p class="whitespace-pre-wrap" x-text="panel.content || 'Sin contenido.'"></p>
                </div>
              </template>

              <!-- IMAGEN -->
              <template x-if="panel.type === 'imagen'">
                <div class="space-y-2">
                  <div class="media-box">
                    <template x-if="!panel.imgError && panel.src">
                      <img :src="panel.src" :alt="panel.title || ''" loading="lazy" x-on:error="panel.imgError=true">
                    </template>
                    <template x-if="panel.imgError || !panel.src">
                      <div class="p-4 text-sm text-center text-slate-400">No se pudo cargar la imagen.</div>
                    </template>
                  </div>
                  <p class="text-xs muted" x-text="panel.caption || ''"></p>
                </div>
              </template>

              <!-- VIDEO -->
              <template x-if="panel.type === 'video'">
                <div class="space-y-2">
                  <div class="media-box">
                    <template x-if="panel.src">
                      <video x-ref="videoEl" :src="panel.src" playsinline controls autoplay muted></video>
                    </template>
                    <template x-if="!panel.src">
                      <div class="p-4 text-sm text-center text-slate-400">Video no disponible.</div>
                    </template>
                  </div>
                  <p class="text-xs muted" x-text="panel.caption || ''"></p>
                </div>
              </template>

              <!-- AUDIO -->
              <template x-if="panel.type === 'audio'">
                <div class="space-y-2">
                  <div class="media-box" style="aspect-ratio:auto;">
                    <template x-if="panel.src">
                      <audio x-ref="audioEl" :src="panel.src" controls autoplay></audio>
                    </template>
                    <template x-if="!panel.src">
                      <div class="p-4 text-sm text-center text-slate-400">Audio no disponible.</div>
                    </template>
                  </div>
                  <p class="text-xs muted" x-text="panel.caption || ''"></p>
                </div>
              </template>

              <!-- OTRO -->
              <template x-if="panel.type === 'otro'">
                <div class="space-y-2">
                  <p class="whitespace-pre-wrap" x-text="panel.content || 'Sin contenido.'"></p>
                  <template x-if="panel.src">
                    <p class="text-sm text-slate-400">Recurso: <a :href="panel.src" target="_blank" class="underline">Abrir enlace</a></p>
                  </template>
                </div>
              </template>
            </div>
          </section>
        </div>
      </template>
      <!-- /Overlay dentro del visor -->
    </div>

    <!-- Formulario de nuevo hotspot -->
    <div x-show="adding && newPos" class="p-4 mt-4 rounded shadow bg-gray-50">
      <div class="mb-2 text-sm">Nueva posición: <strong x-text="newPos"></strong></div>

      <!-- Control de separación -->
      <label class="block mb-1 text-sm font-medium">
        Separación del fondo (m): <span x-text="depthOffset.toFixed(2)"></span>
      </label>
      <input type="range"
             min="0" max="5" step="0.05"
             x-model.number="depthOffset"
             x-on:input="recomputeFromOffset()"
             class="w-full mb-3">
      <div class="flex items-center gap-2 mb-3">
        <button type="button" class="px-2 py-1 bg-gray-200 rounded" x-on:click="nudgeOffset(-0.05)">–</button>
        <button type="button" class="px-2 py-1 bg-gray-200 rounded" x-on:click="nudgeOffset(0.05)">+</button>
        <span class="text-xs text-gray-500">Usa ± para ajustes finos.</span>
      </div>

      <label class="block mb-2 text-sm font-medium">Selecciona elemento:</label>
      <select x-model="selectedElemento" class="w-full p-2 mb-3 border rounded">
        <option value="">— Elige elemento —</option>
        <template x-for="e in elementos" :key="e.id">
          <option :value="e.id" x-text="e.nombre"></option>
        </template>
      </select>
      <button x-on:click="confirmAdd()"
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
              <td class="px-6 py-4 whitespace-nowrap" x-text="h.elemento_nombre || h.elemento?.nombre || '—'"></td>
              <td class="px-6 py-4 whitespace-nowrap" x-text="h.posicion"></td>
              <td class="px-6 py-4 text-right">
                <button x-on:click="promptDelete(h)" class="px-2 py-1 text-sm text-white bg-red-500 rounded hover:bg-red-600">−</button>
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

    <!-- Modal: Confirmación de eliminación (global) -->
    <template x-if="confirm.visible">
      <div class="modal-overlay" x-transition.opacity x-on:click.self="closeConfirm()" role="dialog" aria-modal="true" aria-labelledby="confirmTitle">
        <div class="max-w-lg modal-card" x-transition.scale.origin.center>
          <div class="modal-header">
            <div class="flex items-center gap-2">
              <svg class="w-5 h-5 text-red-400" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M11 15h2v2h-2zm0-8h2v6h-2z"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/></svg>
              <h3 id="confirmTitle" class="font-semibold">Eliminar hotspot</h3>
            </div>
            <button class="btn-close" x-on:click="closeConfirm()" aria-label="Cerrar">✕</button>
          </div>
          <div class="space-y-3 modal-body">
            <p class="text-slate-300">¿Seguro que deseas eliminar este hotspot? Esta acción no se puede deshacer.</p>
            <div class="p-3 text-sm rounded text-slate-400 bg-slate-800/50">
              <div><span class="text-slate-500">Elemento:</span> <span x-text="confirm.meta.nombre || '—'"></span></div>
              <div><span class="text-slate-500">Posición:</span> <span x-text="confirm.meta.posicion || '—'"></span></div>
              <template x-if="confirm.meta.id"><div><span class="text-slate-500">ID:</span> <span x-text="confirm.meta.id"></span></div></template>
            </div>
          </div>
          <div class="flex items-center justify-end gap-2 p-4 bg-[#0b0f19]">
            <button x-ref="cnfCancel" x-on:click="closeConfirm()" class="btn btn-ghost" :disabled="confirm.loading">Cancelar</button>
            <button x-on:click="confirmDelete()" class="relative btn btn-red" :disabled="confirm.loading">
              <svg x-show="confirm.loading" class="absolute w-4 h-4 animate-spin left-3" viewBox="0 0 24 24" fill="none">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
              </svg>
              <span class="pl-4" :class="{'pl-6': confirm.loading}">Eliminar</span>
            </button>
          </div>
        </div>
      </div>
    </template>

    <!-- Toast -->
    <template x-if="toast.visible">
      <div class="toast" x-text="toast.message" x-transition x-on:click="toast.visible=false"></div>
    </template>
  </main>

  <x-slot name="scripts">
<script>
function hotspotManager(props) {
  const RADIUS = 100;   // radio del domo
  return {
    // ====== datos ======
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

    // ====== estado UI ======
    adding: false,
    newPos: null,
    newPosArr: null,
    selectedElemento: null,
    hover: null,

    depthOffset: 0.50,   // separación desde el fondo (m)
    dirVec: null,        // dirección normalizada [nx,ny,nz]

    panel: { visible:false, type:null, title:null, description:null, caption:null, content:null, src:null, imgError:false },
    confirm: { visible:false, loading:false, id:null, meta:{ id:null, nombre:null, posicion:null } },
    toast: { visible:false, message:'' },

    // ====== init ======
    init() {
      const sceneEl = this.$refs.scene;
      sceneEl.addEventListener('loaded', () => {
        // hover y click precisos sobre el domo
        sceneEl.canvas.addEventListener('mousemove', this.onMouseMove.bind(this));
        sceneEl.canvas.addEventListener('click', this.onCanvasClick.bind(this));
        // ESC cierra modales
        window.addEventListener('keydown', (e) => {
          if (e.key === 'Escape') { this.closePanel(); if (this.confirm.visible) this.closeConfirm(); }
        });
      });

      // click en hotspot → abrir modal dentro del visor
      window.addEventListener('hs-click', (ev) => {
        const id = String(ev.detail?.id || '');
        const h = this.hotspots.find(x => String(x.id) === id);
        if (h) this.openElement(h);
      });
    },

    // ====== utilidades ======
    raycastSky(evt) {
      const rect = this.$refs.scene.canvas.getBoundingClientRect();
      const x_ndc = ((evt.clientX - rect.left) / rect.width) * 2 - 1;
      const y_ndc = -((evt.clientY - rect.top) / rect.height) * 2 + 1;
      const mouse = new AFRAME.THREE.Vector2(x_ndc, y_ndc);
      const camera = this.$refs.scene.camera.el.getObject3D('camera');
      const raycaster = new AFRAME.THREE.Raycaster();
      raycaster.setFromCamera(mouse, camera);
      const skyObj = this.$refs.scene.querySelector('#sky').object3D;
      const inter = raycaster.intersectObject(skyObj, true)[0];
      return inter ? inter.point : null; // world coords
    },

    // ====== añadir ======
    recomputeFromOffset() {
      if (!this.dirVec) return;
      const s = Math.max(0, Math.min(5, this.depthOffset));
      const radiusIn = RADIUS - s;
      const arr = this.dirVec.map(c => +(c * radiusIn).toFixed(2));
      this.newPosArr = arr;
      this.newPos = arr.join(' ');
    },
    nudgeOffset(delta) {
      this.depthOffset = Math.max(0, Math.min(5, +(this.depthOffset + delta).toFixed(2)));
      this.recomputeFromOffset();
    },
    onCanvasClick(evt) {
      if (!this.adding) return;
      const p = this.raycastSky(evt);
      if (!p) return;
      const n = new AFRAME.THREE.Vector3(p.x, p.y, p.z).normalize();
      this.dirVec = [n.x, n.y, n.z];
      this.recomputeFromOffset();
    },
    onMouseMove(evt) {
      const p = this.raycastSky(evt);
      if (!p) { this.hover = null; return; }
      const n = new AFRAME.THREE.Vector3(p.x, p.y, p.z).normalize();
      const radiusIn = RADIUS - (this.adding ? this.depthOffset : 0.15);
      const v = n.multiplyScalar(radiusIn);
      this.hover = `${v.x.toFixed(2)}, ${v.y.toFixed(2)}, ${v.z.toFixed(2)}`;
    },

    startAdd() { this.adding = true; this.newPos = this.newPosArr = this.selectedElemento = null; this.depthOffset = 0.50; this.dirVec = null; },
    cancelAdd() { this.adding = false; this.newPos = this.newPosArr = this.selectedElemento = null; this.dirVec = null; },

    confirmAdd() {
      if (!this.newPos || !this.selectedElemento) return;
      fetch(this.postUrl, {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': this.csrf },
        body: JSON.stringify({ elemento_id: this.selectedElemento, posicion: this.newPos })
      })
      .then(async res => {
        if (!res.ok) throw new Error('HTTP '+res.status);
        const data = await res.json().catch(() => ({}));
        if (data && data.success && data.position) {
          const posArr = String(data.position).split(' ').map(Number);
          this.hotspots.push({
            id: data.id,
            posicion: data.position,
            posArr,
            elemento_id: data.elemento?.id || data.elemento_id,
            elemento_nombre: data.elemento?.nombre || data.elemento_nombre,
            elemento: data.elemento || null
          });
          this.cancelAdd();
          this.showToast('Hotspot creado');
        } else { location.reload(); }
      })
      .catch(() => location.reload());
    },

    // ====== modal dentro del visor ======
    _resolveSrc(e) {
      let c = (e?.contenido ?? '').toString().trim();
      if (!c) return null;
      c = c.replace(/\\/g,'/');                            // Windows → web
      if (/^https?:\/\//i.test(c)) return c;               // URL absoluta
      if (c.startsWith('/storage/')) return `${window.location.origin}${c}`;
      if (c.startsWith('storage/'))  return `${window.location.origin}/${c}`;
      const clean = c.replace(/^public\//,'').replace(/^\/+/, '');
      return `${window.location.origin}/storage/${clean}`;  // asset('storage/...')
    },
openElement(h) {
  const e = h.elemento || null;
  if (!e) return;
  const type = String(e.tipo || 'datos').toLowerCase();

  // ✅ usa media_url del backend si existe
  const src = (type !== 'datos') ? (e.media_url || this._resolveSrc(e)) : null;

  const caption = (e.descripcion || '') || ((e.contenido || '').includes('/') ? (e.contenido || '').split('/').pop() : '');
  this.panel = {
    visible: true,
    type,
    title: e.nombre || 'Elemento',
    description: e.descripcion || '',
    caption,
    content: e.contenido || '',
    src,
    imgError: false
  };

  console.log('[Elemento abierto]', { type, src, contenido: e.contenido }); // 👈 DEBUG ÚTIL

  this.$nextTick(() => {
    if (type === 'video' && this.$refs.videoEl) { try { this.$refs.videoEl.play().catch(()=>{}); } catch {} }
    if (type === 'audio' && this.$refs.audioEl) { try { this.$refs.audioEl.play().catch(()=>{}); } catch {} }
  });
},

    closePanel() {
      if (this.$refs.videoEl) { try { this.$refs.videoEl.pause(); } catch {} }
      if (this.$refs.audioEl) { try { this.$refs.audioEl.pause(); } catch {} }
      this.panel.visible = false;
    },

    // ====== eliminar con modal ======
    promptDelete(h) {
      this.confirm.visible = true;
      this.confirm.loading = false;
      this.confirm.id = h.id;
      this.confirm.meta = { id: h.id, nombre: h.elemento_nombre || h.elemento?.nombre || '', posicion: h.posicion || '' };
      this.$nextTick(() => this.$refs.cnfCancel?.focus());
      window.addEventListener('keydown', this._onConfirmKeys);
    },
    _onConfirmKeys: (e) => {},
    _onConfirmKeys(e) {
      if (!this.confirm.visible) return;
      if (e.key === 'Enter') { e.preventDefault(); this.confirmDelete(); }
      if (e.key === 'Escape') { e.preventDefault(); this.closeConfirm(); }
    },
    closeConfirm() {
      this.confirm.visible = false;
      this.confirm.loading = false;
      window.removeEventListener('keydown', this._onConfirmKeys);
    },
    confirmDelete() {
      if (!this.confirm.id) return;
      this.confirm.loading = true;
      fetch(`${this.deleteUrlBase}/${this.confirm.id}`, {
        method: 'DELETE',
        headers:{ 'X-CSRF-TOKEN': this.csrf }
      })
      .then(res => {
        if (!res.ok) throw new Error('HTTP '+res.status);
        this.hotspots = this.hotspots.filter(x => x.id !== this.confirm.id);
        this.closeConfirm();
        this.showToast('Hotspot eliminado');
      })
      .catch(() => { this.closeConfirm(); location.reload(); });
    },

    // ====== toast ======
    showToast(msg='Listo') { this.toast.message = msg; this.toast.visible = true; setTimeout(() => { this.toast.visible = false; }, 2000); },
  }
}
</script>
  </x-slot>
</x-app-layout>

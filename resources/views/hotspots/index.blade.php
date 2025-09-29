{{-- resources/views/hotspots/index.blade.php --}}
<x-app-layout :show-footer="false">
  <x-slot name="head">
    <script src="https://aframe.io/releases/1.4.0/aframe.min.js"></script>
    <script src="https://unpkg.com/aframe-look-at-component/dist/aframe-look-at-component.min.js"></script>
    <script src="https://unpkg.com/aframe-event-set-component/dist/aframe-event-set-component.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
      :root { --viewer-h: min(78vh, calc(100vh - 140px)); }

      /* Sidebar */
      .sidebar   { width:100%; height:var(--viewer-h); background:#fff; border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,.08); display:flex; flex-direction:column; overflow:hidden; }
      .side-head { padding:12px 14px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; }
      .side-list { flex:1; overflow:auto; padding:8px; }
      .side-item { display:flex; align-items:center; justify-content:space-between; gap:.5rem; padding:10px 12px; border-radius:10px; background:#f8fafc; cursor:pointer; }
      .side-item + .side-item { margin-top:8px; }
      .side-item:hover { background:#eef2ff; }
      .side-foot { padding:10px 12px; border-top:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; }
      .fab { width:40px; height:40px; border-radius:999px; display:inline-flex; align-items:center; justify-content:center; background:#059669; color:#fff; font-weight:700; }
      .fab:hover { background:#047857; }
      .coord { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; font-size:12px; color:#475569; }
      .form-card { background:#f8fafc; border-top:1px dashed #cbd5e1; padding:10px 12px; }

      /* Viewer 360 */
      .viewer { position:relative; height:var(--viewer-h); background:#000; border-radius:12px; overflow:hidden; box-shadow:0 8px 24px rgba(0,0,0,.08); }

      /* HUD dentro del visor */
      .scene-overlay { position:absolute; inset:0; background:rgba(0,0,0,.75); display:flex; align-items:center; justify-content:center; z-index:36; }
      .scene-modal   { width:min(1100px, 92%); height:88%; max-height:92%; background:#0b0f19; color:#e5e7eb; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,.55); overflow:hidden; display:flex; flex-direction:column; border:1px solid rgba(255,255,255,.06); }
      .scene-modal__header { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; background:#111827; }
      .scene-modal__body   { padding:12px; overflow:auto; }
      .btn-close  { width:32px; height:32px; border-radius:8px; background:#b91c1c; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; }
      .media-box  { width:100%; aspect-ratio:16/9; background:#000; display:flex; align-items:center; justify-content:center; border-radius:8px; overflow:hidden; }
      .media-box img, .media-box video, .media-box audio { max-width:100%; max-height:100%; display:block; }
      .badge { display:inline-flex; align-items:center; gap:6px; font-size:12px; padding:3px 8px; border-radius:999px; background:#0f172a; color:#cbd5e1; }
      .muted { color:#94a3b8; }

      /* Modal confirmación global */
      .modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,.75); display:flex; align-items:center; justify-content:center; z-index:40; }
      .modal-card { width: min(900px, 92vw); max-height: 88vh; background:#0b0f19; color:#e5e7eb; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,.5); overflow:hidden; display:flex; flex-direction:column; }
      .modal-header { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; background:#111827; }
      .modal-body { padding:16px; overflow:auto; }

      .toast { position:fixed; right:16px; bottom:16px; z-index:50; background:#111827; color:#e5e7eb; padding:10px 14px; border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,.35) }
    </style>

    <script>
      // Emite hs-click al hacer click en un hotspot
      AFRAME.registerComponent('hotspot-click', {
        schema: { id: { type: 'string' } },
        init: function () {
          const fire = () => window.dispatchEvent(new CustomEvent('hs-click', { detail: { id: this.data.id } }));
          this.el.addEventListener('click', fire);
        }
      });
    </script>
  </x-slot>

  {{-- Alpine scope en <main> para que el TOOLBOX comparta estado (adding) --}}
  <main
    x-data='hotspotManager({
      hotspots: @json($hotspots),
      elementos: @json($elementos),
      postUrl: @json(route("panoramas.hotspots.store", $panorama)),
      deleteUrlBase: @json(url("/hotspots")),
      csrf: @json(csrf_token())
    })'
    x-init="init()"
    class="w-full px-4 py-6 mx-auto max-w-7xl"
  >
    {{-- TOOLBOX unificado --}}
    <x-ui.toolbox
      :title="'Hotspots'"
      :subtitle="'Panorama: ' . $panorama->nombre"
      :back="route('panoramas.index')"
      backLabel="Volver a panoramas"
    >
      {{-- Toggle añadir hotspot desde la toolbar --}}
      <x-ui.btn-primary @click="adding = !adding" x-text="adding ? 'Cancelar' : '+ Añadir hotspot'"></x-ui.btn-primary>
    </x-ui.toolbox>

    {{-- GRID 30% / 70% --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-10">

      {{-- 30% IZQUIERDA --}}
      <aside class="md:col-span-3">
        <div class="sidebar">
          <div class="side-head">
            <div class="font-semibold text-slate-800">Hotspots</div>
            <span class="text-xs text-slate-500" x-text="hotspots.length + ' en total'"></span>
          </div>

          <div class="side-list">
            <template x-for="h in hotspots" :key="h.id">
              <div class="side-item" x-on:click="openElement(h)" title="Ver elemento">
                <div>
                  <div class="text-sm font-medium text-slate-800" x-text="h.elemento_nombre || h.elemento?.nombre || ('Hotspot #' + h.id)"></div>
                  <div class="text-xs coord" x-text="h.posicion"></div>
                </div>

                <form :action="`${deleteUrlBase}/${h.id}`" method="POST" x-on:submit.prevent="promptDelete(h)">
                  @csrf @method('DELETE')
                  <x-ui.btn-ghost type="submit" class="px-2 py-1 text-xs text-rose-600 border-rose-300 hover:bg-rose-50">Eliminar</x-ui.btn-ghost>
                </form>
              </div>
            </template>

            <template x-if="hotspots.length === 0">
              <div class="p-3 text-sm text-center text-slate-500">No hay hotspots aún.</div>
            </template>
          </div>

          <div class="side-foot">
            <div>
              <div class="text-xs text-slate-500">Coordenadas</div>
              <div class="coord"><span x-text="hover || '—, —, —'"></span></div>
            </div>

            {{-- Botón flotante secundario para añadir --}}
            <button type="button" class="fab" title="Añadir hotspot" x-on:click="startAdd()">＋</button>
          </div>

          {{-- Formulario de guardado cuando ya seleccionaste punto --}}
          <div class="form-card" x-show="adding && newPos" x-transition>
            <div class="mb-1 text-xs"><strong>Posición:</strong> <span class="coord" x-text="newPos"></span></div>

            <label class="block mb-1 text-sm">Separación del fondo (m): <span x-text="depthOffset.toFixed(2)"></span></label>
            <input type="range" min="0" max="5" step="0.05"
                   x-model.number="depthOffset" x-on:input="recomputeFromOffset()"
                   class="w-full mb-2">

            <div class="flex items-center gap-2 mb-2">
              <x-ui.btn-ghost type="button" class="px-2 py-1" x-on:click="nudgeOffset(-0.05)">–</x-ui.btn-ghost>
              <x-ui.btn-ghost type="button" class="px-2 py-1" x-on:click="nudgeOffset(0.05)">+</x-ui.btn-ghost>
            </div>

            <label class="block mb-1 text-sm">Elemento</label>
            <select x-model="selectedElemento" class="w-full p-2 mb-2 border rounded">
              <option value="">— Elige elemento —</option>
              <template x-for="e in elementos" :key="e.id">
                <option :value="e.id" x-text="e.nombre"></option>
              </template>
            </select>

            <div class="flex items-center justify-between">
              <x-ui.btn-primary type="button" :disabled="true" x-bind:disabled="!selectedElemento" x-on:click="confirmAdd()">Guardar</x-ui.btn-primary>
              <x-ui.btn-secondary type="button" x-on:click="cancelAdd()">Cancelar</x-ui.btn-secondary>
            </div>
          </div>
        </div>
      </aside>

      {{-- 70% DERECHA --}}
      <section class="md:col-span-7">
        <div class="viewer" :class="adding ? 'cursor-crosshair' : 'cursor-default'">
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
            <a-entity id="camera" camera look-controls position="0 1.6 0"
                      cursor="rayOrigin: mouse; fuse: false"
                      raycaster="objects: .clickable; far: 800">
            </a-entity>

            <!-- Hotspots -->
            <template x-for="h in hotspots" :key="h.id">
              <a-circle class="clickable"
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
              <a-circle :position="newPosArr.join(' ')"
                        radius="5"
                        color="#22C55E"
                        transparent="true"
                        opacity="0.95"
                        look-at="#camera"
                        event-set__enter="_event: mouseenter; scale: 1.2 1.2 1.2"
                        event-set__leave="_event: mouseleave; scale: 1 1 1"
                        x-on:click="confirmAdd()">
              </a-circle>
            </template>
          </a-scene>

          <!-- HUD dentro del visor (panel de elemento) -->
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
        </div>
      </section>
    </div>

    {{-- Modal de confirmación (global) --}}
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
            </div>
          </div>
          <div class="flex items-center justify-end gap-2 p-4 bg-[#0b0f19]">
            <x-ui.btn-secondary x-ref="cnfCancel" @click="closeConfirm()" :disabled="false" x-bind:disabled="confirm.loading">Cancelar</x-ui.btn-secondary>
            <x-ui.btn-primary @click="confirmDelete()" class="relative" :disabled="false" x-bind:disabled="confirm.loading">
              <svg x-show="confirm.loading" class="absolute w-4 h-4 animate-spin left-3" viewBox="0 0 24 24" fill="none">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
              </svg>
              <span class="pl-0" :class="{'pl-6': confirm.loading}">Eliminar</span>
            </x-ui.btn-primary>
          </div>
        </div>
      </div>
    </template>

    {{-- Toast --}}
    <template x-if="toast.visible">
      <div class="toast" x-text="toast.message" x-transition x-on:click="toast.visible=false"></div>
    </template>
  </main>

  <x-slot name="scripts">
    <script>
      function hotspotManager(props) {
        const RADIUS = 100;
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

          adding:false, newPos:null, newPosArr:null, selectedElemento:null, hover:null,
          depthOffset:0.50, dirVec:null,

          panel:{ visible:false, type:null, title:null, description:null, caption:null, content:null, src:null, imgError:false },
          confirm:{ visible:false, loading:false, id:null, meta:{ id:null, nombre:null, posicion:null } },
          toast:{ visible:false, message:'' },

          init() {
            const sceneEl = this.$refs.scene;
            sceneEl.addEventListener('loaded', () => {
              sceneEl.canvas.addEventListener('mousemove', this.onMouseMove.bind(this));
              sceneEl.canvas.addEventListener('click', this.onCanvasClick.bind(this));
              window.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') { this.closePanel(); if (this.confirm.visible) this.closeConfirm(); }
              });
            });
            window.addEventListener('hs-click', (ev) => {
              const id = String(ev.detail?.id || '');
              const h = this.hotspots.find(x => String(x.id) === id);
              if (h) this.openElement(h);
            });
          },

          // ---------- raycast ----------
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
            return inter ? inter.point : null;
          },

          // ---------- añadir ----------
          recomputeFromOffset() {
            if (!this.dirVec) return;
            const s = Math.max(0, Math.min(5, this.depthOffset));
            const r = RADIUS - s;
            const arr = this.dirVec.map(c => +(c * r).toFixed(2));
            this.newPosArr = arr;
            this.newPos = arr.join(' ');
          },
          nudgeOffset(d) {
            this.depthOffset = Math.max(0, Math.min(5, +(this.depthOffset + d).toFixed(2)));
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
            const r = RADIUS - (this.adding ? this.depthOffset : 0.15);
            const v = n.multiplyScalar(r);
            this.hover = `${v.x.toFixed(2)}, ${v.y.toFixed(2)}, ${v.z.toFixed(2)}`;
          },

          startAdd() { this.adding = true; this.newPos = this.newPosArr = this.selectedElemento = null; this.depthOffset = 0.50; this.dirVec = null; },
          cancelAdd() { this.adding = false; this.newPos = this.newPosArr = this.selectedElemento = null; this.dirVec = null; },

          confirmAdd() {
            if (!this.newPos || !this.selectedElemento) return;
            fetch(this.postUrl, {
              method:'POST',
              headers:{ 'Content-Type':'application/json','X-CSRF-TOKEN':this.csrf },
              body:JSON.stringify({ elemento_id:this.selectedElemento, posicion:this.newPos })
            })
            .then(async res => {
              if (!res.ok) throw new Error('HTTP '+res.status);
              const data = await res.json().catch(()=>({}));
              if (data?.success && data.position) {
                const posArr = String(data.position).split(' ').map(Number);
                this.hotspots.push({
                  id:data.id,
                  posicion:data.position,
                  posArr,
                  elemento_id:data.elemento?.id || data.elemento_id,
                  elemento_nombre:data.elemento?.nombre || data.elemento_nombre,
                  elemento:data.elemento || null
                });
                this.cancelAdd();
                this.showToast('Hotspot creado');
              } else {
                location.reload();
              }
            })
            .catch(() => location.reload());
          },

          // ---------- modal elemento ----------
          openElement(h) {
            const e = h.elemento || null; if (!e) return;
            const type = String(e.tipo || 'datos').toLowerCase();

            // Prioridad: media_url → url → media_path → contenido (normalizado a /storage)
            let raw = e.media_url || e.url || e.media_path || e.contenido || null;
            let src = null;
            if (type !== 'datos' && raw) {
              if (!/^https?:\/\//i.test(raw)) {
                let c = raw.toString().replace(/\\/g,'/').replace(/^\/+/, '')
                          .replace(/^public\//i,'').replace(/^storage\//i,'');
                src = `${window.location.origin}/storage/${c}`;
              } else { src = raw; }
              src = encodeURI(src);
            }

            const caption = (e.descripcion || '') || ((e.contenido || '').includes('/') ? (e.contenido || '').split('/').pop() : '');
            this.panel = {
              visible:true,
              type,
              title:e.nombre || 'Elemento',
              description:e.descripcion || '',
              caption,
              content:e.contenido || '',
              src,
              imgError:false
            };

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

          // ---------- eliminar ----------
          promptDelete(h) {
            this.confirm.visible = true;
            this.confirm.loading = false;
            this.confirm.id = h.id;
            this.confirm.meta = { id:h.id, nombre:h.elemento_nombre || h.elemento?.nombre || '', posicion:h.posicion || '' };
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
              method:'DELETE',
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

          // ---------- toast ----------
          showToast(msg='Listo') {
            this.toast.message = msg;
            this.toast.visible = true;
            setTimeout(() => { this.toast.visible = false; }, 2000);
          },
        }
      }
    </script>
  </x-slot>
</x-app-layout>

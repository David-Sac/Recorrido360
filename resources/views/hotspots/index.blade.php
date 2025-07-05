{{-- resources/views/hotspots/index.blade.php --}}
<x-app-layout>

  {{-- 1) Inyectamos A-Frame + Alpine --}}
  <x-slot name="head">
    <script src="https://aframe.io/releases/1.4.0/aframe.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
      .scene-container { width:100%; height:600px; }
    </style>
  </x-slot>

  {{-- 2) Cabecera --}}
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Hotspots de “{{ $panorama->nombre }}”</h2>
  </x-slot>

  @php
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
    class="py-6 max-w-6xl mx-auto space-y-6"
  >

    {{-- 3.1) Última posición y modo --}}
    <div class="flex justify-between text-gray-700">
      <div><strong>Modo:</strong> <span x-text="adding ? 'Añadiendo (clic en esfera)' : 'Visualización'"></span></div>
      <div><strong>Última pos:</strong> <span x-text="newPos || '—, —, —'"></span></div>
    </div>

    {{-- 3.2) Botones --}}
    <div class="flex justify-end space-x-2 mb-4">
      <button @click="startAdd()"
              class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded">
        ＋ Añadir
      </button>
      <button @click="cancelAdd()"
              class="px-4 py-2 bg-gray-400 hover:bg-gray-500 text-white rounded">
        ✕ Cancelar
      </button>
    </div>

    {{-- 3.3) Visor 360° --}}
    <div class="scene-container rounded-lg overflow-hidden bg-black">
      <a-scene embedded style="width:100%;height:100%;" x-ref="scene">
        {{-- 3.3.1) El cielo captura el click --}}
        <a-sky
          src="{{ asset('storage/'.$panorama->imagen_path) }}"
          rotation="0 -100 0"
          @mousedown="onClick($event)"
        ></a-sky>

        {{-- 3.3.2) Cámara + cursor --}}
        <a-camera wasd-controls-enabled="false" look-controls>
          <a-cursor rayOrigin="mouse" material="opacity: 0;"></a-cursor>
        </a-camera>

        {{-- 3.3.3) Hotspots guardados --}}
        <template x-for="h in hotspots" :key="h.id">
          <a-circle
            :position="offset(h.posicion,0.99)"
            radius="1"
            side="double"
            material="color: orange; opacity:0.8"
            look-at="#camera"
            @click.stop="deleteHotspot(h.id)"
          ></a-circle>
        </template>

        {{-- 3.3.4) Punto provisional --}}
        <template x-if="adding && newPos">
          <a-circle
            :position="offset(newPos,0.99)"
            radius="1"
            side="double"
            material="color: lime; opacity:0.8"
            look-at="#camera"
          ></a-circle>
        </template>
      </a-scene>
    </div>

    {{-- 3.4) Tabla --}}
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
              <td class="px-6 py-4" x-text="h.elemento_nombre"></td>
              <td class="px-6 py-4" x-text="h.posicion"></td>
              <td class="px-6 py-4 text-right">
                <button @click="deleteHotspot(h.id)"
                        class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded text-sm">−</button>
              </td>
            </tr>
          </template>
          <template x-if="hotspots.length===0">
            <tr>
              <td colspan="3" class="px-6 py-4 text-center text-gray-500">No hay hotspots aún.</td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

  </main>

  {{-- 4) Lógica AlpineJS --}}
  <x-slot name="scripts">
    <script>
      function hotspotManager(){
        return {
          hotspots: JSON.parse(document.getElementById('hotspot-root').dataset.hotspots),
          adding: false,
          newPos: null,

          startAdd(){
            this.adding = true;
            this.newPos = null;
          },
          cancelAdd(){
            this.adding = false;
            this.newPos = null;
          },

          onClick(evt){
            if(!this.adding) return;
            const inter = evt.detail.intersection;
            if(!inter) return;
            const p = inter.point;
            this.newPos = `${p.x.toFixed(2)} ${p.y.toFixed(2)} ${p.z.toFixed(2)}`;
            this.confirmAdd();
          },

          offset(pos, factor){
            const [x,y,z] = pos.split(' ').map(Number);
            return `${(x*factor).toFixed(3)} ${(y*factor).toFixed(3)} ${(z*factor).toFixed(3)}`;
          },

          confirmAdd(){
            if(!this.newPos) return;
            fetch(`{{ url("panoramas/{$panorama->id}/hotspots") }}`, {
              method: 'POST',
              headers: {
                'Content-Type':'application/json',
                'X-CSRF-TOKEN':'{{ csrf_token() }}'
              },
              body: JSON.stringify({ elemento_id:null, posicion:this.newPos })
            }).then(res=>{
              if(!res.ok) throw new Error(res.status);
              location.reload();
            });
          },

          deleteHotspot(id){
            if(!confirm('¿Eliminar hotspot?')) return;
            fetch(`/hotspots/${id}`, {
              method:'DELETE',
              headers:{ 'X-CSRF-TOKEN':'{{ csrf_token() }}' }
            }).then(res=>{
              if(!res.ok) throw new Error(res.status);
              location.reload();
            });
          }
        }
      }
    </script>
  </x-slot>
</x-app-layout>

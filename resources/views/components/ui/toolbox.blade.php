@props([
  'title'       => '',
  'subtitle'    => null,
  'back'        => null,          // url o null
  'backLabel'   => 'Volver',
  'breadcrumbs' => null,          // [['label'=>'...', 'url'=>null|'/ruta']]
])

{{-- Barra superior consistente con el ancho de la app --}}
<div class="border-b border-slate-200 bg-white/70 backdrop-blur supports-[backdrop-filter]:bg-white/60">
  <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
    <div class="flex flex-col gap-3 py-4 sm:flex-row sm:items-center sm:justify-between">

      {{-- Izquierda: volver + título (+breadcrumb/subtítulo) --}}
      <div class="flex items-start gap-3">
        @if($back)
          <x-ui.btn-ghost href="{{ $back }}" class="shrink-0">
            <svg class="w-4 h-4 -ml-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            <span>{{ $backLabel }}</span>
          </x-ui.btn-ghost>
        @endif

        <div>
          @if(is_array($breadcrumbs) && count($breadcrumbs))
            <nav class="mb-1 text-xs text-slate-500" aria-label="Breadcrumb">
              <ol class="flex flex-wrap items-center gap-1">
                @foreach($breadcrumbs as $i => $bc)
                  @if($i)<span class="mx-1 text-slate-300">/</span>@endif
                  @if(!empty($bc['url']))
                    <a href="{{ $bc['url'] }}" class="hover:underline">{{ $bc['label'] }}</a>
                  @else
                    <span class="font-medium text-slate-700">{{ $bc['label'] }}</span>
                  @endif
                @endforeach
              </ol>
            </nav>
          @endif

          <h1 class="text-2xl font-bold text-slate-900">{{ $title }}</h1>
          @if($subtitle)
            <p class="text-sm text-slate-500">{{ $subtitle }}</p>
          @endif
        </div>
      </div>

      {{-- Derecha: acciones (solo si hay contenido en el slot) --}}
      @if(trim($slot) !== '')
        <div class="flex items-center gap-2">
          {{ $slot }}
        </div>
      @endif
    </div>
  </div>
</div>

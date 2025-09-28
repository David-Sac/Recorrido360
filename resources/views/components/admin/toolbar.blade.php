{{-- resources/views/components/admin/toolbar.blade.php --}}
@props([
  'title' => null,
  'back' => null,           // URL: route('dashboard') / url('/...')
  'backLabel' => 'Volver',
  'breadcrumbs' => [],      // [['label'=>'Dashboard','url'=>route('dashboard')], ['label'=>'Sección']]
])

@php
  $isLast = fn($i,$arr) => $i === count($arr)-1;
@endphp

<div class="sticky top-0 z-30 border-b bg-white/90 backdrop-blur border-slate-200">
  <div class="flex items-center justify-between gap-3 px-4 py-3 mx-auto max-w-7xl">
    <div class="flex items-center min-w-0 gap-3">
      @if($back)
        <a href="{{ $back }}"
           class="inline-flex items-center px-3 py-1.5 rounded-md bg-slate-900 text-white hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M15.5 19a1 1 0 0 1-.7-.29l-6-6a1 1 0 0 1 0-1.42l6-6A1 1 0 0 1 16.5 6l-5.29 5.29L16.5 16a1 1 0 0 1-.71 1.71H15.5z"/>
          </svg>
          <span class="ml-2">{{ $backLabel }}</span>
        </a>
      @endif>

      <div class="flex flex-col min-w-0">
        {{-- Breadcrumbs --}}
        @if(!empty($breadcrumbs))
          <nav class="flex items-center gap-2 text-sm truncate text-slate-500">
            @foreach($breadcrumbs as $i => $bc)
              @if(!empty($bc['url']) && !$isLast($i,$breadcrumbs))
                <a href="{{ $bc['url'] }}" class="hover:underline shrink-0">{{ $bc['label'] }}</a>
                <span class="shrink-0">›</span>
              @else
                <span class="font-medium truncate text-slate-800">{{ $bc['label'] }}</span>
              @endif
            @endforeach
          </nav>
        @endif

        {{-- Título --}}
        @if($title)
          <h1 class="text-lg font-semibold truncate text-slate-900">{{ $title }}</h1>
        @endif
      </div>
    </div>

    {{-- Acciones (slot a la derecha) --}}
    <div class="flex items-center gap-2">
      {{ $slot }}
    </div>
  </div>
</div>

@props(['href' => null, 'type' => 'button'])

@php
  $classes = 'inline-flex items-center justify-center gap-2 rounded-md px-3 py-1.5
              font-medium bg-white text-slate-700 border border-slate-300
              hover:bg-slate-50 transition focus:outline-none focus:ring-2
              focus:ring-slate-400 focus:ring-offset-1 disabled:opacity-60 disabled:pointer-events-none';
@endphp

@if ($href)
  <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
  </a>
@else
  <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
  </button>
@endif

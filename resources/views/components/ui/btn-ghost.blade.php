@props([
  'href' => null,
  'type' => null,
  'size' => 'md',
])

@php
  $base   = 'inline-flex items-center justify-center gap-2 rounded-md font-medium transition
             disabled:opacity-60 disabled:pointer-events-none
             focus:outline-none focus:ring-2 focus:ring-slate-300 focus:ring-offset-1';
  $colors = 'bg-transparent text-slate-700 hover:bg-slate-100';
  $sizes  = [
    'sm' => 'px-2.5 py-1.5 text-sm',
    'md' => 'px-3 py-1.5 text-sm',
    'lg' => 'px-4 py-2 text-base',
  ][$size] ?? 'px-3 py-1.5 text-sm';

  $classes = "{$base} {$colors} {$sizes}";
@endphp

@if ($href)
  <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
  </a>
@else
  <button type="{{ $type ?? 'button' }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
  </button>
@endif

@props([
  'href' => null,     // si viene, renderiza <a>; si no, <button>
  'type' => null,     // solo aplica cuando NO hay href
  'size' => 'md',     // sm | md | lg
])

@php
  $base   = 'inline-flex items-center justify-center gap-2 rounded-md font-medium transition
             disabled:opacity-60 disabled:pointer-events-none
             focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-1';
  $colors = 'bg-emerald-600 hover:bg-emerald-700 text-white';
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

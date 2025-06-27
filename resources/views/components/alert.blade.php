{{-- resources/views/components/alert.blade.php --}}
@props(['type'=>'success'])

@php
  $colors = [
    'success' => 'bg-green-500 text-white',
    'warning' => 'bg-yellow-400 text-black',
    'error'   => 'bg-red-500 text-white',
  ];
  // Mapea las claves de session a tipos
  $message = session($type) ?? null;
@endphp

@if($message)
  <div
    x-data="{ show: true }"
    x-init="setTimeout(() => show = false, 2000)"
    x-show="show"
    x-transition
    class="fixed inset-0 flex items-center justify-center z-50 px-4"
  >
    <div class="inline-flex items-center {{ $colors[$type] }} rounded-lg shadow-lg px-6 py-4 space-x-3">
      {{-- Icono según tipo --}}
      @if($type==='success')
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
      @elseif($type==='warning')
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 8v.01"/>
        </svg>
      @else
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      @endif

      <span class="flex-1">{{ $message }}</span>

    </div>
  </div>
@endif

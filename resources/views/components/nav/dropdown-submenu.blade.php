@props(['alignment' => 'right'])

@php
$alignmentStyle = [
'left' => 'left: 0px; top: 0em; transform:translateX(-100%);',
'right' => 'right: 0px; top: 0em; transform:translateX(100%);'
]
@endphp

<div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">

    <div class="cursor-pointer px-3 py-1 text-sm hover:bg-gray-300">
        {{ $nav_item }}
    </div>

    {{-- <div class="absolute z-25 bg-white text-black rounded shadow-md py-1 mt-0 w-auto" --}}
    <div class="absolute z-25 bg-white rounded shadow-md py-1 mt-0 w-auto"
        style="{{ $alignmentStyle[$alignment] }}";
        {{-- style="right: 0px; top: 0em; transform:translateX(100%);" --}}
        x-show="open"
        x-transition:enter="transition transform duration-300 ease-out"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:leave="transition transform duration-75 ease-in"
        x-transition:leave-end="opacity-100 scale-50">
        {{ $slot }}
    </div>

</div>
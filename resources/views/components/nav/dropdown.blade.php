@props(['alignment' => 'left'])

@php
$alignmentClasses = [
'left' => 'left-0',
'right' => 'right-0'
]
@endphp

<div class="relative" x-data="{ open: false }" @mouseleave="open = false">

    <div class="cursor-pointer hover:underline hover:text-cool-gray-300 pb-1" @mouseover="open = true">
        {{ $nav_item }}
    </div>

    <div class="absolute {{ $alignmentClasses[$alignment] }} z-20 bg-white text-black rounded shadow-md py-2 mt-0 w-60"
        x-show="open"
        x-transition:enter="transition transform duration-300 ease-out"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:leave="transition transform duration-75 ease-in"
        x-transition:leave-end="opacity-100 scale-50">
        {{ $slot }}
    </div>

</div>
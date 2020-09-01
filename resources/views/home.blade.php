<x-layout>

    <x-slot name='centred'>
        <div class="flex w-full flex-col justify-center items-center space-y-6">
            <a href="{{ route('home') }}">
                <x-logo class="w-auto h-16 mx-auto text-indigo-600" />
            </a>

            <h1 class="text-5xl font-extrabold tracking-wider text-center text-gray-600">
                <div>{{ config('app.name') }}</div>
                <div class='text-sm font-bold italic'>Version Alpha</div>
            </h1>
            @if ($currentProject)
            <h2 class="text-3xl font-bold text-center text-indigo-600">
                {{ $currentProject->project }}
            </h2>
            @endif

            @include('layouts.errormsg')

            {{-- <ul class="list-reset">
                    <li class="inline px-4">
                        <a href="https://tailwindcss.com"
                            class="font-medium text-indigo-600 hover:text-indigo-500 focus:outline-none focus:underline transition ease-in-out duration-150">Tailwind
                            CSS</a>
                    </li>
                    <li class="inline px-4">
                        <a href="https://github.com/alpinejs/alpine"
                            class="font-medium text-indigo-600 hover:text-indigo-500 focus:outline-none focus:underline transition ease-in-out duration-150">Alpine.js</a>
                    </li>
                    <li class="inline px-4">
                        <a href="https://laravel.com"
                            class="font-medium text-indigo-600 hover:text-indigo-500 focus:outline-none focus:underline transition ease-in-out duration-150">Laravel</a>
                    </li>
                </ul> --}}
        </div>
    </x-slot>

</x-layout>
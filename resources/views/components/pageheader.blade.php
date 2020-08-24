<div class="flex justify-between mb-2">
    <div class='flex space-x-10'>
        <div class='text-xl font-bold'>
            {{ $slot }}
        </div>
        {{ $secondary ?? ''}}
    </div>
    <div class='space-x-2'>
        {{ $button ?? ''}}
        {{ $button2 ?? ''}}
    </div>
</div>
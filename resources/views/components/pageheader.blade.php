<div class="flex justify-between mb-2">
    <div class='flex space-x-10'>
        <div class='text-xl font-bold'>
            {{ $slot }}
        </div>
        <div class='text-l font-bold'>
        {{ $secondary ?? ''}}
        </div>
    </div>
    <div class='space-x-2 ml-5'>
        {{ $button ?? ''}}
        {{ $button2 ?? ''}}
    </div>
</div>
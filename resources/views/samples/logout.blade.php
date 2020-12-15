<x-layout>
    <x-pageheader>
        Log Samples Out of Storage
    </x-pageheader>

    @include('layouts.message')

    {{ Form::open(['url' => '/sample/logout', 'class' => 'form', 'method' => 'PATCH']) }}
    {{ Form::text('barcode', null, ['placeholder'=>'Scan sample barcode...','autofocus'])}}
    {{ Form::close() }}
</x-layout>
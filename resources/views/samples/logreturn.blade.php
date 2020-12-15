<x-layout>
    <x-pageheader>
        Log Samples Return to Storage
    </x-pageheader>

    @include('layouts.message')

    {{ Form::open(['url' => '/sample/logreturn', 'class' => 'form', 'method' => 'PATCH']) }}
    {{ Form::text('barcode', null, ['placeholder'=>'Scan sample barcode...','autofocus'])}}
    {{ Form::close() }}
</x-layout>
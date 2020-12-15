<x-layout>
    <x-pageheader>
        Log Samples as Lost
    </x-pageheader>

    @include('layouts.message')

    {{ Form::open(['url' => '/sample/loglost', 'class' => 'form', 'method' => 'PATCH']) }}
    {{ Form::text('barcode', null, ['placeholder'=>'Scan sample barcode...','autofocus'])}}
    {{ Form::close() }}
</x-layout>
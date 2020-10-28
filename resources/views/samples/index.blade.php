<x-layout>
    <x-pageheader>
        Manage Samples
    </x-pageheader>

    @include('layouts.message')

    {{ Form::open(['url' => '/samples', 'class' => 'form', 'method' => 'GET']) }}
    {{ Form::text('barcode', null, ['placeholder'=>'Scan sample barcode...','autofocus'])}}
    {{ Form::close() }}
</x-layout>
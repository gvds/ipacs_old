<x-layout>
    <x-pageheader>
        Log Samples as Used
    </x-pageheader>

    @include('layouts.message')

    {{ Form::open(['url' => '/sample/logused', 'class' => 'form', 'method' => 'PATCH']) }}
    {{ Form::text('barcode', null, ['placeholder'=>'Scan sample barcode...','autofocus'])}}
    {{ Form::close() }}
</x-layout>
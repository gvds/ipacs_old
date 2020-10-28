<x-layout>

    <x-pageheader>
        Log Derivative Samples (by Parent)
    </x-pageheader>

    {{ Form::open(['url' => '/derivative/parent', 'class' => 'form', 'method' => 'post']) }}
    {{ Form::text('parent', null, ['placeholder'=>'Scan Parent barcode...','autocomplete'=>'off', 'autofocus']) }}
    {{ Form::close() }}

    @include('layouts.message')

</x-layout>
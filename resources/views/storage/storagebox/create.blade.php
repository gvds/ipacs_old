<x-layout>
    <x-pageheader>
        New Storage Box
    </x-pageheader>

    @include('layouts.message')

    {{ Form::open(['url' => '/storagebox', 'class' => 'form', 'method' => 'POST']) }}
    {{ Form::label('barcode', 'Box Barcode', ['class'=>'text-sm']) }}
    {{ Form::text('barcode', null, ['required']) }}
    {{ Form::label('sampletype_id', 'Sample Type', ['class'=>'text-sm']) }}
    {{ Form::select('sampletype_id', $sampletypes, null) }}
    {{ Form::label('positions', 'Box Positions', ['class'=>'text-sm']) }}
    {{ Form::text('positions', 68, ['required']) }}
    {{ Form::submit('Save Record', ['class' => "w-full mt-4"]) }}
    {{ Form::close() }}

</x-layout>

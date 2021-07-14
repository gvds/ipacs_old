<x-layout>
    <x-pageheader>
        Create New Storage Unit Definition
        <x-slot name='button'>
            <x-buttonlink href='/unitDefinition' class='text-orange-600'>Cancel</x-buttonlink>
        </x-slot>
    </x-pageheader>

    @include('layouts.message')

    {{ Form::open(['url' => '/unitDefinition', 'class' => 'form', 'method' => 'POST']) }}
    {{ Form::label('unitType', 'Unit Type Name', ['class'=>'text-sm']) }}
    {{ Form::text('unitType', null, ['required']) }}
    {{ Form::label('orientation', 'Orientation', ['class'=>'text-sm']) }}
    {{ Form::select('orientation', ['Chest'=>'Chest','Upright'=>'Upright'], ['required']) }}
    {{ Form::label('sectionLayout', 'Section Layout', ['class'=>'text-sm']) }}
    {{ Form::select('sectionLayout', ['Vertical'=>'Vertical','Horizontal'=>'Horizontal'], ['required']) }}
    {{ Form::label('boxDesignation', 'Box Designation', ['class'=>'text-sm']) }}
    {{ Form::select('boxDesignation', ['Alpha'=>'Alpha','Numeric'=>'Numeric'], ['required']) }}
    {{ Form::label('storageType', 'Storage Type', ['class'=>'text-sm']) }}
    {{ Form::select('storageType', ['Minus 80'=>'Minus 80','Liquid Nitrogen'=>'Liquid Nitrogen','Minus 20'=>'Minus 20','BiOS'=>'BiOS'], ['required']) }}
    {{ Form::label('rackOrder', 'Rack Order', ['class'=>'text-sm']) }}
    {{ Form::select('rackOrder', ['By Column'=>'By Column','By Row'=>'By Row'], ['required']) }}

    {{ Form::submit('Save Record', ['class' => "w-full mt-4"]) }}
    {{ Form::close() }}

</x-layout>

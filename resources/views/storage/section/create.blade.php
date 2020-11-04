<x-layout>
    <x-pageheader>
        Create New Unit Section for: {{$unitDefinition->unitType}}
        <x-slot name='button'>
            <x-buttonlink href='/unitDefinitions/{{$unitDefinition->id}}' class='text-orange-600'>Cancel</x-buttonlink>
        </x-slot>
    </x-pageheader>

    @include('layouts.message')

    {{ Form::open(['url' => '/sections', 'class' => 'form', 'method' => 'POST']) }}
    {{-- {{ Form::label('unitType', 'Unit Type Name', ['class'=>'text-sm']) }} --}}
    {{ Form::hidden('unitDefinition_id', $unitDefinition->id) }}
    {{ Form::hidden('section', $section) }}
    {{ Form::label('rows', 'Rows', ['class'=>'text-sm']) }}
    {{ Form::number('rows', 1, ['required','min'=>1]) }}
    {{ Form::label('columns', 'Columns', ['class'=>'text-sm']) }}
    {{ Form::number('columns', 1, ['required','min'=>1]) }}
    {{ Form::label('boxes', 'Boxes', ['class'=>'text-sm']) }}
    {{ Form::number('boxes', 1, ['required','min'=>1]) }}
    {{ Form::label('positions', 'Positions', ['class'=>'text-sm']) }}
    {{ Form::number('positions', 100, ['required','min'=>1]) }}

    {{ Form::submit('Save Record', ['class' => "w-full mt-4"]) }}
    {{ Form::close() }}

</x-layout>
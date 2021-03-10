<x-layout>
    <x-pageheader>
        Create New Physical Unit of type: {{$unitDefinition->unitType}}
        <x-slot name='button'>
            <x-buttonlink href='/unitDefinition/{{$unitDefinition->id}}' class='text-orange-600'>Cancel</x-buttonlink>
        </x-slot>
    </x-pageheader>

    @include('layouts.message')

    {{ Form::open(['url' => '/physicalUnit', 'class' => 'form', 'method' => 'POST']) }}
    {{ Form::hidden('unitDefinition_id', $unitDefinition->id) }}
    {{ Form::label('unitID', 'Unit ID', ['class'=>'text-sm']) }}
    {{ Form::text('unitID', null, ['required','maxlength'=>40]) }}
    {{ Form::label('user_id', 'Administrator', ['class'=>'text-sm']) }}
    {{ Form::select('user_id', $adminlist, ['required']) }}

    {{ Form::submit('Save Record', ['class' => "w-full mt-4"]) }}
    {{ Form::close() }}

</x-layout>

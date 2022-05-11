<x-layout>
    <x-pageheader>
        Edit Physical Unit: {{$physicalUnit->initID}}
        <x-slot name='button'>
            <x-buttonlink href='/unitDefinition/{{$physicalUnit->unitDefinition_id}}' class='text-orange-600'>Cancel</x-buttonlink>
        </x-slot>
    </x-pageheader>

    @include('layouts.message')

    {{ Form::model($physicalUnit, ['url' => "/physicalUnit/$physicalUnit->id", 'class' => 'form', 'method' => 'PATCH']) }}
    {{ Form::label('unitID', 'Unit ID', ['class'=>'text-sm']) }}
    {{ Form::text('unitID', null, ['required','maxlength'=>40]) }}
    {{ Form::label('serial', 'Serial Number', ['class'=>'text-sm']) }}
    {{ Form::text('serial', null, ['required','maxlength'=>40]) }}
    {{ Form::label('user_id', 'Administrator', ['class'=>'text-sm']) }}
    {{ Form::select('user_id', $adminlist, ['required']) }}

    {{ Form::submit('Save Record', ['class' => "w-full mt-4"]) }}
    {{ Form::close() }}

</x-layout>

<x-layout>
    <x-pageheader>
        New Arm
    </x-pageheader>

    @include('layouts.errormsg')

    {{ Form::open(['url' => 'arms', 'class'=>'form']) }}
    {{ Form::label('name', 'Arm Name') }}
    {{ Form::text('name', null, ['required'=>'required']) }}
    {{ Form::label('redcap_arm_id', 'REDCap Arm ID') }}
    {{ Form::text('redcap_arm_id', null) }}
    {{ Form::label('arm_num', 'Arm Number') }}
    {{ Form::text('arm_num', null, ['required'=>'required']) }}
    {{ Form::label('manual_enrole', 'Manual Enrole') }}
    {{ Form::radio('manual_enrole', 0, true, ['class'=>'mb-3']) }} No {{ Form::radio('manual_enrole', 1) }} Yes
    {{ Form::label('switcharms', 'Switch to Arms') }}
    @foreach ($arms as $arm)
    {{ Form::checkbox('switcharms[]', $arm->id) }} {{$arm->name}}<br />
    @endforeach
    {{ Form::submit('Save Record', ['class' => "w-full mt-2"]) }}
    <x-buttonlink href='/arms' class='text-orange-500'>Cancel</x-buttonlink>
    {{ Form::close() }}

</x-layout>
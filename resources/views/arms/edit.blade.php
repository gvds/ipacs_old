<x-layout>
    <x-pageheader>
        Edit Arm
    </x-pageheader>

    @include('layouts.errormsg')

    {{ Form::model($arm, ['route' => ['arms.update', $arm], 'method' => 'PATCH', 'class'=>'form']) }}
    {{ Form::label('name', 'Arm Name') }}
    {{ Form::text('name', null, ['required'=>'required']) }}
    {{ Form::label('redcap_arm_id', 'REDCap Arm ID') }}
    {{ Form::text('redcap_arm_id', null) }}
    {{ Form::label('arm_num', 'Arm Number') }}
    {{ Form::text('arm_num', null, ['required'=>'required']) }}
    {{ Form::label('manual_enrol', 'Manual Enrole') }}
    {{ Form::radio('manual_enrol', 0, true) }} No {{ Form::radio('manual_enrol', 1) }} Yes
    {{ Form::label('switcharms', 'Switch to Arms') }}
    @foreach ($arms as $arm)
    {{ Form::checkbox('switcharms[]', $arm->id) }} {{$arm->name}}<br />
    @endforeach
    {{ Form::submit('Save Changes', ['class' => "w-full mt-2"]) }}
    <x-buttonlink :href="url('/arms')" class='text-orange-500'>Cancel</x-buttonlink>
    {{ Form::close() }}

</x-layout>
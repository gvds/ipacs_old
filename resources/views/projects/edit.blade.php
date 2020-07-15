<x-layout>

    <x-pageheader>
        Edit Project
    </x-pageheader>

    @include('layouts.errormsg')
    {{ Form::model($project, ['url' => "/project/$project->id", 'method' => 'patch', 'class'=>'form']) }}
    {{ Form::label('project', 'Project Name') }}
    {{ Form::text('project', null, ['required','maxlength'=>'50']) }}
    {{ Form::label('redcapProject_id', 'REDCap Project') }}
    {{ Form::select('redcapProject_id', ['' => '', '1' => '1', '2' => '2', '3' => '3', '4' => '4']) }}
    {{ Form::label('owner', 'Owner', ['class'=>'text-sm']) }}
    {{ Form::select('owner', $users, null, ['required']) }}
    {{ Form::label('subject_id_prefix', 'SubjectID Prefix') }}
    {{ Form::text('subject_id_prefix', null, ['maxlength'=>'6']) }}
    {{ Form::label('subject_id_digits', 'SubjectID Digits') }}
    {{ Form::selectRange('subject_id_digits', 2, 6, 3) }}
    {{ Form::label('storageProjectName', 'Storage Project Name') }}
    {{ Form::text('storageProjectName', null, ['maxlength'=>'15']) }}
    {{ Form::label('label_id', 'Label Format') }}
    {{ Form::select('label_id', ['Large' => 'Large', 'Small' => 'Small']) }}
    {{ Form::submit('Save', ['class' => 'bg-gray-300 mt-2 font-bold hover:text-indigo-500']) }}
    <x-button href='/project' class='bg-gray-300 text-orange-500'>Cancel</x-button>
    {{ Form::close() }}

</x-layout>
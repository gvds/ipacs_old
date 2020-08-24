<x-layout>

    <x-pageheader>
        Edit REDCap Project
    </x-pageheader>

    @include('layouts.errormsg')
    {{ Form::model($project, ['url' => "/redcapproject/$project->id", 'method' => 'patch', 'class'=>'form']) }}
    {{ Form::label('project', 'Project Name') }}
    <div class='font-bold text-lg'>{{$project->project}}</div>
    {{ Form::label('owner', 'Owner', ['class'=>'text-sm']) }}
    {{ Form::select('owner', $users, $project->owner, ['required']) }}
    {{ Form::label('subject_id_prefix', 'SubjectID Prefix') }}
    {{ Form::text('subject_id_prefix', $project->subject_id_prefix, ['maxlength'=>'6']) }}
    {{ Form::label('subject_id_digits', 'SubjectID Digits') }}
    {{ Form::selectRange('subject_id_digits', 2, 6, $project->subject_id_digits) }}
    {{ Form::label('storageProjectName', 'Storage Project Name') }}
    {{ Form::text('storageProjectName', $project->storageProjectName, ['maxlength'=>'15']) }}
    {{ Form::label('label_id', 'Label Format') }}
    {{ Form::select('label_id', ['L7651_mod' => 'L7651_mod', 'L7651' => 'L7651'], $project->label_id) }}
    {{ Form::submit('Save', ['class' => 'w-full mt-2']) }}
    <x-buttonlink href='/project' class='text-orange-500'>Cancel</x-buttonlink>
    {{ Form::close() }}

</x-layout>
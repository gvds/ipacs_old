<x-layout>
    <x-pageheader>
        Edit Event
    </x-pageheader>

    @include('layouts.errormsg')

    {{ Form::model($event, ['route' => ['events.update', $event], 'method' => 'PATCH', 'class'=>'form']) }}
    {{ Form::label('name', 'Event Name') }}
    {{ Form::text('name', null, ['required'=>'required']) }}
    {{ Form::label('arm_id', 'Arm') }}
    {{ Form::select('arm_id', $arms) }}
    {{ Form::label('redcap_event_id', 'REDCap Event ID') }}
    {{ Form::text('redcap_event_id', null) }}
    {{ Form::label('autolog', 'Auto-Log') }}
    {{ Form::radio('autolog', 0, true, ['class'=>'mb-3']) }} No {{ Form::radio('autolog', 1) }} Yes
    {{ Form::label('offset', 'Offset') }}
    {{ Form::text('offset', null, ['required'=>'required']) }}
    {{ Form::label('offset_ante_window', 'Prior Offset Window') }}
    {{ Form::text('offset_ante_window', null, ['required'=>'required']) }}
    {{ Form::label('offset_post_window', 'Post Offset Window') }}
    {{ Form::text('offset_post_window', null, ['required'=>'required']) }}
    {{ Form::label('name_labels', 'Name Labels') }}
    {{ Form::text('name_labels', null) }}
    {{ Form::label('subject_event_labels', 'Study Event Labels') }}
    {{ Form::text('subject_event_labels', null) }}
    {{ Form::label('study_id_labels', 'Study ID Labels') }}
    {{ Form::text('study_id_labels', null) }}
    {{ Form::label('event_order', 'Event Order') }}
    {{ Form::text('event_order', null) }}
    {{ Form::label('active', 'Active') }}
    {{ Form::radio('active', 0, ['class'=>'mb-3']) }} No {{ Form::radio('active', 1, true) }} Yes
    {{ Form::submit('Save Changes', ['class' => "w-full mt-2"]) }}
    <x-buttonlink href='/events' class='text-orange-500'>Cancel</x-buttonlink>
    {{ Form::close() }}

</x-layout>
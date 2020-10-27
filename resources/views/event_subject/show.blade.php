<x-layout>

    <x-pageheader>
        Log Event Status
    </x-pageheader>

    @include('layouts.errormsg')

    {{ Form::open(['url' => '/event_subject/retrieve', 'class' => 'form', 'method' => 'GET']) }}
    {{ Form::text('pse', null, ['placeholder'=>'Scan PSE barcode...','autocomplete'=>'off', 'autofocus']) }}
    {{ Form::close() }}

    @if (isset($event_subject))
    <div class='bg-gray-100 border border-gray-300 rounded shadow-md p-4 w-max-content mt-5'>
        <x-table>
            <tr>
                <th class='text-left'>SubjectID</th>
                <td>{{$event_subject->subject->subjectID}}</td>
            </tr>
            <tr>
                <th class='text-left'>Event</th>
                <td>{{$event_subject->event->name}} [{{$event_subject->iteration}}]</td>
            </tr>
            <tr>
                <th class='text-left'>Scheduled</th>
                <td>{{$event_subject->eventDate}}</td>
            </tr>
            <tr>
                <th class='text-left'>Status</th>
                <td>{{$event_subject->status->eventstatus}}</td>
            </tr>
            <tr>
                <th class='text-left'>Logged</th>
                <td>{{$event_subject->logDate}}</td>
            </tr>
        </x-table>
        <div class='mb-5'>
            <x-buttonlink href='/subjects/{{$event_subject->subject_id}}'>Got to Subject Record</x-buttonlink>
        </div>
        {{ Form::open(['url' => "/event_subject/$event_subject->id", 'class' => 'max-w-xs text-sm', 'method' => 'POST']) }}
        {{ Form::date('logdate', \Carbon\Carbon::now(), ['required'])}}
        {{ Form::select('eventstatus', $eventstatuses, $event_subject->eventstatus_id) }}
        {{ Form::submit('Update Event Status', ['class'=>'w-full mt-2']) }}
        {{ Form::close() }}
    </div>
    @endif
</x-layout>
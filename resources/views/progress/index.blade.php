<x-layout>

    <x-pageheader>
        Progress Report
        {{ $subjects->links() }}
    </x-pageheader>


    <x-table>
        <x-slot name='head'>
            <th>ID</th>
            <th>Site</th>
            <th>Current Arm</th>
        </x-slot>
        @foreach ($subjects as $subject)
        <tr class='odd:bg-gray-200'>
            <td>{{$subject->subjectID}}</td>
            <td>{{$subject->site->name}}</td>
            <td>{{$subject->arm->name}}</td>
            @foreach ($subject->events as $event)
            <td>
                {{$event->arm->name}}<br />
                {{$event->name}}<br />
                {{$eventStatuses[$event->pivot->eventstatus_id]}}
            </td>
            @endforeach
        </tr>
        @endforeach
    </x-table>

</x-layout>

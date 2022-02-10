<x-layout>

    <x-pageheader>
        Progress Report
        {{-- <x-slot name='button'>
            <x-buttonlink href="project/create">
                New Project
            </x-buttonlink>
        </x-slot> --}}
        {{-- <x-slot name='button2'>
            <x-buttonlink href="redcapproject/new">
                New REDCap Project
            </x-buttonlink>
        </x-slot> --}}
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
            {{-- <table> --}}
                {{-- <tr> --}}
                    @foreach ($subject->events as $event)
                    <td>{{$event->arm->name}}<br />{{$event->name}}<br />{{$eventStatuses[$event->pivot->eventstatus_id]}}
                    </td>

                    @endforeach
                    {{--
                </tr> --}}
                {{-- </table> --}}
        </tr>
        @endforeach
    </x-table>

</x-layout>

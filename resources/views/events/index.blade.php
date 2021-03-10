<x-layout>
    <x-pageheader>
        Events
        <x-slot name='button'>
            <x-buttonlink href="events/create">
                Add New Event
            </x-buttonlink>
        </x-slot>
    </x-pageheader>

    <div x-data="deleteModal()">

        <x-table>
            <x-slot name='head'>
                <th>Name</th>
                <th>Arm</th>
                <th>Auto-log</th>
                <th>Offset</th>
                <th>Prior Window</th>
                <th>Post Window</th>
                <th>Name Labels</th>
                <th>Subject Event Labels</th>
                <th>Study ID Labels</th>
                <th>Repeatable</th>
                <th>Active</th>
            </x-slot>
            @foreach ($events as $event)
            <tr class='odd:bg-gray-100'>
                <td class='py-2'>{{$event->name}}</td>
                <td>{{$event->arm->name}}</td>
                <td>{{$event->autolog}}</td>
                <td>{{$event->offset}}</td>
                <td>{{$event->offset_ante_window}}</td>
                <td>{{$event->offset_post_window}}</td>
                <td>{{$event->name_labels}}</td>
                <td>{{$event->subject_event_labels}}</td>
                <td>{{$event->study_id_labels}}</td>
                <td>{{$event->repeatable}}</td>
                <td>{{$event->active}}</td>
                <td>
                    <x-buttonlink href="events/{{$event->id}}/edit">
                        Edit
                    </x-buttonlink>
                </td>
                <td>
                    <button class='bg-red-700 text-red-100 py-1 px-2 rounded-md font-bold'
                        @click="deleteconf('event','{{$event->name}}',{{$event->id}})">Delete</button>
                </td>
            </tr>
            @endforeach
        </x-table>

        <x-modals.deleteModal />
    </div>

</x-layout>

<x-layout>
    <x-pageheader>
        Events
        <x-slot name='button'>
            <x-buttonlink href="/events/create">
                Add New Event
            </x-buttonlink>
        </x-slot>
    </x-pageheader>

    <x-table>
        <x-slot name='head'>
            <th>Name</th>
            <th>Arm</th>
            <th>Auto-log</th>
            <th>Ofset</th>
            <th>Min Offset</th>
            <th>Max Offset</th>
            <th>Name Labels</th>
            <th>Subject Event Labels</th>
            <th>Study ID Labels</th>
            <th>Event Order</th>
            <th>Active</th>
        </x-slot>
        @foreach ($events as $event)
        <tr class='odd:bg-gray-100'>
            <td class='py-2'>{{$event->name}}</td>
            <td>{{$event->arm->name}}</td>
            <td>{{$event->autolog}}</td>
            <td>{{$event->offset}}</td>
            <td>{{$event->offset_min}}</td>
            <td>{{$event->offset_max}}</td>
            <td>{{$event->name_labels}}</td>
            <td>{{$event->subject_event_labels}}</td>
            <td>{{$event->study_id_labels}}</td>
            <td>{{$event->event_order}}</td>
            <td>{{$event->active}}</td>
            <td>
                <x-buttonlink href="/events/{{$event->id}}/edit">
                    Edit
                </x-buttonlink>
            </td>
            <td>
                <x-delConfirm url='/events/{{$event->id}}' />
            </td>
        </tr>
        @endforeach
    </x-table>

</x-layout>

<x-delConfirmScript />
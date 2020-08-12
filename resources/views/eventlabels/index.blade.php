<x-layout>

    {{ Form::open(['url' => '/labels', 'class' => '', 'method' => 'POST']) }}
    <x-pageheader>
        Event Label Queue
        {{ Form::submit('Clear Selected Labels',['class' => 'text-sm text-red-600']) }}
    </x-pageheader>

    @include('layouts.errormsg')
    <div x-data="{selectall: false}">
        <x-table>
            <tr>
                <td colspan="4">
                </td>
                <td>
                    <x-buttonlink href="#" @click="selectall=!selectall">
                        Select All
                    </x-buttonlink>
                </td>
            </tr>
            <x-slot name='head'>
                <th>subjectID</th>
                <th>Arm</th>
                <th>Event</th>
                <th>Date</th>
                <th>Clear</th>
            </x-slot>
            @foreach ($events as $event)
            <tr class='odd:bg-gray-100'>
                <td class='py-2'>{{$event->subjectID}}</td>
                <td>{{$event->armname}}</td>
                <td>{{$event->eventname}}</td>
                <td>{{$event->eventDate}}</td>
                <td class='text-center'>
                    {{ Form::checkbox('label_ids[]',$event->id, false, ['x-bind:checked'=>'selectall'])}}
                </td>
            </tr>
            @endforeach
        </x-table>
    </div>

    {{ Form::close() }}

</x-layout>

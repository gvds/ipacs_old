<x-layout>

    <x-pageheader>
        Manifests Received
    </x-pageheader>

    @include('layouts.message')

    <x-table class='mt-4'>
        <x-slot name='head'>
            <th>ID</th>
            <th>Created</th>
            <th>Creator</th>
            <th>Source</th>
            <th>Shipped</th>
            <th>Receiver</th>
            <th>Received</th>
        </x-slot>
        @foreach ($manifests as $manifest)
        <tr class='odd:bg-gray-200'>
            @if ($manifest->manifestStatus_id === 2)
            <td>
                <x-buttonlink href="/manifest/receive/{{$manifest->id}}">{{$manifest->id}}</x-buttonlink>
            </td>
            @else
            <td>{{$manifest->id}}</td>
            @endif
            <td>{{$manifest->created_at->format('Y-m-d')}}</td>
            <td>{{$manifest->user->fullname}}</td>
            <td>{{$manifest->source->name}}</td>
            <td>{{$manifest->shippedDate}}</td>
            <td>{{$manifest->receiver->fullname}}</td>
            <td>{{$manifest->receivedDate}}</td>
        </tr>
        @endforeach
    </x-table>

</x-layout>
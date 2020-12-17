<x-layout>

    <x-pageheader>
        Manifests
        {{-- <x-slot name='button'>
            <x-buttonlink href="/manifest/create">
                New Manifest
            </x-buttonlink>
        </x-slot> --}}
        {{-- <x-slot name='button2'>
            <x-buttonlink href="redcapproject/new">
                New REDCap Project
            </x-buttonlink>
        </x-slot> --}}
    </x-pageheader>

    @include('layouts.message')

    {{ Form::open(['url' => '/manifest', 'class' => 'form', 'method' => 'POST']) }}
    {{ Form::label('destinationSite_id', 'Destination')}}
    {{ Form::select('destinationSite_id', $sites, null)}}
    {{ Form::submit('Create New Manifest', ['class'=>'mt-4 w-full']) }}
    {{ Form::close() }}

    <x-table class='mt-4'>
        <x-slot name='head'>
            <th>ID</th>
            <th>Created</th>
            <th>Creator</th>
            <th>Destination</th>
            <th>Shipped</th>
        </x-slot>
        @foreach ($manifests as $manifest)
        <tr class='odd:bg-gray-200'>
            @if ($manifest->manifestStatus_id === 1)
            <td>
                <x-buttonlink href="/manifest/{{$manifest->id}}">{{$manifest->id}}</x-buttonlink>
            </td>
            @else
            <td>{{$manifest->id}}</td>
            @endif
            <td>{{$manifest->created_at->format('Y-m-d')}}</td>
            <td>{{$manifest->user->fullname}}</td>
            <td>{{$manifest->destination->name}}</td>
            <td>{{$manifest->shippedDate}}</td>
        </tr>
        @endforeach
    </x-table>

</x-layout>
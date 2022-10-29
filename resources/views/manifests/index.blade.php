<x-layout>

    <x-pageheader>
        Manifests
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
            <th>Received</th>
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
            <td>
                @if ($manifest->manifestStatus_id === 2)
                <div x-data="confirmation()">
                    {{ Form::open(['url' => "/manifest/$manifest->id/shipperLogReceived", 'class' => 'form', 'method' =>
                    'POST',
                    'x-on:submit.prevent'=>'finalise()', 'x-ref'=>'finaliseform']) }}
                    {{ Form::submit('Confirm Manifest Receipt',['class'=>'text-red-600']) }}
                    {{ Form::close() }}
                </div>
                @elseif ($manifest->manifestStatus_id === 3)
                {{$manifest->receivedDate}} ({{$manifest->receiver->fullname}})
                @endif
            </td>
            <td>
                <x-buttonlink href="/manifest/{{$manifest->id}}/itemlist" class='bg-blue-200'>Download</x-buttonlink>
            </td>
        </tr>
        @endforeach
    </x-table>

</x-layout>

<script>
    function confirmation() {
        return {
            finalise() {
                var response = confirm("Click OK to confirm receipt of manifest.\n\nNote that this step is irreversible!");
                if (response === true) {
                    this.$refs.finaliseform.submit();
                }
            }
        }
    }
</script>

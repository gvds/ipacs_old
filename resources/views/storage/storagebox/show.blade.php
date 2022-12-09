<x-layout>
    <x-pageheader>
        Storage Box Details
    </x-pageheader>

    @include('layouts.message')

    <div>
        <x-table>
            <tr>
                <td>Barcode</td>
                <td>{{$storagebox->barcode}}</td>
            </tr>
            <tr>
                <td>Sample Type</td>
                <td>{{$storagebox->sampletype->name}}</td>
            </tr>
            <tr>
                <td>Positions</td>
                <td>{{$storagebox->positions}}</td>
            </tr>
            <tr>
                <td>Free</td>
                <td>{{$storagebox->positions - $used}}</td>
            </tr>
            @if ($used===0)
            <tr>
                <td>
                    <form action="/storagebox/{{$storagebox->id}}" method='POST'>
                        @csrf
                        @method('DELETE')
                        <x-button class='bg-red-500 text-red-50'>Delete
                        </x-button>
                    </form>
                </td>
            </tr>
            @endif
        </x-table>
    </div>
    <div>
        <x-table>
            <x-slot name='head'>
                <th>Position</th>
                <th>barcode</th>
            </x-slot>
            @foreach ($storagebox->boxPositions as $position)
            <tr>
                <td>{{$position->position}}</td>
                <td class='text-center'>{{$position->barcode ?? '-'}}</td>
            </tr>
            @endforeach
        </x-table>
    </div>

</x-layout>

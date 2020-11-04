<x-layout>
    <x-pageheader>
        Manage Virtual Storage Units
    </x-pageheader>

    @include('layouts.message')

    <x-table>
        <x-slot name='head'>
            <th>Unit Name</th>
            <th>Storage Type</th>
            <th>Unit Type</th>
            <th>Available</th>
            <th>Virtual Units</th>
        </x-slot>
        @foreach ($physicalUnits as $physicalUnit)
        <tr class="odd:bg-gray-100">
            <td>{{$physicalUnit->unitID}}</td>
            <td>{{$physicalUnit->storageType}}</td>
            <td>{{$physicalUnit->unitType}}</td>
            <td>
                @if ($physicalUnit->available)
                <svg class="h-5 w-5 text-green-600 bg-gray-200 p-1 border rounded shadow" fill="none" viewBox="0 0 20 20"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                @else
                <svg class="h-5 w-5 text-red-600 bg-gray-200 p-1 border rounded shadow" fill="none" viewBox="0 0 20 20"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                @endif
            </td>
            <td><x-buttonlink href='/physicalUnits/{{$physicalUnit->id}}'>-></x-buttonlink></td>
        </tr>
        @endforeach
    </x-table>

</x-layout>
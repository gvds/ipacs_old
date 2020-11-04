<x-layout>
    <x-pageheader>
        Manage Virtual Storage Units
    </x-pageheader>

    @include('layouts.message')

    <x-table>
        <x-slot name='head'>
            <th>Unit Name</th>
            <th>Type</th>
            <th>Available</th>
            <th>Virtual Units</th>
        </x-slot>
        @foreach ($physicalUnits as $physicalUnit)
        <tr class="odd:bg-gray-100">
            <td>{{$physicalUnit->unitID}}</td>
            <td>{{$physicalUnit->unitType->unitType}}</td>
            <td>{{$physicalUnit->available ? 'True' : 'False'}}</td>
            <td><x-buttonlink href='/physicalUnits/{{$physicalUnit->id}}'>-></x-buttonlink></td>
        </tr>
        @endforeach
    </x-table>

</x-layout>
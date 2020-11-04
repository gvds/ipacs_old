<x-layout>
    <x-pageheader>
        Manage Virtual Storage Units
        {{-- <x-slot name='button'>
            <x-buttonlink href='/unitDefinitions/create'>Add New Type</x-buttonlink>
        </x-slot> --}}
    </x-pageheader>

    @include('layouts.message')

    <x-table>
        <x-slot name='head'>
            <th>Unit Name</th>
            <th>Project</th>
            <th>Section</th>
            <th>Start Rack</th>
            <th>End Rack</th>
            <th>Start Box</th>
            <th>End Box</th>
            <th>Sample Type</th>
            <th>Rack Capacity</th>
            <th>Box Capacity</th>
            <th>Active</th>
        </x-slot>
        @foreach ($virtualUnits as $virtualUnit)
        <tr class="odd:bg-gray-100">
            <td>{{$virtualUnit->virtualUnit}}</td>
            <td>{{$virtualUnit->project}}</td>
            <td>{{$virtualUnit->sections}}</td>
            <td>{{$virtualUnit->startRack}}</td>
            <td>{{$virtualUnit->endRack}}</td>
            <td>{{$virtualUnit->startBox}}</td>
            <td>{{$virtualUnit->endBox}}</td>
            <td>{{$virtualUnit->sampleType}}</td>
            <td>{{$virtualUnit->rackCapacity}}</td>
            <td>{{$virtualUnit->boxCapacity}}</td>
            <td>{{$virtualUnit->active}}</td>
        </tr>
        @endforeach
    </x-table>

</x-layout>
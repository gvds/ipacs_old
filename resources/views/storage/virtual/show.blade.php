<x-layout>
    <x-pageheader>
        Manage Virtual Storage Units
        {{-- <x-slot name='button'>
            <x-buttonlink href='/unitDefinition/create'>Add New Type</x-buttonlink>
        </x-slot> --}}
    </x-pageheader>

    @include('layouts.message')

    <x-table>
        <tr>
            <th>Unit Name</th>
            <td>{{$virtualUnit->virtualUnit}}</td>
        </tr>
        <tr>
            <th>Project</th>
            <td>{{$virtualUnit->project}}</td>
        </tr>
        <tr>
            <th>Section</th>
            <td>{{$virtualUnit->section}}</td>
        </tr>
        <tr>
            <th>Start Rack</th>
            <td>{{$virtualUnit->startRack}}</td>
        </tr>
        <tr>
            <th>End Rack</th>
            <td>{{$virtualUnit->endRack}}</td>
        </tr>
        <tr>
            <th>Start Box</th>
            <td>{{$virtualUnit->startBox}}</td>
        </tr>
        <tr>
            <th>End Box</th>
            <td>{{$virtualUnit->endBox}}</td>
        </tr>
        <tr>
            <th>Sample Type</th>
            <td>{{$virtualUnit->storageSampleType}}</td>
        </tr>
        <tr>
            <th>Rack Capacity</th>
            <td>{{$virtualUnit->rackCapacity}}</td>
        </tr>
        <tr>
            <th>Box Capacity</th>
            <td>{{$virtualUnit->boxCapacity}}</td>
        </tr>
        <tr>
            <th>Active</th>
            <td>{{$virtualUnit->active}}</td>
        </tr>
        <tr>
            <td>
                <x-buttonlink href='/physicalUnit/{{$virtualUnit->physicalUnit_id}}'>
                    Return
                </x-buttonlink>
            </td>
        </tr>
    </x-table>


</x-layout>

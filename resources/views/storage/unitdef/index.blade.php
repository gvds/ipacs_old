<x-layout>
    <x-pageheader>
        Manage Storage Unit Definitions
        <x-slot name='button'>
            <x-buttonlink href='/unitDefinition/create'>Add New Type</x-buttonlink>
        </x-slot>
    </x-pageheader>

    @include('layouts.message')

    <x-table>
        <x-slot name='head'>
            <th>Unit Type</th>
            <th>Sections</th>
            <th>Section Layout</th>
            <th>Box Designation</th>
            <th>Storage Type</th>
            <th>Rack Order</th>
            <th>Orientation</th>
        </x-slot>
        @foreach ($unitDefinitions as $unitDefinition)
        <tr class="odd:bg-gray-100">
            <td>
                <x-buttonlink href='/unitDefinition/{{$unitDefinition->id}}'>{{$unitDefinition->unitType}}
                </x-buttonlink>
            </td>
            <td>{{count($unitDefinition->sections)}}</td>
            <td>{{$unitDefinition->sectionLayout}}</td>
            <td>{{$unitDefinition->boxDesignation}}</td>
            <td>{{$unitDefinition->storageType}}</td>
            <td>{{$unitDefinition->rackOrder}}</td>
            <td>{{$unitDefinition->orientation}}</td>
        </tr>
        @endforeach
    </x-table>

</x-layout>

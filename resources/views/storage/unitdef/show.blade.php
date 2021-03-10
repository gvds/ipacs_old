<x-layout>
    <x-pageheader>
        Unit Definition for: {{$unitDefinition->unitType}}
        <x-slot name='button'>
            <x-buttonlink href='/unitDefinitions'>Show All Types</x-buttonlink>
        </x-slot>
    </x-pageheader>

    @include('layouts.message')

    <div class='flex flex-row' x-data="deleteModal()">
        <div>
            <x-table>
                <tr>
                    <th class='text-left'>Unit Type</th>
                    <td>{{$unitDefinition->unitType}}</td>
                </tr>
                <tr>
                    <th class='text-left'>Orientation</th>
                    <td>{{$unitDefinition->orientation}}</td>
                </tr>
                <tr>
                    <th class='text-left'>Sections</th>
                    <td>{{count($sections)}}</td>
                </tr>
                <tr>
                    <th class='text-left'>Racks</th>
                    <td>{{$unitDefinition->racks}}</td>
                </tr>
                <tr>
                    <th class='text-left'>Boxes</th>
                    <td>{{$unitDefinition->boxes}}</td>
                </tr>
                <tr>
                    <th class='text-left'>Section Layout</th>
                    <td>{{$unitDefinition->sectionLayout}}</td>
                </tr>
                <tr>
                    <th class='text-left'>Box Designation</th>
                    <td>{{$unitDefinition->boxDesignation}}</td>
                </tr>
                <tr>
                    <th class='text-left'>Storage Type</th>
                    <td>{{$unitDefinition->storageType}}</td>
                </tr>
                <tr>
                    <th class='text-left'>Rack Order</th>
                    <td>{{$unitDefinition->rackOrder}}</td>
                </tr>
            </x-table>
            <div>
                <button class='bg-red-700 text-red-100 py-1 px-2 rounded-md font-bold'
                    @click="deleteconf('unitDefinitions','{{$unitDefinition->unitType}}',{{$unitDefinition->id}})">Delete</button>
            </div>
        </div>
        <div class='flex flex-col ml-20'>
            <div class='font-medium text-lg'>Sections</div>
            <x-table>
                <x-slot name='head'>
                    <th>Section</th>
                    <th>Rows</th>
                    <th>Columns</th>
                    <th>Boxes</th>
                    <th>Positions</th>
                </x-slot>
                @foreach ($sections as $section)
                <tr class="odd:bg-gray-100">
                    <td>{{$section->section}}</td>
                    <td>{{$section->rows}}</td>
                    <td>{{$section->columns}}</td>
                    <td>{{$section->boxes}}</td>
                    <td>{{$section->positions}}</td>
                    <td>
                        <button class='bg-red-700 text-red-100 py-1 px-2 rounded-md font-bold'
                            @click="deleteconf('sections','{{$section->section}}',{{$section->id}})">Delete</button>
                    </td>
                </tr>
                @endforeach
            </x-table>
            {{ Form::open(['url' => '/sections/create', 'class' => '', 'method' => 'GET']) }}
            {{ Form::hidden('unitDefinition_id', $unitDefinition->id) }}
            {{ Form::submit('Add New Section', ['class'=>'text-sm']) }}
            {{ Form::close() }}
        </div>
        <div class='flex flex-col ml-20'>
            <div class='font-medium text-lg'>Physical Storage Units</div>
            <x-table>
                <x-slot name='head'>
                    <th>Unit ID</th>
                    <th>Administrator</th>
                    <th>Available</th>
                </x-slot>
                @foreach ($physicalUnits as $physicalUnit)
                <tr class="odd:bg-gray-100">
                    <td>{{$physicalUnit->unitID}}</td>
                    <td>{{$physicalUnit->administrator->fullname}}</td>
                    <td>
                        <a href="/physicalUnits/{{$physicalUnit->id}}/toggleActive">
                            @if ($physicalUnit->available)
                            <svg class="h-6 w-6 text-green-600 bg-gray-200 p-1 border rounded shadow" fill="none"
                                viewBox="0 0 20 20" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            @else
                            <svg class="h-6 w-6 text-red-600 bg-gray-200 p-1 border rounded shadow" fill="none"
                                viewBox="0 0 20 20" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            @endif
                        </a>
                    </td>
                    <td>
                        <button class='bg-red-700 text-red-100 py-1 px-2 rounded-md font-bold'
                            @click="deleteconf('physicalUnits','{{$physicalUnit->unitID}}',{{$physicalUnit->id}})">Delete</button>
                    </td>
                </tr>
                @endforeach
            </x-table>
            {{ Form::open(['url' => '/physicalUnits/create', 'class' => '', 'method' => 'GET']) }}
            {{ Form::hidden('unitDefinition_id', $unitDefinition->id) }}
            {{ Form::submit('Add New Unit', ['class'=>'text-sm']) }}
            {{ Form::close() }}
        </div>
        <x-modals.deleteModal />
    </div>


</x-layout>

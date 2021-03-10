<x-layout>
    <x-pageheader>
        Manage Virtual Storage in: {{$physicalUnit->unitID}} [Type: {{$physicalUnit->unitType->unitType}}]
    </x-pageheader>

    @include('layouts.message')

    <div class='flex-row'>
        <div class='flex justify-between'>
            <div x-data="storagedata()"
                x-init="initialise({{$physicalUnit->id}},{{$physicalUnit->unitType}},{{$physicalUnit->unitType->sections}})">
                {{ Form::open(['url' => '/virtualUnit', 'class' => 'form', 'method' => 'POST']) }}
                {{ Form::hidden('physicalUnit_id', $physicalUnit->id) }}
                {{ Form::label('project_id', 'Project', ['class'=>'text-sm']) }}
                {{ Form::select('project_id', $projects, null, ['x-model'=>'project_id','x-on:change'=>"getSampletypes()",'required']) }}
                {{ Form::label('storageSampleType', 'Sample Type', ['class'=>'text-sm']) }}
                <select name="storageSampleType" id="sampleType" "required">
                    <template x-for="[id, value] in Object.entries(sampletypes)" :key="id">
                        <option :value="value" x-text="value"></option>
                    </template>
                </select>
                {{ Form::label('virtualUnit', 'Unit Name', ['class'=>'text-sm']) }}
                {{ Form::text('virtualUnit',null,['required']) }}
                <div class='flex flex-col justify-between'>
                    <div class='flex flex-row justify-between items-center space-x-4'>
                        {{ Form::label('section', 'Section', ['class'=>'text-sm']) }}
                        {{ Form::label('startRack', 'Start Rack', ['class'=>'text-sm']) }}
                        {{ Form::label('endRack', 'End Rack', ['class'=>'text-sm']) }}
                    </div>
                    <div class='flex flex-row justify-between space-x-4'>
                        {{ Form::selectRange('section', 1, count($physicalUnit->unitType->sections),null,['x-model'=>'section_number','x-on:change'=>'getSection()']) }}
                        <select name="startRack" id='startRack'>
                            <template x-for="id in racks" :key="id">
                                <option :value="id" x-text="id"></option>
                            </template>
                        </select>
                        <select name="endRack" id='endRack'>
                            <template x-for="id in racks" :key="id">
                                <option :value="id" x-text="id"></option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class='flex flex-row justify-between space-x-4'>
                    {{ Form::label('rackCapacity', 'Boxes per Rack', ['class'=>'text-sm']) }}
                    {{ Form::label('boxCapacity', 'Positions per Box', ['class'=>'text-sm']) }}
                </div>
                <div class='flex flex-row justify-between space-x-4'>
                    {{ Form::text('rackCapacity', null, ['size'=>8,'x-model'=>'sections[section_number-1].boxes']) }}
                    {{ Form::text('boxCapacity', null, ['size'=>8, 'x-model'=>'sections[section_number-1].positions']) }}
                </div>
                <div class='flex flex-row justify-between items-center'>
                    <div class='flex flex-col items-center justify-between'>
                        <div class='flex flex-row justify-between space-x-4'>
                            {{ Form::label('partial', 'Partial', ['class'=>'text-sm']) }}
                        </div>
                        <div class='flex flex-row justify-between space-x-4'>
                            {{ Form::checkbox('partial',true,false,['x-model'=>'partial']) }}
                        </div>
                    </div>
                    <div>
                        <div class='flex flex-row justify-between space-x-4'>
                            {{ Form::label('startBox', 'Start Box', ['class'=>'text-sm']) }}
                            {{ Form::label('endBox', 'End Box', ['class'=>'text-sm']) }}
                        </div>
                        <div class='flex flex-row justify-between space-x-4'>
                            <select name="startBox" id='startBox' x-bind:disabled="!partial">
                                <template x-for="id in boxes" :key="id">
                                    <option :value="id" x-text="id"></option>
                                </template>
                            </select>
                            <select name="endBox" id='endBox' x-bind:disabled="!partial">
                                <template x-for="id in boxes" :key="id">
                                    <option :value="id" x-text="id"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>

                {{ Form::submit('Create Virtual Unit', ['class'=>'w-full mt-4']) }}
                {{ Form::close() }}
            </div>
            @php
            $rackCount = 0;
            switch ($physicalUnit->unitType->sectionLayout) {
            case 'Vertical':
            $fclass = 'flex-col';
            break;
            default:
            $fclass = 'flex-row';
            break;
            }
            @endphp
            <div class='flex {{$fclass}} ml-4'>
                @foreach ($physicalUnit->unitType->sections as $section)
                <div class='mr-4 mb-4'>
                    <div class='font-medium'>Section {{$section->section}}</div>
                    <x-table>
                        <x-slot name='head'>
                            <th class='bg-gray-700 text-white px-2 py-1'></th>
                            @for ($c = 1; $c <= $section->columns; $c++)
                                <th class='bg-gray-700 text-white px-2 py-1'>{{$c}}</th>
                                @endfor
                        </x-slot>
                        @for ($r = 1; $r <= $section->rows; $r++)
                            <tr>
                                <th class='bg-gray-700 text-white border border-l-0 border-blue-800 px-2 py-1'>{{$r}}
                                </th>
                                @for ($c = 1; $c <= $section->columns; $c++)
                                    @php
                                    if ($physicalUnit->unitType->rackOrder == 'Row-wise') {
                                    $rack = $section->columns * ($r-1) + $c + $rackCount;
                                    } else {
                                    $rack = $section->rows * ($c - 1) + $r + $rackCount;
                                    }
                                    if ($racks[$rack] === 1) {
                                    $class = 'bg-blue-700 text-white';
                                    } elseif ($racks[$rack] === 2) {
                                    $class = 'cursor-pointer bg-blue-300';
                                    } else {
                                    $class = 'cursor-pointer';
                                    }
                                    @endphp
                                    <td class='border border-gray-700 px-2 py-1 {{$class}}'>
                                        {{$rack}}
                                    </td>
                                    @endfor
                            </tr>
                            @endfor
                    </x-table>
                </div>
                @php
                $rackCount += $section->columns * $section->rows;
                @endphp
                @endforeach
            </div>
        </div>
        <div class='mt-5' x-data="deleteModal()">
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
                    <td class='text-center'>{{$virtualUnit->section}}</td>
                    <td class='text-center'>{{$virtualUnit->startRack}}</td>
                    <td class='text-center'>{{$virtualUnit->startBox ? 'Partial' : $virtualUnit->endRack}}</td>
                    <td class='text-center'>{{$virtualUnit->startBox ?? '-'}}</td>
                    <td class='text-center'>{{$virtualUnit->endBox?? '-'}}</td>
                    <td>{{$virtualUnit->storageSampleType}}</td>
                    <td class='text-center'>{{$virtualUnit->rackCapacity}}</td>
                    <td class='text-center'>{{$virtualUnit->boxCapacity}}</td>
                    <td>
                        <a href="/virtualUnit/{{$virtualUnit->id}}/toggleActive">
                            @if ($virtualUnit->active)
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
                            @click="deleteconf('virtualunit','{{$virtualUnit->virtualUnit}}',{{$virtualUnit->id}})">Delete</button>
                    </td>
                </tr>
                @endforeach
            </x-table>
            <x-modals.deleteModal />
        </div>
    </div>

</x-layout>

<script>
    function storagedata() {
        return {
            physicalUnit: null,
            unitDefinition: {},
            project_id: null,
            section_number: 1,
            sampletypes: {},
            sections: {},
            boxes: [],
            racks: [],
            partial: false,
            initialise(physicalUnit,unitDefinition,sections){
                this.physicalUnit = physicalUnit;
                this.unitDefinition = unitDefinition;
                this.sections = sections;
                this.getSection();
            },
            getSampletypes() {
                fetch('/project/stypes?id=' + this.project_id)
                .then(response => response.json())
                .then(data => {
                    this.sampletypes = data;
                });
            },
            getSection() {
                if (this.unitDefinition.boxDesignation == 'Alpha') {
                this.boxes = Array(this.sections[this.section_number-1].boxes).fill().map((_, i) => String.fromCharCode('A'.charCodeAt(0) + i));
                // this.boxes =Array.from({ length: this.section.boxes }, (_, i) => String.fromCharCode('A'.charCodeAt(0) + i));
                // this.boxes =[...Array(this.section.boxes)].map((_, i) => String.fromCharCode('A'.charCodeAt(0) + i));
                } else {
                this.boxes = Array(this.sections[this.section_number-1].boxes).fill().map((_, i) => i + 1);
                // this.boxes = Array.from({length: this.section.boxes}, (_, i) => i + 1);
                }
                let rackoffset = 0;
                for (let index = 0; index < this.section_number-1; index++) {
                    rackoffset += this.sections[index].rows * this.sections[index].columns;
                }
                const range = (start, stop) => Array.from({ length: (stop - start) + 1}, (_, i) => start + i);
                this.racks = range(rackoffset + 1, rackoffset + this.sections[this.section_number-1].rows * this.sections[this.section_number-1].columns);
                this.partial = false;
            },
        }
    }
</script>

<x-layout>
    <x-pageheader>
        Manage Virtual Storage in: {{$physicalUnit->unitID}} [Type: {{$physicalUnit->unitType->unitType}}]
    </x-pageheader>

    @include('layouts.message')

    <div class='flex-row'>
        <div class='flex justify-between' x-data="storagedata()"
            x-init="initialise({{$physicalUnit->unitType}},{{$physicalUnit->unitType->sections}},{{$virtualUnits}})">
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
            {{ Form::label('virtualUnit', 'Virtual Unit Name', ['class'=>'text-sm']) }}
            {{ Form::text('virtualUnit',null,['required']) }}
            <div class='flex flex-col justify-between'>
                <div class='flex flex-row justify-between items-center space-x-4'>
                    {{ Form::label('section', 'Section', ['class'=>'text-sm']) }}
                    {{ Form::label('startRack', 'Start Rack', ['class'=>'text-sm']) }}
                    {{ Form::label('endRack', 'End Rack', ['class'=>'text-sm']) }}
                </div>
                <div class='flex flex-row justify-between space-x-4'>
                    {{ Form::text('section', null, ['x-model'=>'section_number','class'=>'bg-gray-200','readonly'])}}
                    {{ Form::text('startRack', null, ['x-model'=>'startRack','class'=>'bg-gray-200','readonly'])}}
                    {{ Form::text('endRack', null, ['x-model'=>'endRack','class'=>'bg-gray-200','readonly'])}}
                </div>
            </div>
            <div class='flex flex-row justify-between space-x-4'>
                {{ Form::label('rackCapacity', 'Boxes per Rack', ['class'=>'text-sm']) }}
                {{ Form::label('boxCapacity', 'Positions per Box', ['class'=>'text-sm']) }}
            </div>
            <div class='flex flex-row justify-between space-x-4'>
                {{ Form::text('rackCapacity', null, ['size'=>8,
                'x-model.number'=>'sections[section_number-1].boxes',
                "x-bind:readonly"=>"partial_lock==true && selection_type=='partial'",
                'x-on:change'=>'getSection()']) }}
                {{ Form::text('boxCapacity', null, ['size'=>8, 'x-model.number'=>'sections[section_number-1].positions']) }}
            </div>
            <div class='flex flex-row justify-between items-center'>
                <div class='flex flex-col items-center justify-between'>
                    <div class='flex flex-row justify-between space-x-4'>
                        {{ Form::label('selection_type', 'Partial', ['class'=>'text-sm']) }}
                    </div>
                    <div class='flex flex-row justify-between space-x-4'>
                        <input type='radio' name='selection_type' value='full' x-model='selection_type'
                            :disabled="partial_lock==true && selection_type=='partial'" x-on:change='boxSelectionReset()' checked />No
                        <input type='radio' , name='selection_type' value='partial' x-model='selection_type'
                            :disabled="partial_lock==true && selection_type=='full'" />Yes
                    </div>
                </div>
                <div>
                    <div class='flex flex-row justify-between space-x-4'>
                        {{ Form::label('startBox', 'Start Box', ['class'=>'text-sm']) }}
                        {{ Form::label('endBox', 'End Box', ['class'=>'text-sm']) }}
                    </div>
                    <div class='flex flex-row justify-between space-x-4'>
                        <select name="startBox" id='startBox' x-model.number='startBox' x-bind:disabled="selection_type=='full'"
                            x-on:change='boxSelectionCheck()'>
                            <template x-for="(box, id) in boxes" :key="id">
                                <option :value="id" x-text="box" x-bind:disabled="boxes_disabled[id]"></option>
                            </template>
                        </select>
                        <select name="endBox" id='endBox' x-model.number='endBox' x-bind:disabled="selection_type=='full'"
                            x-on:change='boxSelectionCheck()'>
                            <template x-for="(box, id) in boxes" :key="id">
                                <option :value="id" x-text="box" x-bind:disabled="boxes_disabled[id]"></option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>

            {{ Form::submit('Create Virtual Unit', ['class'=>'w-full mt-4']) }}
            {{ Form::close() }}
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
            <div class='flex {{$fclass}} ml-4 mb-4'>
                @foreach ($physicalUnit->unitType->sections as $section)
                <div class='mr-2'>
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
                                    if ($physicalUnit->unitType->rackOrder == 'By Row') {
                                    $rack = $section->columns * ($r - 1) + $c + $rackCount;
                                    } else {
                                    $rack = $section->rows * ($c - 1) + $r + $rackCount;
                                    }
                                    @endphp
                                    <td class='border border-gray-700 px-2 py-1 cursor-pointer' :class="{'bg-white':selectedracks[{{$rack-1}}]==0,
                                        'bg-green-600':selectedracks[{{$rack-1}}]&1,
                                        'bg-blue-700 text-white':selectedracks[{{$rack-1}}]==2,
                                        'bg-blue-300':selectedracks[{{$rack-1}}]==4}" x-on:click='rackselect({{$rack}})'>
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
                            <svg class="h-6 w-6 text-green-600 bg-gray-200 p-1 border rounded shadow" fill="none" viewBox="0 0 20 20"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            @else
                            <svg class="h-6 w-6 text-red-600 bg-gray-200 p-1 border rounded shadow" fill="none" viewBox="0 0 20 20"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            @endif
                        </a>
                    </td>
                    <td>
                        <x-buttonlink href='/storageconsolidation?virtualunit={{$virtualUnit->id}}'
                            class='bg-blue-900 text-blue-100 py-1 px-2 rounded-md font-bold'>Consolidation</x-buttonlink>
                        {{-- <x-buttonlink href='/virtualUnit/{{$virtualUnit->id}}/consolidate'
                            class='bg-blue-900 text-blue-100 py-1 px-2 rounded-md font-bold'>
                            Consolidate</x-buttonlink> --}}
                    </td>
                    <td>
                        <button class='bg-red-700 text-red-100 py-1 px-2 rounded-md font-bold'
                            @click="deleteconf('virtualUnit','{{$virtualUnit->virtualUnit}}',{{$virtualUnit->id}})">Delete</button>
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
            unitDefinition: {},
            project_id: null,
            section_number: 1,
            sampletypes: {},
            sections: {},
            virtualUnits: {},
            boxes: [],
            boxes_disabled: [],
            positions: null,
            selectedracks: [],
            startRack: null,
            endRack: null,
            startBox: 0,
            endBox: 0,
            selection_type: 'full',
            partial_lock: true,
            initialise(unitDefinition,sections,virtualUnits){
                this.unitDefinition = unitDefinition;
                this.sections = sections;
                this.initSections();
                this.virtualUnits = virtualUnits;
                this.setupRacks();
                this.getSection();
            },
            getSampletypes() {
                fetch('/project/stypes?id=' + this.project_id)
                .then(response => response.json())
                .then(data => {
                    this.sampletypes = data;
                });
            },
            initSections() {
                previousEndRack = null;
                this.sections.forEach(section => {
                    section.racks = section.rows * section.columns;
                    if (!previousEndRack) {
                        section.startRack = 0;
                        section.endRack = section.rows * section.columns;
                        previousEndRack = section.endRack;
                    } else {
                        section.startRack = previousEndRack + 1;
                        section.endRack = previousEndRack + section.rows * section.columns;;
                    }
                });
            },
            setupRacks() {
                this.sections.forEach(section => {
                    this.selectedracks = this.selectedracks.concat(Array(section.rows * section.columns).fill(0));
                });
                this.virtualUnits.forEach(virtualUnit => {
                    for (let rack = virtualUnit.startRack-1; rack <= virtualUnit.endRack-1; rack++) {
                        if (virtualUnit.startBox) {
                            this.selectedracks[rack] = 4;
                        } else {
                            this.selectedracks[rack] = 2;
                        }
                    }
                });
            },
            boxSelectionCheck(){
                if (this.startBox > this.endBox) {
                    this.endBox = this.startBox;
                }
                this.virtualUnits.forEach(virtualUnit => {
                    if (virtualUnit.startBox && virtualUnit.startRack == this.startRack) {
                        if (this.unitDefinition.boxDesignation == 'Alpha') {
                            start = virtualUnit.startBox.charCodeAt() - 65;
                            end = virtualUnit.endBox.charCodeAt() - 65;
                        } else {
                            start = virtualUnit.startBox - 1;
                            end = virtualUnit.endBox - 1;
                        }
                        if (start > this.startBox && end < this.endBox) {
                            this.endBox = this.startBox;
                        }
                    }
                });
            },
            boxSelectionReset(){
                this.startBox = this.endBox = 0;
            },
            getSection() {
                if (this.unitDefinition.boxDesignation == 'Alpha') {
                    this.boxes = Array(this.sections[this.section_number-1].boxes).fill().map((_, i) => String.fromCharCode('A'.charCodeAt(0) + i));
                } else {
                    this.boxes = Array(this.sections[this.section_number-1].boxes).fill().map((_, i) => i + 1);
                }
                this.boxes_disabled = Array(this.boxes.length).fill(false);
                this.positions = this.sections[this.section_number-1].boxes;
            },
            rackselect($rack){
                if (this.selectedracks[$rack-1] != 2) { // not a filled rack
                    if (this.selectedracks.reduce((a,b)=>a+(b&1),0)==0) { // There is no current selection
                        this.sections.forEach((section,index) => {
                            if ($rack >= section.startRack && $rack <= section.endRack) {
                                this.section_number = index + 1;
                                this.getSection();
                            }
                        });
                        this.selectedracks[$rack-1]+=1;
                        if (this.selectedracks[$rack-1]&4) {
                            this.selection_type='partial';
                        } else {
                            this.partial_lock=false;
                        }
                    } else { // Rack selection already exists
                        if (this.selectedracks[$rack-1]&1) { // This rack is part of the current selection
                            if ($rack-1==0 || $rack==this.selectedracks.length || !(this.selectedracks[$rack-2]&1) || !(this.selectedracks[$rack]&1)) { //  This is an end rack
                                this.selectedracks[$rack-1]-=1;
                                if (this.selectedracks.reduce((a,b)=>a+(b&1),0)==0) { // Just removed the last rack from the selection
                                    this.section_number = 1;
                                    this.getSection();
                                    this.selection_type='full';
                                    this.partial_lock=true;
                                    this.boxSelectionReset();
                                } else if (this.selectedracks.reduce((a,b)=>a+(b&1),0)==1) {
                                    this.partial_lock=false;
                                }
                            }
                        } else if (!this.selectedracks[$rack-1]&1 && ($rack >= this.sections[this.section_number-1].startRack) && ($rack <= this.sections[this.section_number-1].endRack)) {
                            if (this.selectedracks[$rack-2]==1 || this.selectedracks[$rack]==1) { // The adjacent rack is part of the current selection
                                this.selectedracks[$rack-1]+=1;
                                if (this.selectedracks.reduce((a,b)=>a+(b&1),0)>1) { // The current selection is multi-rack
                                    this.selection_type='full';
                                    this.partial_lock=true;
                                    this.boxSelectionReset();
                                }
                            }
                        }
                    }
                    if(this.selectedracks.indexOf(5) != -1){ // For a partial rack selection
                        this.startRack = this.endRack = this.selectedracks.indexOf(5) + 1;
                        this.virtualUnits.forEach(virtualUnit => {
                            if (virtualUnit.startRack == $rack) {
                                if (this.unitDefinition.boxDesignation == 'Alpha') {
                                    start = virtualUnit.startBox.charCodeAt() - 65;
                                    end = virtualUnit.endBox.charCodeAt() - 65;
                                } else {
                                    start = virtualUnit.startBox - 1;
                                    end = virtualUnit.endBox - 1;
                                }
                                for (let index = start; index <= end; index++) {
                                    this.boxes_disabled[index] = true;
                                }
                            }
                        });
                        this.endBox = this.startBox = this.boxes_disabled.indexOf(false);
                    } else { // For a full rack selection
                        this.startRack = this.selectedracks.indexOf(1) != -1 ? this.selectedracks.indexOf(1) + 1 : null;
                        this.endRack = this.selectedracks.lastIndexOf(1) != -1 ? this.selectedracks.lastIndexOf(1) + 1 : null;
                    }
                }
            }
        }
    }
</script>

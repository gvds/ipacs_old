<x-layout>
    <x-pageheader>
        Virtual Storage in: {{$physicalUnit->unitID}} [Type: {{$physicalUnit->unitType->unitType}}]
    </x-pageheader>

    <div class='flex-row'>
        <div class='flex justify-between border border-gray-200 rounded shadow-md p-4 mb-4 max-w-min' x-data="storagedata()"
            x-init="initialise({{$physicalUnit->unitType}},{{$physicalUnit->unitType->sections}},{{$virtualUnits}})">
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
                                    <td class='border border-gray-700 px-2 py-1' :class="{'bg-white':selectedracks[{{$rack-1}}]==0,
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
        <x-table>
            <x-slot name='head'>
                <th>Unit Name</th>
                <th>Project</th>
                <th>Section</th>
                <th>Rack Usage</th>
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
                <td class='text-center'>{{$virtualUnit->rackusage}}</td>
                <td class='text-center'>{{$virtualUnit->startRack}}</td>
                <td class='text-center'>{{$virtualUnit->endRack}}</td>
                {{-- <td class='text-center'>{{$virtualUnit->startBox ? 'Partial' : $virtualUnit->endRack}}</td> --}}
                <td class='text-center'>{{$virtualUnit->startBox ?? '-'}}</td>
                <td class='text-center'>{{$virtualUnit->endBox?? '-'}}</td>
                <td>{{$virtualUnit->storageSampleType}}</td>
                <td class='text-center'>{{$virtualUnit->rackCapacity}}</td>
                <td class='text-center'>{{$virtualUnit->boxCapacity}}</td>
                <td>
                    @if ($virtualUnit->active)
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 20 20" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    @else
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 20 20" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    @endif
                </td>
            </tr>
            @endforeach
        </x-table>
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
            getSection() {
                if (this.unitDefinition.boxDesignation == 'Alpha') {
                    this.boxes = Array(this.sections[this.section_number-1].boxes).fill().map((_, i) => String.fromCharCode('A'.charCodeAt(0) + i));
                } else {
                    this.boxes = Array(this.sections[this.section_number-1].boxes).fill().map((_, i) => i + 1);
                }
                this.boxes_disabled = Array(this.boxes.length).fill(false);
                this.positions = this.sections[this.section_number-1].boxes;
            }
        }
    }
</script>

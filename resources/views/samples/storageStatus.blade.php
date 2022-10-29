<x-layout>
    <x-pageheader>
        Sample Storage Status
    </x-pageheader>

    @include('layouts.message')

    {{-- {{ $reports->links() }} --}}

    <x-table class='w-full'>
        <x-slot name='head'>
            <th>Sample Type</th>
            <th>Active</th>
            <th>Aliquots</th>
            <th>Tube Label Type</th>
            <th>Virtual Unit</th>
            <th>Physical Unit</th>
            <th>Section</th>
            <th>Start Rack</th>
            <th>End Rack</th>
            <th>Start Box</th>
            <th>End Box</th>
            <th>Free</th>
            <th>Used</th>
            <th>Total</th>
        </x-slot>
        @foreach ($virtualUnits as $virtualUnit)
        <tr>
            <td>{{$virtualUnit->name}}</td>
            <td>
                @if ($virtualUnit->active)
                <svg class="h-5 w-5 text-green-600" fill="none"
                    viewBox="0 0 20 20" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                @else
                <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 20 20"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                @endif
            </td>
            <td>{{$virtualUnit->samples}}</td>
            <td>{{$virtualUnit->storageSampleType}}</td>
            <td>{{$virtualUnit->virtualUnit}}</td>
            <td><a href="/physicalUnit/{{$virtualUnit->physicalUnit->id}}"
                    class='cursor-pointer text-indigo-900'>{{$virtualUnit->physicalUnit->unitID}}</a></td>
            <td>{{$virtualUnit->section}}</td>
            <td>{{$virtualUnit->startRack}}</td>
            <td>{{$virtualUnit->endRack}}</td>
            <td>{{$virtualUnit->startBox}}</td>
            <td>{{$virtualUnit->endBox}}</td>
            <td>{{$virtualUnit->free}}</td>
            <td>{{$virtualUnit->used}}</td>
            <td>{{$virtualUnit->free + $virtualUnit->used}}</td>
        </tr>
        @endforeach

    </x-table>

</x-layout>

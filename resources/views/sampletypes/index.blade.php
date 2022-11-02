<x-layout>
    <x-pageheader>
        Sample Types
        <x-slot name='button'>
            <x-buttonlink href="sampletypes/create">
                Add New Sample Type
            </x-buttonlink>
        </x-slot>
    </x-pageheader>

    <x-table>
        <x-slot name='head'>
            <th>ID</th>
            <th>Name</th>
            <th>Primary</th>
            <th>Aliquots</th>
            <th>Pooled</th>
            <th>Default Volume</th>
            <th>Transfer Destination</th>
            <th>Sample Group</th>
            <th>Tube Label Type</th>
            <th>Storage Destination</th>
            <th>Storage Sample Type</th>
            <th>Parent Sample Type</th>
            <th>Active</th>
        </x-slot>
        @foreach ($sampletypes as $sampletype)
        <tr class='odd:bg-gray-100'>
            <td class='py-2'>{{$sampletype->id}}</td>
            <td>{{$sampletype->name}}</td>
            <td>{{$sampletype->primary}}</td>
            <td>{{$sampletype->aliquots}}</td>
            <td>{{$sampletype->pooled}}</td>
            <td>{{$sampletype->defaultVolume}} {{$sampletype->volumeUnit}}</td>
            <td>
                @if ($sampletype->transferDestination)
                {{implode(',',json_decode($sampletype->transferDestination))}}
                @endif
            </td>
            <td>{{$sampletype->sampleGroup}}</td>
            <td>{{$sampletype->tubeLabelType->tubeLabelType ?? ''}}</td>
            <td>{{$sampletype->storageDestination}}</td>
            <td>{{$sampletype->storageSampleType}}</td>
            <td>{{$sampletype->parentSampleType->name ?? ''}}</td>
            <td>
                @if ($sampletype->active)
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                @else
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                @endif
            </td>
            <td>
                <x-buttonlink href="sampletypes/{{$sampletype->id}}/edit">
                    Edit
                </x-buttonlink>
            </td>
            {{-- <td>
                <x-delConfirm url='/sampletypes/{{$sampletype->id}}' />
            </td> --}}
        </tr>
        @endforeach
    </x-table>

</x-layout>

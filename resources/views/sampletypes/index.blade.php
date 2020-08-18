<x-layout>
    <x-pageheader>
        Sample Types
        <x-slot name='button'>
            <x-buttonlink href="/sampletypes/create">
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
            <th>Volume Unit</th>
            <th>Store</th>
            <th>Transfer Destination</th>
            <th>Transfer Source</th>
            <th>Sample Group</th>
            <th>Tube Label Type</th>
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
            <td>{{$sampletype->defaultVolume}}</td>
            <td>{{$sampletype->volumeUnit}}</td>
            <td>{{$sampletype->store}}</td>
            <td>{{$sampletype->transferDestination}}</td>
            <td>{{$sampletype->transferSource}}</td>
            <td>{{$sampletype->sampleGroup}}</td>
            <td>{{$sampletype->tubeLabelType}}</td>
            <td>{{$sampletype->storageSampleType}}</td>
            <td>{{$sampletype->parentSampleType_id}}</td>
            <td>{{$sampletype->active}}</td>
            <td>
                <x-buttonlink href="/sampletypes/{{$sampletype->id}}/edit">
                    Edit
                </x-buttonlink>
            </td>
            <td>
                <x-delConfirm url='/sampletypes/{{$sampletype->id}}' />
            </td>
        </tr>
        @endforeach
    </x-table>

</x-layout>

<x-delConfirmScript />
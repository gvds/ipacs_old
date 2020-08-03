<x-layout>
    <x-pageheader>
        Samples
        <x-slot name='button'>
            <x-buttonlink href="/samples/create">
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
        @foreach ($samples as $sample)
        <tr class='odd:bg-gray-100'>
            <td class='py-2'>{{$sample->id}}</td>
            <td>{{$sample->name}}</td>
            <td>{{$sample->primary}}</td>
            <td>{{$sample->aliquots}}</td>
            <td>{{$sample->pooled}}</td>
            <td>{{$sample->defaultVolume}}</td>
            <td>{{$sample->volumeUnit}}</td>
            <td>{{$sample->store}}</td>
            <td>{{$sample->transferDestination}}</td>
            <td>{{$sample->transferSource}}</td>
            <td>{{$sample->sampleGroup}}</td>
            <td>{{$sample->tubeLabelType}}</td>
            <td>{{$sample->storageSampleType}}</td>
            <td>{{$sample->parentSampleType_id}}</td>
            <td>{{$sample->active}}</td>
            <td>
                <x-buttonlink href="/samples/{{$sample->id}}/edit">
                    Edit
                </x-buttonlink>
            </td>
            <td>
                <x-delConfirm url='/samples/{{$sample->id}}' />
            </td>
        </tr>
        @endforeach
    </x-table>

</x-layout>

<x-delConfirmScript />
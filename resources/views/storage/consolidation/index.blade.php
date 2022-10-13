<x-layout>
    <x-pageheader>
        Virtual Storage Unit Consolidation
    </x-pageheader>

    @include('layouts.message')

    <div class='flex flex-col'>
        <x-table>
            <tr>
                <th>Physical Unit</th>
                <td>{{$virtualUnit->physicalUnit->unitID}}</td>
            </tr>
            <tr>
                <th>Virtual Unit</th>
                <td>{{$virtualUnit->virtualUnit}}</td>
            </tr>
            <tr>
                <th>Project</th>
                <td>{{$virtualUnit->project}}</td>
            </tr>
            <tr>
                <th>SampleType</th>
                <td>{{$virtualUnit->storageSampleType}}</td>
            </tr>
            <tr>
                <td>
                    <x-buttonlink href='/physicalUnit/{{$virtualUnit->physicalunit->id}}' class='w-full'>
                        Return
                    </x-buttonlink>
                </td>
            </tr>
        </x-table>

        <form action='/storageconsolidation' method='POST' class='mb-3 text-center'>
            @csrf
            <input type="hidden" name="virtualunit" value={{$virtualUnit->id}}>
            <x-button class='bg-blue-900 text-blue-100 font-bold min-w-full'>
                Consolidate
            </x-button>
        </form>

        <x-table>
            <x-slot name='head'>
                <th>Date</th>
                <th>User</th>
            </x-slot>
            @foreach ($storageconsolidations as $storageconsolidation)
            <tr class="odd:bg-gray-100">
                <td>{{$storageconsolidation->created_at}}</td>
                <td>{{$storageconsolidation->user->fullname}}</td>
                <td>
                    <x-buttonlink href='/storageconsolidation/{{$storageconsolidation->id}}'
                        class='bg-blue-900 text-blue-100 py-1 px-2 mb-3 rounded-md font-bold text-center'>
                        Generate Report
                    </x-buttonlink>
                </td>
            </tr>
            @endforeach
        </x-table>
    </div>
</x-layout>

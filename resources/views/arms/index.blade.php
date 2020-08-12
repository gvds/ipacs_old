<x-layout>
    <x-pageheader>
        Arms
        <x-slot name='button'>
            <x-buttonlink href="/arms/create">
                Add New Arm
            </x-buttonlink>
        </x-slot>
    </x-pageheader>

    <x-table>
        <x-slot name='head'>
            <th>Name</th>
            <th>Number</th>
            <th>Manual Enrol</th>
            <th>Switch Arms</th>
        </x-slot>
        @foreach ($arms as $arm)
        <tr class='odd:bg-gray-100'>
            <td class='py-2'>{{$arm->name}}</td>
            <td>{{$arm->arm_num}}</td>
            <td>{{$arm->manual_enrol}}</td>
            <td>{{$arm->switcharms}}</td>
            <td>
                <x-buttonlink href="/arms/{{$arm->id}}/edit">
                    Edit
                </x-buttonlink>
            </td>
            <td>
                <x-delConfirm url='/arms/{{$arm->id}}' />
            </td>
        </tr>
        @endforeach
    </x-table>

</x-layout>

<x-delConfirmScript />
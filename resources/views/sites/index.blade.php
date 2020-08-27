<x-layout>
    <x-pageheader>
        Sites
        <x-slot name='button'>
            <x-buttonlink href="sites/create">
                Add New Site
            </x-buttonlink>
        </x-slot>
    </x-pageheader>

    <x-table>
        <x-slot name="head">
            <th>Site</th>
        </x-slot>
        @foreach ($sites as $site)
        <tr class="odd:bg-gray-100">
            <td class='py-2'>{{$site->name}}</td>
            <td>
                <x-buttonlink href="sites/{{$site->id}}/edit">
                    Edit
                </x-buttonlink>
            </td>
            <td>
                <x-delConfirm url='/sites/{{$site->id}}' />
            </td>
        </tr>
        @endforeach

    </x-table>

</x-layout>

<x-delConfirmScript />
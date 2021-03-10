<x-layout>
    <x-pageheader>
        Sites
        <x-slot name='button'>
            <x-buttonlink href="sites/create">
                Add New Site
            </x-buttonlink>
        </x-slot>
    </x-pageheader>

    <div x-data="deleteModal()">

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
                    <button class='bg-red-700 text-red-100 py-1 px-2 rounded-md font-bold'
                        @click="deleteconf('site','{{$site->name}}',{{$site->id}})">Delete</button>
                </td>
            </tr>
            @endforeach

        </x-table>

        <x-modals.deleteModal />
    </div>

</x-layout>

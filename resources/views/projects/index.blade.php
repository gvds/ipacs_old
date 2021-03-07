<x-layout>

    <x-pageheader>
        Projects
        <x-slot name='button'>
            <x-buttonlink href="project/create">
                New Project
            </x-buttonlink>
        </x-slot>
        <x-slot name='button2'>
            <x-buttonlink href="redcapproject/new">
                New REDCap Project
            </x-buttonlink>
        </x-slot>
    </x-pageheader>

    @include('layouts.message')

    @if (count($projects))

    <x-table>
        <x-slot name="head">
            <th>Project ID</th>
            <th>Project Name</th>
            <th>Owner</th>
            <th>REDCap ID</th>
            <th>Subject ID Prefix</th>
            <th>Subject ID Digits</th>
            <th>Storage Name</th>
            <th>Label ID</th>
            <th>Last Subject ID</th>
            <th>Active</th>
        </x-slot>
        @foreach ($projects as $project)
        <tr class="odd:bg-gray-100">
            <td class="text-center"><a href="/project/{{$project->id}}/select"><button class='bg-gray-300 font-semibold px-2 py-1 rounded border border-gray-300 shadow-md w-16'>{{$project->id}}</button></td>
            <td>{{$project->project}}</td>
            <td>{{$project->projectOwner->full_name}}</td>
            <td>{{$project->redcapProject_id}}</td>
            <td class="text-center">{{$project->subject_id_prefix}}</td>
            <td class="text-center">{{$project->subject_id_digits}}</td>
            <td>{{$project->storageProjectName}}</td>
            <td>{{$project->label_id}}</td>
            <td>{{$project->last_subject_id}}</td>
            <td class="text-center">{{$project->active}}</td>
            <td>
                @if (isset($project->redcapProject_id))
                <x-buttonlink href='/redcapproject/{{$project->id}}/edit'>Edit</x-buttonlink>
                @else
                <x-buttonlink href='/project/{{$project->id}}/edit'>Edit</x-buttonlink>
                @endif
            </td>
            <td>
                <x-buttonlink class='bg-orange-500' href='/project/{{$project->id}}/reset'>Reset</x-buttonlink>
            </td>
            <td>
                <x-delConfirm url='/project/{{$project->id}}' />
            </td>
        </tr>
        @endforeach
    </x-table>

    @endif

</x-layout>

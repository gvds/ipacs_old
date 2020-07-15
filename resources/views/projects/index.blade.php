<x-layout>

    <x-pageheader>
        Projects
        <x-slot name='button'>
            <x-button href="/project/create">
                New Project
            </x-button>
        </x-slot>
    </x-pageheader>

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
        <tr class="even:bg-gray-200">
            <td class="text-center">{{$project->id}}</td>
            <td>{{$project->project}}</td>
            <td>{{$project->projectOwner->full_name}}</td>
            <td>{{$project->redcapProject_id}}</td>
            <td class="text-center">{{$project->subject_id_prefix}}</td>
            <td class="text-center">{{$project->subject_id_digits}}</td>
            <td>{{$project->storageProjectName}}</td>
            <td>{{$project->label_id}}</td>
            <td>{{$project->last_subject_id}}</td>
            <td class="text-center">{{$project->active}}</td>
            <td class='bg-white'>
                <x-button href='/project/{{$project->id}}/edit'>Edit</x-button>
            </td>
            <td class='bg-white'>
                {{ Form::open(['url' => "/project/$project->id", 'method' => 'DELETE']) }}
                {{ Form::submit('Delete', ['class' => 'text-sm font-bold bg-red-700 text-red-50 mb-0 py-1 px-2']) }}
                {{ Form::close() }}
            </td>
        </tr>
        @endforeach
    </x-table>

    @endif

</x-layout>
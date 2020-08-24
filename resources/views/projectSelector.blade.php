<x-layout>

    <x-pageheader>
        Select Project
    </x-pageheader>

    <div class='w-full max-w-sm bg-gray-100 border border-gray-300 rounded shadow px-0 py-2 text-sm overflow-y-auto'>
        @if(count($projects)==0)
        <div class='px-3'>
            You do not currently have access to any projects
        </div>
        @endempty
        @foreach ($projects as $project)
        <div class="py-1 px-3 text-center hover:font-semibold">
            <a href="/project/{{$project->id}}/select">{{$project->project}}</a>
        </div>
        @endforeach
    </div>

</x-layout>
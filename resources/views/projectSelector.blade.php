<x-layout>

    <x-pageheader>
        Select Project
    </x-pageheader>

    <div class='w-full max-w-sm bg-gray-100 border border-gray-300 rounded shadow px-4 py-3 text-sm'>
        @foreach ($projects as $project)
        <div class="py-1">
            <a href="/project/{{$project->id}}/select">{{$project->project}}</a>
        </div>
        @endforeach
    </div>

</x-layout>
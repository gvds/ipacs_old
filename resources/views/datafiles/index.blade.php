<x-layout>

    <x-pageheader>
        Data Files
        <x-slot name='button'>
            <x-buttonlink href="/datafiles/create">
                Add New Data File
            </x-buttonlink>
        </x-slot>
    </x-pageheader>
    <x-table>
        <x-slot name="head">
            <tr>
                <th>Name</th>
                <th>Site</th>
                <th>File Set</th>
                <th>Generated</th>
                <th>Lab</th>
                <th>Platform</th>
            </tr>
        </x-slot>
        @foreach ($datafiles as $datafile)
        <tr class="odd:bg-gray-100">
            <td>{{$datafile->filename}}</td>
            <td>{{$datafile->site->name}}</td>
            <td>{{$datafile->fileset}}</td>
            <td>{{$datafile->generationDate}}</td>
            <td>{{$datafile->lab}}</td>
            <td>{{$datafile->platform}}</td>
            <td>
                <x-buttonlink href="/datafiles/{{$datafile->id}}/download">
                    Download
                </x-buttonlink>
            </td>
            <td>
                <x-buttonlink href="/datafiles/{{$datafile->id}}">
                    Details
                </x-buttonlink>
            </td>
            <td>
                <x-buttonlink href="/datafiles/create?fileset={{$datafile->fileset}}">
                    Add new file to Set
                </x-buttonlink>
            </td>
        </tr>
        @endforeach
    </x-table>

</x-layout>
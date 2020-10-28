<x-layout>

    <x-pageheader>
        Generate Subject IDs
    </x-pageheader>

    @include('layouts.message')

    {{ Form::open(['url' => '/subjects', 'class' => 'form', 'method' => 'POST']) }}
    <table>
        <thead>
            <tr>
                <th>Number of New Records</th>
                <th>Arm</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ Form::selectRange('records', 1, 20, 5) }}</td>
                <td>{{ Form::select('arm', $arms) }}</td>
            </tr>
            <tr>
                <td>
                    {{ Form::submit('Generate') }}
                </td>
            </tr>
        </tbody>
    </table>
    {{ Form::close() }}

</x-layout>
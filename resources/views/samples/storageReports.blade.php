<x-layout>
    <x-pageheader>
        Sample Storage Reports
    </x-pageheader>

    @include('layouts.message')

    <x-table class='w-full'>
        <x-slot name='head'>
            <th>User</th>
            <th>Timestamp</th>
        </x-slot>
        @foreach ($reports as $report)
        <tr>
            <td>{{$report->user->fullname}}</td>
            <td>{{$report['created_at']}}</td>
            <td><x-buttonlink href="/samplestore/{{$report['id']}}/report">Retrieve</x-buttonlink></td>
        </tr>
        @endforeach
    </x-table>

</x-layout>
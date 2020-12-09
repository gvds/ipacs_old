<x-layout>
    <x-pageheader>
        Impersonate User
    </x-pageheader>

    <x-table>
        <x-slot name='head'>
            <th>Site</th>
            <th>Name</th>
        </x-slot>
        @foreach ($users as $user)
            <tr>
                <td>{{$user->currentSite[0]->name}}</td>
                <td>{{$user->fullname}}</td>
                <td><x-buttonlink href='/users/impersonate/start/{{$user->id}}'>-></x-buttonlink></td>
            </tr>
        @endforeach
    </x-table>

</x-layout>
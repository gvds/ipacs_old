<x-layout>

    <x-pageheader>
        User Substitutions
    </x-pageheader>

    @include('layouts.message')

    <x-table>
        <x-slot name="head">
            <th>First Name</th>
            <th>Surname</th>
            <th>Site</th>
            <th>Email</th>
            <th>Telephone</th>
            <th>Current Substitute</th>
            <th>Manage</th>
        </x-slot>
        @foreach ($substitutableUsers as $user)
        <tr class="odd:bg-gray-100">
            <td class='py-2'>{{$user->firstname}}</td>
            <td>{{$user->surname}}</td>
            <td>
                @if (!empty($user['currentSite'][0]))
                {{$user['currentSite'][0]['name']}}
                @endif
            </td>
            <td>{{$user->email}}</td>
            <td>{{$user->telephone}}</td>
            <td>
                @if (!empty($user->substitute[0]))
                {{$user->substitute[0]->fullname}}
                @endif
            </td>
            <td class='content-center'>
                <a href="/substitute/{{$user->id}}">
                    <div class='bg-gray-300  py-1 rounded-md'>
                        <svg class='mx-auto h-4 w-4' xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10.293 15.707a1 1 0 010-1.414L14.586 10l-4.293-4.293a1 1 0 111.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                            <path fill-rule="evenodd"
                                d="M4.293 15.707a1 1 0 010-1.414L8.586 10 4.293 5.707a1 1 0 011.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                </a>
            </td>
        </tr>
        @endforeach
    </x-table>

</x-layout>

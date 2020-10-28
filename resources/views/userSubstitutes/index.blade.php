<x-layout>

    <x-pageheader>
        Substitutes
    </x-pageheader>

    @include('layouts.message')

    @isset($currentSubstitute)
    <div class='mb-3 font-medium italic text-white tracking-wider bg-blue-900 rounded px-2 py-1'>
        You are currently being substituted by {{$currentSubstitute->fullname}}
    </div>
    @endisset

    <x-table>
        <x-slot name="head">
            <th>First Name</th>
            <th>Surname</th>
            <th>Email</th>
            <th>Telephone</th>
        </x-slot>
        @foreach ($candidateSubstitutes as $substitute)
        <tr class="odd:bg-gray-100">
            <td class='py-2'>{{$substitute->firstname}}</td>
            <td>{{$substitute->surname}}</td>
            <td>{{$substitute->email}}</td>
            <td>{{$substitute->telephone}}</td>
            <td>
                @if (isset($currentSubstitute) and $substitute->id === $currentSubstitute->id)
                {{ Form::open(['url' => '/substitute', 'method' => 'DELETE']) }}
                <button type='submit' class='bg-red-300 rounded shaddow-md px-2 py-1'>Remove Substitute</button>
                {{ Form::close() }}
                @else
                {{ Form::open(['url' => '/substitute', 'method' => 'POST']) }}
                {{ Form::hidden('substitute_id', $substitute->id) }}
                <button type='submit' class='bg-gray-300 rounded shaddow-md px-2 py-1'>Set Substitute</button>
                {{ Form::close() }}
                @endif
            </td>
        </tr>
        @endforeach
    </x-table>

</x-layout>
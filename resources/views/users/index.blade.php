<x-layout>

  <x-pageheader>
    Users
    <x-slot name='button'>
      <x-buttonlink href="/users/create">
        Add New User
      </x-buttonlink>
    </x-slot>
  </x-pageheader>

  <x-table>
    <x-slot name="head">
      <th>Username</th>
      <th>First Name</th>
      <th>Surname</th>
      <th>Email</th>
      <th>Telephone</th>
      <th>Roles</th>
    </x-slot>
    @foreach ($users as $user)
    <tr class="odd:bg-gray-100">
      <td class='py-2'>{{$user->username}}</td>
      <td>{{$user->firstname}}</td>
      <td>{{$user->surname}}</td>
      <td>{{$user->email}}</td>
      <td>{{$user->telephone}}</td>
      <td>{{implode(" || ",$user->roles->pluck('name')->toArray())}}</td>
      <td>
        <x-buttonlink href="/users/{{$user->id}}/roles">Roles</x-buttonlink>
      </td>
      <td>
        <x-buttonlink href="/users/{{$user->id}}/edit">Edit</x-buttonlink>
      </td>
      <td>
        <x-delConfirm url='/users/{{$user->id}}' />
        {{-- {{ Form::open(['url' => "/users/$user->id", 'method' => 'DELETE']) }}
        {{ Form::button('Delete', ['class' => 'text-sm font-bold bg-red-700 text-red-50 mb-0 py-1 px-2 rounded shadow-md']) }}
        {{ Form::close() }} --}}
      </td>
    </tr>
    @endforeach
  </x-table>

</x-layout>

<x-delConfirmScript />
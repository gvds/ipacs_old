<x-layout>

  <x-pageheader>
    Users
    <x-slot name='button'>
      <x-buttonlink href="/user/create">
        Add New User
      </x-buttonlink>
    </x-slot>
  </x-pageheader>

  <div x-data="deleteModal()">

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
          <x-buttonlink href="/user/{{$user->id}}/roles">Roles</x-buttonlink>
        </td>
        <td>
          <x-buttonlink href="/user/{{$user->id}}/edit">Edit</x-buttonlink>
        </td>
        <td>
          <button class='bg-red-700 text-red-100 py-1 px-2 rounded-md font-bold'
            @click="deleteconf('user','{{$user->fullname}}',{{$user->id}})">Delete</button>
        </td>
      </tr>
      @endforeach
    </x-table>

    <x-modals.deleteModal />
  </div>

</x-layout>

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
        <th>Active</th>
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
          @if ($user->active)
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          @else
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
          @endif

        </td>
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

    {{ $users->links('vendor.pagination.tailwind') }}

    <x-modals.deleteModal />
  </div>

</x-layout>

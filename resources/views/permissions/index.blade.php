<x-layout>

  <x-pageheader>
    Permissions
    <x-slot name='button'>
      <x-buttonlink href="/permissions/create">
        Add New Permission
      </x-buttonlink>
    </x-slot>
  </x-pageheader>

  <div x-data="deleteModal()">

    <x-table>
      <x-slot name="head">
        <th>Permission</th>
        <th>Name</th>
        <th>Scope</th>
        <th>Description</th>
        <!-- <th>Guard</th> -->
        <th colspan=3></th>
      </x-slot>
      @foreach ($permissions as $permission)
      <tr class="odd:bg-gray-100">
        <td class='py-2'>{{$permission->name}}</td>
        <td>{{$permission->display_name}}</td>
        <td>{{$permission->scope}}</td>
        <td>{{$permission->description}}</td>
        <!-- <td>{{$permission->guard_name}}</td> -->
        <td>
          <x-buttonlink href="permissions/{{$permission->id}}/edit">Edit</x-buttonlink>
        </td>
        <td>
          <button class='bg-red-700 text-red-100 py-1 px-2 rounded-md font-bold'
            @click="deleteconf('permissions','{{$permission->name}}',{{$permission->id}})">Delete</button>
        </td>
      </tr>
      @endforeach
    </x-table>

    <x-modals.deleteModal />
  </div>

</x-layout>

<x-layout>

  <x-pageheader>
    Permissions
    <x-slot name='button'>
      <x-button href="/permissions/create">
        Add New Permission
      </x-button>
    </x-slot>
  </x-pageheader>
  <x-table>
    <x-slot name="head">
      <th>Name</th>
      <th>Guard</th>
      <th colspan=3></th>
    </x-slot>
    @foreach ($permissions as $permission)
    <tr class="odd:bg-gray-100">
      <td>{{$permission->name}}</td>
      <td>{{$permission->guard_name}}</td>
      <td>
        <x-button href="/permissions/{{$permission->id}}/edit">Edit</x-button>
      </td>
      <td>
        <x-delConfirm url='/permissions/{{$permission->id}}' />
      </td>
    </tr>
    @endforeach
  </x-table>
</x-layout>

<x-delConfirmScript />
<x-layout>

  <x-pageheader>
    Roles
    <x-slot name='button'>
      <x-button href="/roles/create">
        Add New Role
      </x-button>
    </x-slot>
  </x-pageheader>

  <x-table>
    <x-slot name="head">
      <th>Name</th>
      <th>Guard</th>
      <th>Permissions</th>
    </x-slot>
      @foreach ($roles as $role)
      <tr class="odd:bg-gray-100">
        <td>{{$role->name}}</td>
        <td>{{$role->guard_name}}</td>
        <td>{{implode(" || ",$role->permissions->pluck('name')->toArray())}}</td>
        <td>
          <x-button href="/roles/{{$role->id}}">Permissions</x-button>
        </td>
        <td>
          <x-button href="/roles/{{$role->id}}/edit">Edit</x-button>
        </td>
        <td>
          <x-delConfirm url="/roles/{{$role->id}}" />
        </td>
      </tr>
      @endforeach
  </x-table>

</x-layout>

<x-delConfirmScript />

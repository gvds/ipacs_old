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
          {!! Form::open(['url' => "roles/$role->id", 'method' => 'DELETE']) !!}
          {{ Form::button('Delete', ['class' => 'text-sm font-bold bg-red-700 text-red-50 mb-0 py-1 px-2 rounded shadow-md']) }}
          {{-- {!! Form::button('Delete', ['class' => "",'onclick'=>"show_confirm($role->id)"]) !!}
          {!! Form::submit('Confirm', ['class' => "",'id'=>'del_'.$role->id]) !!} --}}
          {!! Form::close() !!}
        </td>
      </tr>
      @endforeach
  </x-table>

</x-layout>

<script>
  function show_confirm(id){
            $('#del_' + id).removeClass('invisible');
          }
</script>

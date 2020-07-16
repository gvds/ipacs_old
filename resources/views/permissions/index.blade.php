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
    </x-slot>
    @foreach ($permissions as $permission)
    <tr class="odd:bg-gray-100">
      <td>{{$permission->name}}</td>
      <td>{{$permission->guard_name}}</td>
      <td>
        <x-button href="/permissions/{{$permission->id}}/edit">Edit</x-button>
      </td>
      <td>
        {{ Form::open(['url' => "permissions/$permission->id", 'method' => 'DELETE']) }}
        {{ Form::button('Delete', ['class' => 'text-sm font-bold bg-red-700 text-red-50 mb-0 py-1 px-2 rounded shadow-md']) }}
        {{-- {{ Form::button('Delete', ['class' => "btn btn-sm
        btn-danger",'id'=>'del_'.$permission->id,'onclick'=>"confirm($permission->id)"]) }}
        {{ Form::submit('Confirm', ['class' => "btn btn-sm btn-danger", 'id'=>'confirm_'.$permission->id]) }} --}}
        {{ Form::close() }}
      </td>
    </tr>
    @endforeach
  </x-table>
</x-layout>

<script>
  function confirm(id){
    if($('#del_' + id).text() == 'Delete'){
      $('#del_' + id).text('Cancel');
      $('#del_' + id).removeClass('btn-danger');
      $('#del_' + id).addClass('btn-success');
      $('#confirm_' + id).removeClass('invisible');
    } else {
      $('#del_' + id).text('Delete');
      $('#del_' + id).removeClass('btn-success');
      $('#del_' + id).addClass('btn-danger');
      $('#confirm_' + id).addClass('invisible');
    }
  }
</script>
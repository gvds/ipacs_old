<x-layout>
  <x-pageheader>
    Project Permissions for {{$user->full_name}}
  </x-pageheader>

  @include('layouts.errormsg')

  {{ Form::open(['url' => ["/team/$user->id/permissions"], 'method' => 'PATCH', 'class'=>'form']) }}
  <x-table>
    <x-slot name="head">
      <th class='text-left'>Permissions</th>
    </x-slot>
    @foreach ($permissions as $permission_id => $permission)
    <tr>
      <td>{{$permission}}</td>
      <td>{{ Form::checkbox($permission_id, 1, array_key_exists($permission_id,$userpermissions)) }}</td>
    </tr>
    @endforeach
  </x-table>
  {{ Form::submit('Update Permissions', ['class' => "w-full"]) }}
  <x-buttonlink :href="url('/team')" class='text-orange-500'>Cancel</x-buttonlink>
  {{ Form::close() }}

</x-layout>
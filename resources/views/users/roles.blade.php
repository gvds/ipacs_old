<x-layout>
  <x-pageheader>
    Edit Roles for {{$user->full_name}}
  </x-pageheader>

  @include('layouts.errormsg')

  {{ Form::open(['url' => ["/users/$user->id/roles"], 'method' => 'post', 'class'=>'form']) }}
  <x-table>
    <x-slot name="head">
      <th class='text-left'>Role</th>
    </x-slot>
    @foreach ($roles_restricted as $role_id => $role)
    <tr>
      <td>{{$role}}</td>
      <td>{{ Form::checkbox($role, 1, array_key_exists($role_id,$userroles), ['disabled']) }}</td>
    </tr>
    @endforeach
    @foreach ($roles as $role_id => $role)
    <tr>
      <td>{{$role}}</td>
      <td>{{ Form::checkbox($role, 1, array_key_exists($role_id,$userroles)) }}</td>
    </tr>
    @endforeach
  </x-table>
  {{ Form::submit('Update Roles', ['class' => "w-full"]) }}
  <x-buttonlink :href="url('/users')" class='text-orange-500'>Cancel</x-buttonlink>
  {{ Form::close() }}

</x-layout>
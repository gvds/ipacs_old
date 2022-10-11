<x-layout>

  <x-pageheader>
    Team Member: {{$user->fullname}}
    <x-buttonlink href="/team/{{$user->id}}/edit">Edit</x-buttonlink>
  </x-pageheader>
  @include('layouts.message')
  <div>
    <x-table>
      <tr>
        <th>User Name</th>
        <td>{{$user->username}}</td>
      </tr>
      <tr>
        <th>Email</th>
        <td>{{$user->email}}</td>
      </tr>
      <tr>
        <th>Telephone</th>
        <td>{{$user->telephone}}</td>
      </tr>
      <tr>
        <th>Home Site</th>
        <td>{{$user->homesite}}</td>
      </tr>
      <tr>
        <th>Permissions</th>
        <td>
          {{implode(' || ',$user->team_member_permissions->where('pivot.team_id',$team->id)->pluck('name')->toArray())}}
        </td>
        <td>
          <x-buttonlink href="/team/{{$user->id}}/permissions">Edit Permissions</x-buttonlink>
        </td>
      </tr>
    </x-table>
    <x-form action='/team/{{$user->id}}/transfersubjects' method='POST'>
      <div class='font-bold mb-1'>Transfer Subjects to:</div>
      <select name="transferee">
        @foreach ($users as $id=>$fullname)
        <option value='{{$id}}'>{{$fullname}}</option>
        @endforeach
      </select>
      <x-button class='bg-blue-900 font-semibold mt-3'>Transfer</x-button>
    </x-form>
  </div>
  <x-buttonlink :href="url('/team')">Return</x-buttonlink>
</x-layout>

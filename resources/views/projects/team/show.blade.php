<x-layout>

  <x-pageheader>
    Team Member: {{$user->fullname}}
      <x-buttonlink href="team/{{$user->id}}/edit">Edit</x-buttonlink>
  </x-pageheader>
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
      <td>{{implode(" || ",$user->team_member_permissions->where('pivot.team_id',$team->id)->pluck('name')->toArray())}}</td>
      <td><x-buttonlink href="team/{{$user->id}}/permissions">Edit Permissions</x-buttonlink></td>
    </tr>
  </x-table>
  <x-buttonlink href='/team'>Return</x-buttonlink>
</x-layout>
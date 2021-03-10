<x-layout>

  <x-pageheader>
    Team Members
    <x-slot name='button'>
      <x-buttonlink href="team/addmember">
        Add New Member
      </x-buttonlink>
    </x-slot>
  </x-pageheader>

  <div x-data="deleteModal()">

    <x-table>
      <x-slot name="head">
        <th>Name</th>
        <th>Site</th>
        <th>Permissions</th>
      </x-slot>
      @foreach ($teammembers as $teammember)
      <tr class="odd:bg-gray-100">
        <td class='py-2'>{{$teammember->fullname}}</td>
        <td>{{$teammember->name}}</td>
        <td>
          {{implode(" || ",$teammember->team_member_permissions->where('pivot.team_id',$currentProject->id)->pluck('name')->toArray())}}
        </td>
        <td>
          <x-buttonlink href="team/{{$teammember->id}}">Details</x-buttonlink>
        </td>
        <td>
          <button class='bg-red-700 text-red-100 py-1 px-2 rounded-md font-bold'
            @click="deleteconf('teammember','{{$teammember->fullname}}',{{$teammember->id}})">Delete</button>
        </td>
      </tr>
      @endforeach

    </x-table>

    <x-modals.deleteModal />
  </div>

</x-layout>

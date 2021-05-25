<x-layout>

  <x-pageheader>
    Tube Label-Types
    <x-slot name='button'>
      <x-buttonlink href="/tubelabeltype/create">
        Add New Format
      </x-buttonlink>
    </x-slot>
  </x-pageheader>


  <div x-data="deleteModal()">

    <x-table>
      <x-slot name='head'>
        <th class='text-left'>Type</th>
        <th class='text-left'>Format</th>
        <th>Project Override</th>
        <th></th>
      </x-slot>
      @foreach ($generic_tubeLabelTypes as $tubeLabelType)
      <tr {!!isset($tubeLabelType->override) ? "class='text-blue-500'" : "class='text-blue-800'"!!}>
        <td>{{$tubeLabelType->tubeLabelType}}</td>
        <td>{{$tubeLabelType->barcodeFormat}}</td>
        @if (isset($tubeLabelType->override))
        <td class='text-red-800 font-semibold text-center'>
          Overridden
        </td>
        @else
        <td class='text-center'>
          <x-buttonlink href="/tubelabeltype/{{$tubeLabelType->id}}/override">
            Override
          </x-buttonlink>
        </td>
        @endif
      </tr>
      @endforeach
      <tr>
        <th colspan=5 class='bg-gray-200 text-left italic'>Project-specific Formats</th>
      </tr>
      <tr class='text-left'>
        <th>Type</th>
        <th>Format</th>
        <th class='text-center'>Override</th>
      </tr>
      @foreach ($project_tubeLabelTypes as $tubeLabelType)
      <tr>
        <td>{{$tubeLabelType->tubeLabelType}}</td>
        <td>{{$tubeLabelType->barcodeFormat}}</td>
        <td class='text-center'>
          @if (in_array($tubeLabelType->tubeLabelType,$generic_tubeLabelType_names))
          *
          @endif
        </td>
        <td>
          <x-buttonlink href="/tubelabeltype/{{$tubeLabelType->id}}/edit">
            Edit
          </x-buttonlink>
        </td>
        <td>
          <button class='bg-red-700 text-red-100 py-1 leading-tight px-2 rounded-md font-bold'
            @click="deleteconf('tubelabeltype','{{$tubeLabelType->tubeLabelType}}',{{$tubeLabelType->id}})">Delete</button>
        </td>
      </tr>
      @endforeach
    </x-table>

    <x-modals.deleteModal />
  </div>

</x-layout>

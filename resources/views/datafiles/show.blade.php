<x-layout>

  <x-pageheader>
    Data File Details
    <x-buttonlink href="/datafiles/{{$datafile->id}}/edit">
      Edit
    </x-buttonlink>
  </x-pageheader>
  <div>
    <x-table>
      <tr>
        <th class='text-left'>Name</th>
        <td>{{$datafile->filename}}</td>
        <td>
          <x-delConfirm url="/datafiles/{{$datafile->id}}" />
        </td>
      </tr>
      <tr>
        <th class='text-left'>Owner</th>
        <td>{{$datafile->user->fullname}}</td>
      </tr>
      <tr>
        <th class='text-left'>Site</th>
        <td>{{$datafile->site->name}}</td>
      </tr>
      <tr>
        <th class='text-left'>Generated</th>
        <td>{{$datafile->generationDate}}</td>
      </tr>
      <tr>
        <th class='text-left'>Lab</th>
        <td>{{$datafile->lab}}</td>
      </tr>
      <tr>
        <th class='text-left'>Platform</th>
        <td>{{$datafile->platform}}</td>
      </tr>
      <tr>
        <th class='text-left'>Opperator</th>
        <td>{{$datafile->opperator}}</td>
      </tr>
      <tr>
        <th class='text-left'>SHA256 Hash</th>
        <td>{{$datafile->hash}}</td>
      </tr>
      <tr>
        <th class='text-left'>Description</th>
        <td>{{$datafile->description}}</td>
        
      </tr>
    </x-table>
  </div>
  <x-buttonlink href='/datafiles'>Return</x-buttonlink>
</x-layout>

<x-delConfirmScript />
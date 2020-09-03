<x-layout>

  <x-pageheader>
    Upload Data File
  </x-pageheader>

  @include('layouts.errormsg')
  <div class='flex space-x-40'>
    {{ Form::open(['url' => '/datafiles', 'class' => 'form','files'=>true,'method'=>'POST']) }}
    {{ Form::hidden('fileset', $fileset) }}
    {{ Form::label('file', 'File', ['class'=>'text-sm']) }}
    {{ Form::file('file', ['required']) }}
    {{ Form::label('generationDate', 'Creation Date', ['class'=>'text-sm']) }}
    {{ Form::date('generationDate', \Carbon\Carbon::today(), ['required']) }}
    {{ Form::label('lab', 'Lab', ['class'=>'text-sm']) }}
    {{ Form::text('lab', null, ['required']) }}
    {{ Form::label('platform', 'Platform', ['class'=>'text-sm']) }}
    {{ Form::text('platform', null, ['required']) }}
    {{ Form::label('opperator', 'Opperator', ['class'=>'text-sm']) }}
    {{ Form::text('opperator', null, ['required']) }}
    {{ Form::label('description', 'Description', ['class'=>'text-sm']) }}
    {{ Form::textarea('description', null, ['rows'=>5,'cols'=>35]) }}
    {{ Form::submit('Submit', ['class' => 'w-full mt-2']) }}
    <x-buttonlink :href="url('/datafiles')" class='text-orange-500'>Cancel</x-buttonlink>
    {{ Form::close() }}
    <div>
      <h3 class='text-lg font-semibold'>Files in this set</h3>
      <x-table class='bg-gray-100'>
          <x-slot name='head'>
            <th>Filename</th>
            <th>Date</th>
            <th>Platform</th>
            <th>Lab</th>
            <th>Opperator</th>
          </x-slot>
          @foreach ($files as $file)
          <tr>
            <td>{{$file->description}}</td>
            <td>{{$file->generationDate}}</td>
            <td>{{$file->platform}}</td>
            <td>{{$file->lab}}</td>
            <td>{{$file->opperator}}</td>
          </tr>
          @endforeach
      </x-table>
    </div>
  </div>

</x-layout>
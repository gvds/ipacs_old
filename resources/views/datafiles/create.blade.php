<x-layout>

  <x-pageheader>
    Upload Data File
  </x-pageheader>

  @include('layouts.message')
  <div class='flex space-x-40'>
    {{ Form::open(['url' => '/datafiles', 'class' => 'form','files'=>true,'method'=>'POST']) }}
    {{ Form::hidden('fileset', $fileset) }}
    {{ Form::label('file', 'File', ['class'=>'text-sm']) }}
    {{ Form::file('file', ['required']) }}
    {{ Form::label('generationDate', 'Creation Date', ['class'=>'text-sm']) }}
    {{ Form::date('generationDate', \Carbon\Carbon::today(), ['required']) }}
    {{ Form::label('owner', 'Data Owner', ['class'=>'text-sm']) }}
    {{ Form::text('owner', null, ['required','placeholder'=>'Who owns this data...']) }}
    {{ Form::label('filetype', 'File Type', ['class'=>'text-sm']) }}
    {{ Form::text('filetype', null, ['required','placeholder'=>'The format of the file...']) }}
    {{-- {{ Form::select('filetype', [''=>'','text'=>'Plain Text','csv'=>'Comma-Separated Value','tsv'=>'Tab-Separated Value','doc'=>'Wordprocessor Document','pdf'=>'PDF Document','spreadsheet'=>'Spreadsheet','zip'=>'Zip Archive','tar'=>'Tar Archive'], ['required','placeholder'=>'The format of the file...']), null }} --}}
    {{ Form::label('software', 'Software', ['class'=>'text-sm']) }}
    {{ Form::text('software', null, ['required','placeholder'=>'The software that generated the file...']) }}
    {{ Form::label('lab', 'Lab', ['class'=>'text-sm']) }}
    {{ Form::text('lab', null, ['required','placeholder'=>'The lab in which this file originated...']) }}
    {{ Form::label('platform', 'Platform', ['class'=>'text-sm']) }}
    {{ Form::text('platform', null, ['required','placeholder'=>'The instrument that generated this file...']) }}
    {{ Form::label('opperator', 'Opperator', ['class'=>'text-sm']) }}
    {{ Form::text('opperator', null, ['required','placeholder'=>'The person who generated this data...']) }}
    {{ Form::label('description', 'Description', ['class'=>'text-sm']) }}
    {{ Form::textarea('description', null, ['rows'=>5,'cols'=>35,'placeholder'=>'Optional additional details...']) }}
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
            <th>Depositor</th>
          </x-slot>
          @foreach ($files as $file)
          <tr>
            <td>{{$file->filename}}</td>
            <td>{{$file->generationDate}}</td>
            <td>{{$file->platform}}</td>
            <td>{{$file->lab}}</td>
            <td>{{$file->user->fullname}}</td>
          </tr>
          @endforeach
      </x-table>
    </div>
  </div>

</x-layout>
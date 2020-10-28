<x-layout>

  <x-pageheader>
      Edit Data File Details
  </x-pageheader>

  @include('layouts.message')
  {{ Form::model($datafile, ['url' => "/datafiles/$datafile->id", 'class' => 'form','method'=>'PATCH']) }}
  {{ Form::label('filename', 'Filename', ['class'=>'text-sm']) }}
  {{ Form::text('filename', null,['disabled']) }}
  {{ Form::label('generationDate', 'Creation Date', ['class'=>'text-sm']) }}
  {{ Form::date('generationDate', null, ['required']) }}
  {{ Form::label('owner', 'Data Owner', ['class'=>'text-sm']) }}
  {{ Form::text('owner', null, ['required','placeholder'=>'Who owns this data...']) }}
  {{ Form::label('filetype', 'File Type', ['class'=>'text-sm']) }}
  {{ Form::text('filetype', null, ['required','placeholder'=>'The format of the file...']) }}
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
  {{ Form::submit('Update', ['class' => 'w-full mt-2']) }}
  <x-buttonlink :href="url('/datafiles')" class='text-orange-500'>Cancel</x-buttonlink>
  {{ Form::close() }}

</x-layout>
<x-layout>

  <x-pageheader>
      Edit Data File Details
  </x-pageheader>

  @include('layouts.errormsg')
  {{ Form::model($datafile, ['url' => "/datafiles/$datafile->id", 'class' => 'form','method'=>'PATCH']) }}
  {{ Form::label('filename', 'Filename', ['class'=>'text-sm']) }}
  {{ Form::text('filename', null,['disabled']) }}
  {{ Form::label('generationDate', 'Creation Date', ['class'=>'text-sm']) }}
  {{ Form::date('generationDate', null, ['required']) }}
  {{ Form::label('lab', 'Lab', ['class'=>'text-sm']) }}
  {{ Form::text('lab', null, ['required']) }}
  {{ Form::label('platform', 'Platform', ['class'=>'text-sm']) }}
  {{ Form::text('platform', null, ['required']) }}
  {{ Form::label('opperator', 'Opperator', ['class'=>'text-sm']) }}
  {{ Form::text('opperator', null, ['required']) }}
  {{ Form::label('description', 'Description', ['class'=>'text-sm']) }}
  {{ Form::textarea('description', null, ['rows'=>5, 'cols'=>35]) }}
  {{ Form::submit('Update', ['class' => 'w-full mt-2']) }}
  <x-buttonlink :href="url('/datafiles')" class='text-orange-500'>Cancel</x-buttonlink>
  {{ Form::close() }}


</x-layout>
<x-layout>

  <x-pageheader>
    Subject: {{$subject->subjectID}}
  </x-pageheader>

  @include('layouts.message')
  {{ Form::model($subject, ['route' => ['subjects.update', $subject], 'method' => 'PATCH', 'class'=>'form']) }}
  {{ Form::hidden('id', $subject->id)}}
  {{ Form::label('firstname', 'Firstname', ['class'=>'text-sm']) }}
  {{ Form::text('firstname', null) }}
  {{ Form::label('surname', 'Surname', ['class'=>'text-sm']) }}
  {{ Form::text('surname', null) }}
  {{ Form::label('address1', 'Address', ['class'=>'text-sm']) }}
  {{ Form::text('address1', null) }}
  {{ Form::text('address2', null) }}
  {{ Form::text('address3', null) }}
  {{ Form::submit('Save Changes', ['class' => 'w-full mt-2']) }}
  <x-buttonlink href="/subjects/{{$subject->id}}" class='text-orange-500'>Cancel</x-buttonlink>
  {{ Form::close() }}


</x-layout>
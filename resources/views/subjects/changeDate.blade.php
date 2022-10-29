<x-layout>

  <x-pageheader>
    Subject: {{$subject->subjectID}}
  </x-pageheader>

  @include('layouts.message')

  <div class='font-semibold bg-orange-100 border border-gray-200 p-3 rounded shadow-md mb-4'>
    This will update the event dates for the current arm.<br>
    If this is the enrolment arm, the enrol date will also be amended.
  </div>

  {{ Form::open(['url' => "/subjects/$subject->id/changeDate", 'method' => 'POST', 'class'=>'form']) }}
  {{ Form::label('armBaselineDate', 'New Arm Start Date', ['class'=>'text-sm']) }}
  {{ Form::date('armBaselineDate', $subject->armBaselineDate) }}
  {{ Form::submit('Save Changes', ['class' => 'w-full mt-2']) }}
  <x-buttonlink href="/subjects/{{$subject->id}}" class='text-orange-500'>Cancel</x-buttonlink>
  {{ Form::close() }}


</x-layout>

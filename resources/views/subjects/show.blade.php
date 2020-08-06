<x-layout>

  <x-pageheader>
    Subject: {{$subject->subjectID}}
    <x-slot name='button'>
      <x-buttonlink href='/subjects' class='text-orange-700'>Return</x-buttonlink>
    </x-slot>
  </x-pageheader>

  @include('layouts.errormsg')

  <div class='flex'>
    <div class='flex-col'>
      <div class='text-lg font-bold'>Details</div>
      <div>
        <x-table class="table table-sm">
          <tr>
            <td class='font-bold'>Current Arm</td>
            <td>{{$subject->arm->name}}</td>
          </tr>
          <tr>
            <td class='font-bold'>Site</td>
            <td>{{$subject->site->name}}</td>
          </tr>
          <tr>
            <td class='font-bold'>Owner</td>
            <td>{{$subject->user->fullname}}</td>
          </tr>
          <tr>
            <td class='font-bold'>Previous Arm</td>
            <td>
              @if ($subject->previous_arm)
              {{$subject->previous_arm->name}}
              @endif
            </td>
          </tr>
          <tr>
            <td class='font-bold'>Status</td>
            <td>
              @switch($subject->subject_status)
              @case(0)
              Unenrolled
              @break
              @case(1)
              Enrolled
              @break
              @case(2)
              Dropped
              @break
              @default
              Error
              @endswitch
            </td>
          </tr>
        </x-table>
      </div>
    </div>

    @if ($subject->subject_status === 1 & count($switcharms) > 0)
    <div class='ml-20'>
      <div class='text-lg font-bold'>Switch Arm</div>
      {{ Form::open(['url' => "/subjects/$subject->id/switch", 'class' => 'form', 'method' => 'POST']) }}
      {{ Form::select('switchArm', $switcharms, null, ['required','placeholder' => 'Select new arm...']) }}
      {{ Form::date('switchDate', today(), ['required']) }}
      {{ Form::submit('Switch', ['class' => 'w-full mt-2']) }}
      {{ Form::close() }}
    </div>
    @endif

  </div>


  @if ($subject->subject_status === 0)

  <div class='text-lg font-bold'>Enrol Subject</div>
  {!! Form::open(['url' => "/subjects/$subject->id/enrol", 'class' => 'form', 'method' => 'POST']) !!}
  {{ Form::label('enrolDate','Enrolment Date')}}
  {{ Form::date('enrolDate') }}
  {{ Form::submit('Enrol', ['class' => 'w-full mt-1']) }}
  {{ Form::close() }}

  @else

  <div class='mt-5'>
    <div class='text-lg font-bold w-auto'>Events</div>
    <x-table>
      <x-slot name='head'>
        <th>Arm</th>
        <th>Event ID</th>
        <th>Event</th>
        <th>Status</th>
        <th>Registered</th>
        <th>Logged</th>
      </x-slot>
      @foreach ($events as $event)
      <tr class='odd:bg-gray-100'>
        <td>{{$event->arm->name}}</td>
        <td>{{$event->id}}</td>
        <td>{{$event->name}}</td>
        <td>{{$eventstatus[$event->pivot->eventstatus_id]->eventstatus}}</td>
        <td>
          @if ($event->pivot->reg_timestamp)
          {{Carbon\Carbon::parse($event->pivot->reg_timestamp)->format('Y-m-d H:i')}}
          @endif
        </td>
        <td>
          @if ($event->pivot->log_timestamp)
          {{Carbon\Carbon::parse($event->pivot->log_timestamp)->format('Y-m-d H:i')}}</td>
        @endif
      </tr>
      @endforeach
    </x-table>
  </div>

  @endif
</x-layout>
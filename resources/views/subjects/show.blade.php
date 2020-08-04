<x-layout>

  <x-pageheader>
    Subject: {{$subject->subjectID}}
    <x-slot name='button'>
      <x-buttonlink href='/subjects' class='text-orange-700'>Return</x-buttonlink>
    </x-slot>
  </x-pageheader>

  @include('layouts.errormsg')

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
      <td class='font-bold'>Previous Arm</td>
      <td>
        @if ($subject->previous_arm)
        {{$subject->previous_arm->name}}
        @endif
      </td>
    </tr>
    <tr>
      <td class='font-bold'>Status</td>
      <td>{{$subject->subject_status}}</td>
    </tr>
  </x-table>

  <div class='mt-5'>
    <div class='text-l font-bold'>Events</div>
    <x-table>
      <x-slot name='head'>
        <th>Arm</th>
        <th>ID</th>
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
</x-layout>
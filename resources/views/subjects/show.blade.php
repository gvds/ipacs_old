<x-layout>

    <x-pageheader>
      Subject: {{$subject->subjectID}}
    </x-pageheader>

    @include('layouts.errormsg')
  
    <x-table class="table table-sm">
      <x-slot name='head'>
        
      </x-slot>
      <tr>
        <td>Arm</td>
        <td>{{$subject->arm_id}}</td>
      </tr>
      <tr>
        <td>Site</td>
        <td>{{$subject->site}}</td>
      </tr>
      <tr>
        <td>Previous Arm</td>
        <td>{{$subject->previous_arm_id}}</td>
      </tr>
      <tr>
        <td>Status</td>
        <td>{{$subject->subject_status}}</td>
      </tr>
    </x-table>

    <x-buttonlink href='/roles' class='text-orange-500'>Cancel</x-buttonlink>
    
    @foreach ($events as $event)
        {$event}
    @endforeach
  
  </x-layout>
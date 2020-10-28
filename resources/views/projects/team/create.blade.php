<x-layout>

  <x-pageheader>
      Add Team Member
  </x-pageheader>

  @include('layouts.message')
  {{ Form::open(['url' => '/team', 'class' => 'form']) }}
  {{ Form::label('user', 'New Member', ['class'=>'text-sm']) }}
  {{ Form::select('user', $users, null, ['required']) }}
  {{ Form::label('site', 'Site', ['class'=>'text-sm']) }}
  {{ Form::select('site', $sites, null) }}
  {{ Form::submit('Submit', ['class' => 'w-full mt-2']) }}
  <x-buttonlink :href="url('/team')" class='text-orange-500'>Cancel</x-buttonlink>
  {{ Form::close() }}


</x-layout>
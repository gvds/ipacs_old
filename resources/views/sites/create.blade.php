<x-layout>

  <x-pageheader>
      Add Site
  </x-pageheader>

  @include('layouts.message')
  {{ Form::open(['url' => '/sites', 'class' => 'form']) }}
  {{ Form::label('name', 'New Site Name', ['class'=>'text-sm']) }}
  {{ Form::text('name', null, ['required']) }}
  {{ Form::submit('Save Record', ['class' => "w-full mt-2"]) }}
  <x-buttonlink :href="url('/sites')" class='text-orange-500'>Cancel</x-buttonlink>
  {{ Form::close() }}


</x-layout>
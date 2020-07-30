<x-layout>

  <x-pageheader>
      Add Site
  </x-pageheader>

  @include('layouts.errormsg')
  {{ Form::open(['url' => '/sites', 'class' => 'form']) }}
  {{ Form::label('name', 'New Site Name', ['class'=>'text-sm']) }}
  {{ Form::text('name', null, ['required']) }}
  {{ Form::submit('Save Record', ['class' => "w-full mt-2"]) }}
  <x-buttonlink href='/sites' class='text-orange-500'>Cancel</x-buttonlink>
  {{ Form::close() }}


</x-layout>
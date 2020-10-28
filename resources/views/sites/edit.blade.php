<x-layout>

  <x-pageheader>
      Edit Site
  </x-pageheader>

  @include('layouts.message')
  {{ Form::model($site, ['route' => ['sites.update', $site], 'method' => 'PATCH', 'class'=>'form']) }}
  {{ Form::label('name', 'Site Name', ['class'=>'text-sm']) }}
  {{ Form::text('name', null, ['required']) }}
  {{ Form::submit('Save Changes', ['class' => 'w-full mt-2']) }}
  <x-buttonlink :href="url('/sites')" class='text-orange-500'>Cancel</x-buttonlink>
  {{ Form::close() }}


</x-layout>
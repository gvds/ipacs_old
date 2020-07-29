<x-layout>

  <x-pageheader>
      Edit Site
  </x-pageheader>

  @include('layouts.errormsg')
  {{ Form::model($site, ['route' => ['sites.update', $site], 'method' => 'PATCH', 'class'=>'form']) }}
  {{ Form::label('name', 'Site Name', ['class'=>'text-sm']) }}
  {{ Form::text('name', null, ['required']) }}
  {{ Form::submit('Submit', ['class' => 'w-full']) }}
  <x-buttonlink href='/sites' class='text-orange-500'>Cancel</x-buttonlink>
  {{ Form::close() }}


</x-layout>
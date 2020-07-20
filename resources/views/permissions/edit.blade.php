<x-layout>

  <x-pageheader>
    Edit Permission
  </x-pageheader>

  @include('layouts.errormsg')

  {{ Form::model($permission, ['route' => ['permissions.update', $permission], 'method' => 'PATCH', 'class'=>'form']) }}
  {{ Form::label('name', 'Permission Name') }}
  {{ Form::text('name', null, ['required'=>'required']) }}
  {{ Form::label('guard_name', 'Guard Name') }}
  {{ Form::select('guard_name', ['web'=>'web','api'=>'api']) }}
  {{ Form::submit('Save Record', ['class' => "w-full"]) }}
  <x-buttonlink href='/permissions' class='text-orange-500'>Cancel</x-buttonlink>
  {{ Form::close() }}

</x-layout>
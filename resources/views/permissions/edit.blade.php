<x-layout>

  <x-pageheader>
    Edit Permission
  </x-pageheader>

  @include('layouts.errormsg')

  {{ Form::model($permission, ['route' => ['permissions.update', $permission], 'method' => 'PATCH', 'class'=>'form']) }}
  {{ Form::label('name', 'Permission Name') }}
  {{ Form::text('name', null, ['required'=>'required']) }}
  {{ Form::label('scope', 'Scope') }}
  {{ Form::select('scope', ['system'=>'System','project'=>'Project'],null,['required'=>'required']) }}
  {{ Form::label('display_name', 'Display Name') }}
  {{ Form::text('display_name', null, ['required'=>'required']) }}
  {{ Form::label('description', 'Description') }}
  {{ Form::text('description', null) }}
  <!-- {{ Form::label('guard_name', 'Guard Name') }}
  {{ Form::select('guard_name', ['web'=>'web','api'=>'api']) }} -->
  {{ Form::submit('Save Record', ['class' => "w-full mt-4"]) }}
  <x-buttonlink :href="url('/permissions')" class='text-orange-500'>Cancel</x-buttonlink>
  {{ Form::close() }}

</x-layout>
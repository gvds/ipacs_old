<x-layout>

  <x-pageheader>
    Edit Role
  </x-pageheader>

  @include('layouts.errormsg')

  {{ Form::model($role, ['route' => ['roles.update', $role], 'method' => 'PATCH', 'class'=>'form']) }}
  {{ Form::label('name', 'Role Name') }}
  {{ Form::text('name', null, ['required'=>'required']) }}
  {{ Form::label('guard_name', 'Guard Name') }}
  {{ Form::select('guard_name', ['web'=>'web','api'=>'api'], []) }}
  <span class="mr-4 font-bold">Restricted</span> {{ Form::checkbox('restricted', 1) }}
  {{ Form::submit('Save Record', ['class' => "w-full mt-2"]) }}
  <x-buttonlink href='/roles' class='text-orange-500'>Cancel</x-buttonlink>
  {{ Form::close() }}

</x-layout>
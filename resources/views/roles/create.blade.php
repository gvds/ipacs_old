<x-layout>
  <x-pageheader>
    Create New Role
  </x-pageheader>

  @include('layouts.errormsg')

  {{ Form::open(['url' => 'roles', 'class'=>'form']) }}
  {{ Form::label('name', 'Role Name') }}
  {{ Form::text('name', null, ['required'=>'required']) }}
  {{ Form::label('guard_name', 'Guard Name') }}
  {{ Form::select('guard_name', ['web'=>'web','api'=>'api'], 'web') }}
  <span class="mr-4 font-bold">Restricted</span> {{ Form::checkbox('restricted', 1) }}
  {{ Form::submit('Save Record', ['class' => "w-full mt-2"]) }}
  <x-button href='/roles' class='text-orange-500'>Cancel</x-button>
  {{ Form::close() }}

</x-layout>
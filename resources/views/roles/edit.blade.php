<x-layout>

  <x-pageheader>
    Edit Role
  </x-pageheader>

  @include('layouts.errormsg')

  {{ Form::model($role, ['route' => ['roles.update', $role], 'method' => 'PATCH', 'class'=>'form']) }}
  {{ Form::label('name', 'Role Name') }}
  {{ Form::text('name', null, ['required'=>'required']) }}
  {{ Form::label('display_name', 'Display Name') }}
  {{ Form::text('display_name', null, ['required'=>'required']) }}
  {{ Form::label('description', 'Description') }}
  {{ Form::text('description', null) }}
  {{-- {{ Form::label('guard_name', 'Guard Name') }}
  {{ Form::select('guard_name', ['web'=>'web','api'=>'api'], []) }} --}}
  {{ Form::label('restricted', 'Restricted') }}
  {{ Form::radio('restricted', 0) }} No {{ Form::radio('restricted', 1) }} Yes
  {{ Form::submit('Save Record', ['class' => "w-full mt-2"]) }}
  <x-buttonlink :href="url('/roles')" class='text-orange-500'>Cancel</x-buttonlink>
  {{ Form::close() }}

</x-layout>
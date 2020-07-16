<x-layout>

  <x-pageheader>
    Create New Permission
  </x-pageheader>

  @include('layouts.errormsg')

        {!! Form::open(['url' => 'permissions', 'class'=>'form']) !!}
        {!! Form::label('name', 'Permission Name') !!}
        {!! Form::text('name', null, ['required'=>'required']) !!}
        {!! Form::label('guard_name', 'Guard Name') !!}
        {!! Form::select('guard_name', ['web'=>'web','api'=>'api'], 'web') !!}
        {!! Form::submit('Save Record', ['class' => "w-full"]) !!}
        <x-button href='/permissions' class='text-orange-500'>Cancel</x-button>
        {!! Form::close() !!}

      </x-layout>
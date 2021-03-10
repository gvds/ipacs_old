<x-layout>

  <x-pageheader>
    Edit User
  </x-pageheader>

  @include('layouts.message')

  {!! Form::model($user, ['route' => ['user.update', $user], 'method' => 'patch', 'class' => 'form']) !!}
  {!! Form::label('username', 'Username') !!}
  {!! Form::text('username', null, ['required']) !!}
  {!! Form::label('firstname', 'First Name') !!}
  {!! Form::text('firstname', null, ['required']) !!}
  {!! Form::label('surname', 'Surname') !!}
  {!! Form::text('surname', null, ['required']) !!}
  {!! Form::label('email', 'E-Mail Address') !!}
  {!! Form::email('email', null, ['required']) !!}
  {!! Form::label('telephone', 'Telephone Number') !!}
  {!! Form::text('telephone', null, ['placeholder'=>'0## ###-####']) !!}
  {!! Form::label('homesite', 'Site') !!}
  {!! Form::text('homesite', null, ['required']) !!}
  {!! Form::submit('Update Record', ['class' => "w-full mt-2"]) !!}
  <x-buttonlink :href="url('/user')" class='text-orange-500'>Cancel</x-buttonlink>
  {!! Form::close() !!}

</x-layout>

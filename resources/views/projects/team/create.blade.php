<x-layout>

  <x-pageheader>
      Add Team Member
  </x-pageheader>

  @include('layouts.errormsg')
  {{ Form::open(['url' => '/team', 'class' => 'form']) }}
  {{ Form::label('user', 'New Member', ['class'=>'text-sm']) }}
  {{ Form::select('user', $users, null, ['required']) }}
  {{ Form::label('site', 'Site', ['class'=>'text-sm']) }}
  {{ Form::select('site', $sites, null) }}
  <!-- {{ Form::select('site', [''=>'',1=>'SU_MBHG',2=>'MRC_Gambia',3=>'UCRC'], null) }} -->
  {{ Form::submit('Submit', ['class' => 'w-full mt-2']) }}
  <x-buttonlink href='/team' class='text-orange-500'>Cancel</x-buttonlink>
  {{ Form::close() }}


</x-layout>
<x-layout>

  <x-pageheader>
      Edit Team Member: {{$user->fullname}}
  </x-pageheader>
  @include('layouts.errormsg')
  {{ Form::model($user, ['url' => "/team/$user->id/update", 'method' => 'PATCH', 'class' => 'form']) }}
  {{ Form::label('site', 'Site', ['class'=>'text-sm']) }}
  {{ Form::select('site', $sites, $user->pivot->site_id) }}
  {{ Form::submit('Submit', ['class' => 'w-full']) }}
  <x-buttonlink href='/team' class='text-orange-500'>Cancel</x-buttonlink>
  {{ Form::close() }}

</x-layout>
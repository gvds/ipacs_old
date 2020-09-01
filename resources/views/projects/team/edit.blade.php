<x-layout>

  <x-pageheader>
      Edit Team Member: {{$user->fullname}}
  </x-pageheader>
  @include('layouts.errormsg')
  {{ Form::model($user, ['url' => "/team/$user->id/update", 'method' => 'PATCH', 'class' => 'form']) }}
  {{ Form::label('site', 'Site', ['class'=>'text-sm']) }}
  {{ Form::select('site', $sites, $user->pivot->site_id) }}
  {{ Form::label('redcap_api_token', 'REDCap API Token', ['class'=>'text-sm']) }}
  {{ Form::text('redcap_api_token', $user->pivot->redcap_api_token) }}
  {{ Form::submit('Submit', ['class' => 'w-full mt-2']) }}
  <x-buttonlink :href="url('/team')" class='text-orange-500'>Cancel</x-buttonlink>
  {{ Form::close() }}

</x-layout>
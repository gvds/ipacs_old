@props([
'method' => 'POST',
'action' => '',
'model'
])
@if ($method === 'PATCH')
{{ Form::model($model, ['url' => $action,'method' => $method, 'class' => 'w-full max-w-sm bg-gray-100 border border-gray-300 rounded shadow px-8 py-6 text-sm']) }}
{{ Form::text('project', null, ['required','maxlength'=>'50']) }}
@else
{{ Form::open(['url' => $action,'method' => $method, 'class' => 'w-full max-w-sm bg-gray-100 border border-gray-300 rounded shadow px-8 py-6 text-sm']) }}

@endif
    {{ $fields }}
    {{ $slot }}
{{ Form::close() }}
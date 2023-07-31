<x-layout>

    <x-pageheader>
        Bulk Log Derivative Samples (from file)
    </x-pageheader>

    <div x-data={}>
        {{ Form::open(['url' => '/derivative/file', 'class' => 'form', 'method' => 'post', 'files' => true, 'x-ref' => 'samplelistform']) }}
        {{ Form::label('sampletype', 'Derivative Sample Type') }}
        {{ Form::select('sampletype', $sampletypes) }}
        {{ Form::label('samplefile', 'Bulk Assignment Sample File') }}
        {{ Form::file('samplefile', ['x-on:change' => '$refs.samplelistform.submit()']) }}
        {{ Form::close() }}
    </div>

    @include('layouts.message')

</x-layout>

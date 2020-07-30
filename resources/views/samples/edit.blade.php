<x-layout>
    <x-pageheader>
        Edit Event
    </x-pageheader>

    @include('layouts.errormsg')

    {{ Form::model($sample, ['route' => ['samples.update', $sample], 'method' => 'PATCH', 'class'=>'form']) }}
    {{ Form::label('name', 'Sample Name') }}
    {{ Form::text('name', null, ['required'=>'required']) }}
    {{ Form::label('primary', 'Primary') }}
    {{ Form::radio('primary', 0, true, ['class'=>'mb-3']) }} No {{ Form::radio('primary', 1) }} Yes
    {{ Form::label('aliquots', 'Aliquots') }}
    {{ Form::selectRange('aliquots', 1,20) }}
    {{ Form::label('pooled', 'Pooled') }}
    {{ Form::radio('pooled', 0, true, ['class'=>'mb-3']) }} No {{ Form::radio('pooled', 1) }} Yes
    {{ Form::label('defaultVolume', 'Default Volume') }}
    {{ Form::text('defaultVolume', null) }}
    {{ Form::label('volumeUnit', 'Volume Unit') }}
    {{ Form::text('volumeUnit', null) }}
    {{ Form::label('transferDestination', 'Transfer Destination') }}
    {{ Form::text('transferDestination', null) }}
    {{ Form::label('transferSource', 'Transfer Source') }}
    {{ Form::text('transferSource', null) }}
    {{ Form::label('sampleGroup', 'Sample Group') }}
    {{ Form::text('sampleGroup', null) }}
    {{ Form::label('tubeLabelType', 'Tube Label Type') }}
    {{ Form::select('tubeLabelType', $tubeLabelTypes) }}
    {{ Form::label('storageSampleType', 'Storage Sample Type') }}
    {{ Form::text('storageSampleType', null) }}
    {{ Form::label('parentSampleType_id', 'Parent Sample Type') }}
    {{ Form::select('parentSampleType_id', $sampleTypes) }}
    {{ Form::label('active', 'Active') }}
    {{ Form::radio('active', 0, ['class'=>'mb-3']) }} No {{ Form::radio('active', 1, true) }} Yes
    {{ Form::submit('Save Changes', ['class' => "w-full mt-2"]) }}
    <x-buttonlink href='/events' class='text-orange-500'>Cancel</x-buttonlink>
    {{ Form::close() }}

</x-layout>
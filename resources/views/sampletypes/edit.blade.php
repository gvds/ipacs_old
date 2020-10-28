<x-layout>
    <x-pageheader>
        Edit Sample Type
        <x-slot name='button'>
            <x-delConfirm url='/sampletypes/{{$sampletype->id}}' />
        </x-slot>
    </x-pageheader>

    @include('layouts.message')

    {{ Form::model($sampletype, ['route' => ['sampletypes.update', $sampletype], 'method' => 'PATCH', 'class'=>'form']) }}
    {{ Form::label('name', 'Sample Name') }}
    {{ Form::text('name', null, ['required'=>'required']) }}
    {{ Form::label('primary', 'Primary') }}
    {{ Form::radio('primary', 0, true) }} No {{ Form::radio('primary', 1) }} Yes
    {{ Form::label('aliquots', 'Aliquots') }}
    {{ Form::selectRange('aliquots', 1,20) }}
    {{ Form::label('pooled', 'Pooled') }}
    {{ Form::radio('pooled', 0, true) }} No {{ Form::radio('pooled', 1) }} Yes
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
    {{ Form::label('tubeLabelType_id', 'Tube Label Type') }}
    {{ Form::select('tubeLabelType_id', $tubeLabelTypes) }}
    {{ Form::label('storageSampleType', 'Storage Sample Type') }}
    {{ Form::text('storageSampleType', null) }}
    {{ Form::label('parentSampleType_id', 'Parent Sample Type') }}
    {{ Form::select('parentSampleType_id', $sampleTypes) }}
    {{ Form::label('active', 'Active') }}
    {{ Form::radio('active', 0) }} No {{ Form::radio('active', 1, true) }} Yes
    {{ Form::submit('Save Changes', ['class' => "w-full mt-2"]) }}
    <x-buttonlink :href="url('/sampletypes')" class='text-orange-500'>Cancel</x-buttonlink>
    {{ Form::close() }}

</x-layout>
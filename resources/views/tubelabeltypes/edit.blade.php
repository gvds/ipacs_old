<x-layout>

  <x-pageheader>
    Edit Tube Label-Type
  </x-pageheader>

    @include('layouts.message')

    {{ Form::model($tubelabeltype, ['route' => ['tubelabeltype.update', $tubelabeltype], 'method' => 'PATCH', 'class'=>'form']) }}
    {{ Form::label('tubeLabelType', 'Name') }}
    {{ Form::text('tubeLabelType', null, ['required'=>'required']) }}
    {{ Form::label('preregister', 'Preregister') }}
    {{ Form::radio('preregister', 0, true) }} No {{ Form::radio('preregister', 1) }} Yes
    {{ Form::label('registration', 'Registration') }}
    {{ Form::radio('registration', 'range', true) }} Range {{ Form::radio('registration', 'Single') }} Single
    {{ Form::label('barcodeFormat', 'Barcode Format Regex') }}
    {{ Form::text('barcodeFormat', null) }}
    {{ Form::submit('Save Record', ['class' => "w-full mt-2"]) }}
    <x-buttonlink :href="url('/tubelabeltype')" class='text-orange-500'>Cancel</x-buttonlink>
    {{ Form::close() }}

</x-layout>

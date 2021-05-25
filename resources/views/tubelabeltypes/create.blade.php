<x-layout>

  <x-pageheader>
    New Tube Label-Type
  </x-pageheader>

  @include('layouts.message')

  {{ Form::open(['url' => '/tubelabeltype', 'class'=>'form']) }}
  {{ Form::label('tubeLabelType', 'Name') }}
  @if (isset($tubelabeltype))
  {{ Form::text('tubeLabelType', $tubelabeltype->tubeLabelType, ['required'=>'required','readonly']) }}
  @else
  {{ Form::text('tubeLabelType', null, ['required'=>'required']) }}
  @endif
  {{ Form::label('preregister', 'Preregister') }}
  {{ Form::radio('preregister', 0, true) }} No {{ Form::radio('preregister', 1) }} Yes
  {{ Form::label('registration', 'Registration') }}
  {{ Form::radio('registration', 'range', true) }} Range {{ Form::radio('registration', 'Single') }} Single
  {{ Form::label('barcodeFormat', 'Barcode Format Regex') }}
  @if (isset($tubelabeltype))
  {{ Form::text('barcodeFormat', $tubelabeltype->barcodeFormat, ['required'=>'required']) }}
  @else
  {{ Form::text('barcodeFormat', null, ['required'=>'required']) }}
  @endif
  {{ Form::submit('Save Record', ['class' => "w-full mt-2"]) }}
  <x-buttonlink :href="url('/tubelabeltype')" class='text-orange-500'>Cancel</x-buttonlink>
  {{ Form::close() }}

</x-layout>

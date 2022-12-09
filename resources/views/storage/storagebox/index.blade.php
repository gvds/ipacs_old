<x-layout>
    <x-pageheader>
        Manage Storage Boxes
        <x-slot name='button'>
            <x-buttonlink href='/storagebox/create'>
                New Box
            </x-buttonlink>
        </x-slot>
    </x-pageheader>

    @include('layouts.message')

    <div class='font-semibold'>Search for storage box</div>
    {{ Form::open(['url' => '/storagebox/search', 'class' => 'form', 'method' => 'POST']) }}
    {{ Form::label('barcode', 'Box Barcode', ['class'=>'text-sm']) }}
    {{ Form::text('barcode', null, ['required']) }}
    {{ Form::close() }}

    @foreach ($sampletypes as $sampletype)
    <x-buttonlink href='/storagebox?sampletype={{$sampletype->id}}'>{{$sampletype->name}}</x-buttonlink>
    @endforeach
    @foreach ($storageboxes as $storagebox)
    <div><a href="/storagebox/{{$storagebox->id}}">{{$storagebox->barcode}}</a> {{$storagebox->sampletype->name}}</div>
    @endforeach

</x-layout>

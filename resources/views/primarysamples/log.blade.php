<x-layout>

    <x-pageheader>
        Log Primary Samples
    </x-pageheader>

    @include('layouts.errormsg')

    @if(!isset($sampletypes))

    {{ Form::open(['url' => '/primary.log/retrieve', 'class' => 'form', 'method' => 'GET']) }}
    {{ Form::text('pse', null, ['placeholder'=>'Scan PSE barcode...','autocomplete'=>'off', 'autofocus']) }}
    {{ Form::close() }}

    @else
    <h3 class='text-lg font-semibold'>{{$event_subject->subject->subjectID}} &middot; {{$event_subject->event->arm->name}} : {{$event_subject->event->name}}</h3>
    <div class='flex'>
        <div>
        {{ Form::open(['url' => '/primary.log', 'class' => 'form mt-2', 'method' => 'POST']) }}
        {{ Form::hidden('event_subject_id',$event_subject->id)}}
        {{ Form::text('barcode', null, ['placeholder'=>'Scan sample barcode...','autocomplete'=>'off', 'autofocus']) }}
        {{ Form::close() }}
    </div>
    <div class='mt-2 ml-6'>
        <x-buttonlink href='/primary.log'>Log Another Set</x-buttonlink>
    </div>
    </div>

    <div>
        <x-table class='mt-4'>
            @php
            $type = "";
            @endphp
            @foreach ($sampletypes as $sampletype)
            @if ($sampletype->name != $type)
            @if (!$loop->first)
            </tr>
            @endif
            <tr>
                <td>
                    <div class='font-medium w-max-content'>{{$sampletype->name}}</div>
                </td>
                @endif
                <td class='justify-start'>
                    @if ($sampletype->samplestatus_id == 2)
                    <div class='text-sm text-gray-600 bg-green-200 px-2 border-cool-gray-300 border rounded shadow'>
                        {{$sampletype->barcode}}</div>
                    @else
                    <div class='text-sm text-gray-600 bg-cool-gray-200 px-2 border-cool-gray-300 border rounded shadow'>
                        {{$sampletype->barcode}}</div>
                    @endif
                    <div
                        class='flex text-gray-500 text-xs px-2 my-1 mr-1 w-full bg-indigo-200 border border-gray-300 rounded shadow'>
                        {{$sampletype->volume}}</div>
                </td>
                @php
                $type = $sampletype->name;
                @endphp
                @if ($loop->last)
            </tr>
            @endif
            @endforeach
        </x-table>
    </div>
    @endif
</x-layout>
<x-layout>

    <x-pageheader>
        Log Derivative Samples (by Event)
    </x-pageheader>

    {{ Form::open(['url' => '/derivative/pse', 'class' => 'form', 'method' => 'POST']) }}
    {{ Form::text('pse', null, ['placeholder'=>'Scan PSE barcode...','autocomplete'=>'off', 'autofocus']) }}
    {{ Form::close() }}

    @include('layouts.message')

    @if (isset($sampletypes))
    <div x-data="">
        <x-table class='mt-4'>
            @foreach ($sampletypes as $sampletype)
            <tr>
                <td class='font-medium w-max-content py-2'>
                    {{$sampletype->name}}
                </td>
                @foreach ($sampletype->event_samples as $event_sample)
                <td>
                    {{ Form::open(['url' => "/derivative/$event_sample->id", 'method' => 'GET']) }}
                    {{ Form::submit($event_sample->barcode, ['class'=>'text-sm text-gray-600 bg-cool-gray-200 px-2 py-1']) }}
                    {{ Form::close() }}
                </td>
                @endforeach
            </tr>
            @endforeach
        </x-table>
    </div>
    @endif

</x-layout>
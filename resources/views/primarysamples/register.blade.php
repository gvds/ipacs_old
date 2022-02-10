<x-layout>

    <x-pageheader>
        Register Primary Samples
    </x-pageheader>

    {{ Form::open(['url' => '/primary/retrieve', 'class' => 'form', 'method' => 'GET']) }}
    {{ Form::text('pse', null, ['placeholder'=>'Scan PSE barcode...','autocomplete'=>'off', 'autofocus']) }}
    {{ Form::close() }}

    @include('layouts.message')

    @if (isset($sampletypes))
    @php
    $fieldnum = 0;
    @endphp
    <div x-data="">
        {{ Form::open(['url' => '/primary', 'class' => 'form max-w-none mt-2', 'method' => 'POST', 'x-on:keydown.enter.prevent' => '']) }}
        {{ Form::hidden('event_subject_id',$event_subject_id)}}
        {{ Form::label('log', 'Log samples when registering')}}
        <div class='mb-1'>
            No {{ Form::radio('log', 0, ['selected'])}} Yes {{ Form::radio('log', 1)}}
        </div>
        <div>
            <x-table>
                @php
                    $group = "";
                @endphp
                @foreach ($sampletypes as $sampletype)
                @if ($sampletype->sampleGroup !== $group)
                    <tr>
                        <th class='text-left bg-gray-300  leading-none' colspan={{$maxaliquots + 1}}>{{$sampletype->sampleGroup}}</th>
                    </tr>
                    @php
                        $group = $sampletype->sampleGroup;
                    @endphp
                @endif
                <tr>
                    <td>
                        <div class='font-medium w-max-content'>{{$sampletype->name}}</div>
                        <div class='text-gray-500 italic text-xs mt-1 w-max-content'>Volume ({{$sampletype->volumeUnit}})</div>
                    </td>
                    @for ($aliquot = 1; $aliquot < $sampletype->aliquots + 1; $aliquot++)
                        <td class='justify-start'>
                            @foreach ($sampletype->event_samples as $event_sample)
                            @if ($event_sample->aliquot == $aliquot)
                            <div
                                class='text-sm text-gray-600 bg-cool-gray-200 px-2 border-cool-gray-300 border rounded shadow'>
                                {{$event_sample->barcode}}</div>
                            <div
                                class='flex text-gray-500 text-xs px-2 my-1 mr-1 w-full bg-indigo-200 border border-gray-300 rounded shadow'>
                                {{$event_sample->volume}}</div>
                            @continue(2)
                            @endif
                            @endforeach
                            <div>
                                <input type='text' name="type[{{$sampletype->id}}][]" x-ref="{{'bcode' . $fieldnum++}}"
                                    size='12' class='text-sm mt-0 pb-0 leading-none max-w-max-content'
                                    x-on:keydown.enter='$refs.{{'bcode' . $fieldnum}}.focus()'
                                    placeholder="Scan barcode..." />
                            </div>
                            <div class='flex text-gray-500 text-xs mt-1'>
                                <input type='text' name="vol[{{$sampletype->id}}][]"
                                    value='{{$sampletype->defaultVolume}}' size='8'
                                    class='py-0 bg-indigo-200 max-w-min-content mr-1' tabindex='-1'
                                    x-on:keydown.enter='$refs.{{'bcode' . $fieldnum}}.focus()' />
                            </div>
                            <input type="hidden" name="aliquot[{{$sampletype->id}}][]" value="{{$aliquot}}" />
                        </td>
                        @endfor
                </tr>
                @endforeach
            </x-table>
        </div>
        {{ Form::submit('Log Samples', ['class' => 'mt-2 w-40','x-on:click'=>"buttonDisabled = true",'x-bind:disabled'=>"buttonDisabled"]) }}
        {{ Form::close() }}
    </div>
    @endif
</x-layout>

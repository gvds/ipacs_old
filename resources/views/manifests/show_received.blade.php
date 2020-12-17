<x-layout>

    <x-pageheader>
        Shipping Manifest
        {{-- <x-slot name='secondary'>
            <div x-data="confirmation()">
                {{ Form::open(['url' => "/manifest/$manifest->id", 'class' => 'form', 'method' => 'DELETE', 'x-on:submit.prevent'=>'del()', 'x-ref'=>'delform']) }}
        {{ Form::submit('Delete Manifest', ['class'=>'text-red-600']) }}
        {{ Form::close() }}
        </div>
        </x-slot> --}}
    </x-pageheader>

    <div class='text-l font-bold'>
        Source: {{$manifest->source->name}} <br>
        <i>Created by: {{$manifest->user->fullname}}</i>
    </div>

    @include('layouts.message')

    <div class='flex flex-row justify-between'>
        <div>
            {{ Form::open(['url' => "/manifestitem", 'class' => 'form', 'method' => 'PATCH']) }}
            {{ Form::hidden('manifest_id', $manifest->id)}}
            {{ Form::label('barcode', 'Log Manifest Sample as Received')}}
            {{ Form::text('barcode',null,['placeholder'=>'Scan sample barcode...'])}}
            {{ Form::close() }}
        </div>
        <div x-data="confirmation()">
            {{ Form::open(['url' => "/manifest/$manifest->id/receive", 'class' => 'form', 'method' => 'POST', 'x-on:submit.prevent'=>'finalise()', 'x-ref'=>'finaliseform']) }}
            {{ Form::submit('Finalise Manifest Receipt',['class'=>'text-red-600']) }}
            {{ Form::close() }}
        </div>
    </div>

    <x-table class='mt-4'>
        <x-slot name='head'>
            <th>Barcode</th>
            <th>Subject</th>
            <th>Arm</th>
            <th>Event</th>
            <th>Sampletype</th>
            <th>Aliquot</th>
            <th>Volume</th>
        </x-slot>
        @foreach ($manifestItems as $manifestItem)
        <tr class='odd:bg-gray-200'>
            @if ($manifestItem->received)
            <td class='bg-green-300'>{{$manifestItem->event_sample->barcode}}</td>
            @else
            <td>{{$manifestItem->event_sample->barcode}}</td>
            @endif
            <td>{{$manifestItem->event_sample->event_subject->subject->subjectID}}</td>
            <td>{{$manifestItem->event_sample->event_subject->event->arm->name}}</td>
            <td>{{$manifestItem->event_sample->event_subject->event->name}}</td>
            <td>{{$manifestItem->event_sample->sampletype->name}}</td>
            <td>{{$manifestItem->event_sample->aliquot}}</td>
            <td>{{$manifestItem->event_sample->volume}}{{$manifestItem->event_sample->sampletype->volumeUnit}}</td>
        </tr>
        @endforeach
    </x-table>

</x-layout>

<script>
    function confirmation() {
    return {
        finalise() {
            var response = confirm("Click OK to confirm finalising received manifest.\nAll unscanned samples will revert to the source site.\n\nNote that this step is irreversible!");
            if (response === true) {
                this.$refs.finaliseform.submit();
            }
        }
    }
  }
</script>
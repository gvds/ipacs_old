<x-layout>

    <x-pageheader>
        Shipping Manifest
        <x-slot name='secondary'>
            <div x-data="confirmation()">
                {{ Form::open(['url' => "/manifest/$manifest->id", 'class' => 'form', 'method' => 'DELETE', 'x-on:submit.prevent'=>'del()', 'x-ref'=>'delform']) }}
                {{ Form::submit('Delete Manifest', ['class'=>'text-red-600']) }}
                {{ Form::close() }}
            </div>
        </x-slot>
    </x-pageheader>

    <div class='flex justify-between mt-4 mb-1 items-end border border-gray-300 bg-gray-100 rounded-md px-4 py-2'>
        <div class='text-l font-bold'>
            Destination: {{$manifest->destination->name}} <br>
            <i>Created by: {{$manifest->user->fullname}}</i>
        </div>
        <div class='mb-2'>
            <x-buttonlink href="/manifest/{{$manifest->id}}/samplelist">Sample List</x-buttonlink>
        </div>
    </div>

    @include('layouts.message')

    <div class='flex flex-row justify-between items-end'>
        <div>
            {{ Form::open(['url' => "/manifestitem", 'class' => 'form', 'method' => 'POST']) }}
            {{ Form::hidden('manifest_id', $manifest->id)}}
            {{ Form::label('barcode', 'Add Sample to Manifest')}}
            {{ Form::text('barcode',null,['placeholder'=>'Scan sample barcode...'])}}
            {{ Form::close() }}
        </div>
        <div x-data="confirmation()">
            {{ Form::open(['url' => "/manifest/$manifest->id/ship", 'class' => 'form', 'method' => 'POST', 'x-on:submit.prevent'=>'ship()', 'x-ref'=>'shipform']) }}
            {{ Form::submit('Ship Samples',['class'=>'text-blue-700']) }}
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
            <td>{{$manifestItem->event_sample->barcode}}</td>
            <td>{{$manifestItem->event_sample->event_subject->subject->subjectID}}</td>
            <td>{{$manifestItem->event_sample->event_subject->event->arm->name}}</td>
            <td>{{$manifestItem->event_sample->event_subject->event->name}}</td>
            <td>{{$manifestItem->event_sample->sampletype->name}}</td>
            <td>{{$manifestItem->event_sample->aliquot}}</td>
            <td>{{$manifestItem->event_sample->volume}}{{$manifestItem->event_sample->sampletype->volumeUnit}}</td>
            <td>
                {{ Form::open(['url' => "/manifestitem/$manifestItem->id", 'method' => 'DELETE']) }}
                {{ Form::submit('X', ['class'=>'text-red-500 py-0 cursor-pointer']) }}
                {{ Form::close() }}
            </td>
        </tr>
        @endforeach
    </x-table>

</x-layout>

<script>
    function confirmation() {
    return {
        ship() {
            var response = confirm("Click OK to confirm shipping. Note that this step is irreversible!");
            if (response === true) {
                this.$refs.shipform.submit();
            }
        },
        del() {
            var response = confirm("Are you sure you want to delete this manifest?");
            if (response === true) {
                this.$refs.delform.submit();
            }
        }
    }
  }
</script>

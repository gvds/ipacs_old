<x-layout>
    <x-pageheader>
        Manage Samples
    </x-pageheader>

    @include('layouts.message')

    <x-table class='pb-2'>
        <tr>
            <th class='text-left'>Barcode</th>
            <td>{{$event_sample->barcode}}</td>
        </tr>
        <tr>
            <th class='text-left'>Sample Type</th>
            <td>{{$event_sample->sampletype->name}}</td>
        </tr>
        <tr>
            <th class='text-left'>Category</th>
            <td>{{$event_sample->sampletype->primary ? 'Primary' : 'Derivative'}}</td>
        </tr>
        <tr>
            <th class='text-left'>Aliquot</th>
            <td>{{$event_sample->aliquot}}</td>
        </tr>
        <tr>
            <th class='text-left'>Status</th>
            <td>{{$event_sample->status->samplestatus}}</td>
        </tr>
        <tr>
            <th class='text-left'>Parent Barcode</th>
            <td>{{$event_sample->parentBarcode ?? 'NA'}}</td>
        </tr>
        <tr>
            <th class='text-left'>Volume</th>
            <td>{{$event_sample->volume}} {{$event_sample->sampletype->volumeUnit}}</td>
        </tr>
        <tr>
            <th class='text-left'>Logged</th>
            <td>{{($event_sample->samplestatus_id >= 2) ? $event_sample->sampleActor($event_sample->loggedBy)->fullname . ' (' . Carbon\Carbon::parse($event_sample->logTime)->format('Y-m-d H:i') . ')' : 'NA'}}
            </td>
        </tr>
        <tr>
            <th class='text-left'>Used</th>
            <td>{{$event_sample->usedBy ? $event_sample->sampleActor($event_sample->usedBy)->fullname . ' (' . Carbon\Carbon::parse($event_sample->usedTime)->format('Y-m-y H:i') . ')' : 'NA'}}
            </td>
        </tr>
        <tr>
            <th class='text-left'>Storage Location</th>
            <td>{{$event_sample->location ?? 'NA'}}</td>
        </tr>
        <tr>
            <th class='text-left'>Derivatives</th>
            <td>{{$event_sample->derivativeCount()}}</td>
        </tr>
        <tr>
            {{ Form::open(['url' => "/samples/$event_sample->id/volume", 'method' => 'POST']) }}
            <td class='pb-0 mb-0'>
                {{ Form::submit('Update Volume', ['class'=>'my-0']) }}
            </td>
            <td class='pt-0'>
                {{ Form::text('volume', $event_sample->volume), ['width'=>'10','class'=>'mt-0 py-0 w-max-content']}}
            </td>
            {{ Form::close() }}
        </tr>
        @if ($event_sample->derivativeCount() > 0)
        <tr>
            <td colspan=2 class='bg-red-200'>
                This sample cannot be unlogged as it has derivatives
            </td>
        </tr>
        @elseif (in_array($event_sample->samplestatus_id,[5,8]))
        <tr>
            <td colspan=2 class='bg-red-200'>
                This sample cannot be unlogged as it no longer exists
            </td>
        </tr>
        @else
        <tr x-data="{confirm:false,text:'Unlog Sample'}">
            <td>
                <div @click="{confirm = ! confirm; if (confirm) {text = 'Cancel'} else {text = 'Unlog Sample'};}"
                    x-bind:class="{'bg-green-600':confirm}" x-html="text"
                    class='bg-red-600 text-red-50 font-bold px-2 py-1 rounded shaddow-md text-center w-full cursor-pointer'>
                    Unlog Sample
                </div>
            </td>
            <td>
                <x-buttonlink href="/samples/{{$event_sample->id}}/unlog" x-show='confirm'
                    class='bg-red-600 text-red-50'>
                    Confirm Unlog
                </x-buttonlink>
            </td>
        </tr>
        @endif
    </x-table>

</x-layout>

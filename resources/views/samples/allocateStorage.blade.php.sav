<x-layout>
    <x-pageheader>
        Allocate Sample Storage
    </x-pageheader>

    @include('layouts.message')

    <div class='flex space-x-32'>
        <div>
            <div>
                @if (!$lowstorage->isEmpty())
                <div class='text-red-600 font-semibold'>Low Storage Warning</div>
                <x-table>
                    <x-slot name="head">
                        <th>Storage Sample Type</th>
                        <th>Total Capacity</th>
                        <th>Remaining Capacity</th>
                    </x-slot>
                    @foreach ($lowstorage as $storagetype)
                    <tr class='odd:bg-gray-200'>
                        <td>{{$storagetype->storageSampleType}}</td>
                        <td>{{$storagetype->total}}</td>
                        <td>{{$storagetype->total - $storagetype->used}}</td>
                    </tr>
                    @endforeach
                </x-table>
                @endif
            </div>
            <div x-data="restrictSelection()">
                {{ Form::open(['url' => '/samplestore', 'class' => 'form', 'method' => 'POST', 'class' => 'max-w-lg']) }}
                {{ Form::hidden('storageDestination', null, ['x-model'=>'destination'])}}
                <div class='mb-5 mt-2 flex items-center justify-between'>
                    <span class='font-semibold'>Allow Previously Used Locations</span>
                    <span class='flex border border-gray-300 bg-gray-200 px-3 py-1 ml-4 shaddow rounded space-x-3'>
                        <span>{{Form::radio('reuse[]', 0, true)}} No</span>
                        <span>{{Form::radio('reuse[]', 1)}} Yes</span>
                    </span>
                </div>

                <x-table class='w-full'>
                    <x-slot name='head'>
                        <th>Destination</th>
                        <th>Sample Type</th>
                        <th>Samples</th>
                        <th>Select</th>
                    </x-slot>
                    @foreach ($storageDestinations as $storageDestination => $sampleSets)
                    <tr class="border-b border-gray-300">
                        @foreach ($sampleSets as $sampleSet)
                    <tr>
                        @if ($loop->first)
                        <th class="align-top" rowspan="{{count($sampleSets)}}">{{$storageDestination}}</th>
                        @endif

                        <td>{{$sampleSet['name']}}</td>
                        <td>{{$sampleSet['count']}}</td>
                        <td>{{Form::checkbox('sampletype[]', $sampleSet['sampletype_id'], false, ["x-bind:disabled" => "destination!='$storageDestination' && destination!=''", "x-ref" => "$sampleSet[sampletype_id]", "x-on:click" => "updateDestinations('$storageDestination','$sampleSet[sampletype_id]')"])}}
                        </td>
                    </tr>
                    @endforeach
                    </tr>
                    @endforeach
                </x-table>
                {{ Form::submit('Allocate Storage', ['class' => "w-full mt-2",'x-on:click'=>"buttonDisabled = true",'x-bind:disabled'=>"buttonDisabled"]) }}
                {{ Form::close() }}
            </div>
        </div>
        <div class='ml-20'>
            @if (session('unallocated'))
            <x-table>
                <x-slot name=head>
                    <th>Sample Type</th>
                    <th>Unallocated</th>
                </x-slot>
                @foreach (collect(session('unallocated')) as $sampletype=>$count)
                <tr>
                    <td>{{$sampletype}}</td>
                    <td>{{$count}}</td>
                </tr>
                @endforeach
            </x-table>
            @endif
        </div>
    </div>

    <script>
        function restrictSelection() {
            return {
                destinations: {},
                destination: '',
                updateDestinations(destination, element) {
                    if (this.$refs[element].checked) {
                        if (destination in this.destinations) {
                            this.destinations[destination] = this.destinations[destination] + 1;
                        } else {
                            this.destinations[destination] = 1;
                        }
                        this.destination = destination;
                    } else {
                        this.destinations[destination] = this.destinations[destination] - 1;
                        if (this.destinations[destination] == 0) {
                            this.destination = '';
                        }
                    }
                    console.log(this.$refs[element].checked)
                    console.log(element);
                }
            }
        }
    </script>

</x-layout>

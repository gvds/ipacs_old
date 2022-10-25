<x-layout>
    <x-pageheader>
        Virtual Storage Unit Consolidation
    </x-pageheader>

    @include('layouts.message')

    <div class='flex flex-col'>
        <x-table>
            <tr>
                <th>Physical Unit</th>
                <td>{{$virtualUnit->physicalUnit->unitID}}</td>
            </tr>
            <tr>
                <th>Virtual Unit</th>
                <td>{{$virtualUnit->virtualUnit}}</td>
            </tr>
            <tr>
                <th>Project</th>
                <td>{{$virtualUnit->project}}</td>
            </tr>
            <tr>
                <th>SampleType</th>
                <td>{{$virtualUnit->storageSampleType}}</td>
            </tr>
            <tr>
                <td>
                    <x-buttonlink href='/physicalUnit/{{$virtualUnit->physicalUnit_id}}'>
                        Return
                    </x-buttonlink>
                </td>
            </tr>
        </x-table>
        <div x-data="consolidateModal()">
            <x-button class='bg-blue-900 text-blue-100 font-bold min-w-full'
                @click="consolidateconf('virtualUnit','{{$virtualUnit->virtualUnit}}',{{$virtualUnit->id}})">
                Consolidate
            </x-button>

            <style>
                [x-cloak] {
                    display: none !important;
                }
            </style>
            <div class='fixed inset-0 bg-gray-900 opacity-80' x-cloak x-show="showConsolidate"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-80 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-0">
            </div>
            <div class="bg-white text-gray-800 shadow-md max-w-lg h-48 m-auto rounded-md fixed inset-0" x-cloak x-show="showConsolidate"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-75"
                x-transition:enter-end="opacity-100 transform scale-100">
                <div class='flex flex-col h-full justify-between'>
                    <header class='p-4 rounded-t-md'>
                        <h3 class='font-bold text-xl'>Confirm Consolidation</h3>
                    </header>

                    <main class='px-4 mb-4'>
                        <div class='font-bold'>
                            <div class='text-red-900 mb-2'>
                                <b>{{$virtualUnit->virtualUnit}}</b> will be consolidated!<br>
                                Samples stored in this virtual freezer will be irreversably reordered!
                            </div>
                            <div>
                                Are you sure you want to do this?
                            </div>
                        </div>
                    </main>
                    <footer class='flex justify-end space-x-4 bg-gray-200 rounded-b-md px-4 py-3'>
                        <x-button class='bg-gray-800 hover:bg-gray-500' @click='showConsolidate = false'>Cancel</x-button>
                        <form action='/storageconsolidation' method='POST''>
                            @csrf
                            <input type="hidden" name="virtualunit" value={{$virtualUnit->id}}>
                            <x-button class=' bg-blue-800 hover:bg-blue-500'>Confirm</x-button>
                        </form>
                    </footer>
                </div>
            </div>

        </div>

        <div class="font-bold">Consolidation Logs</div>
        <x-table>
            <x-slot name='head'>
                <th>Date</th>
                <th>User</th>
            </x-slot>
            @foreach ($storageconsolidations as $storageconsolidation)
            <tr class="odd:bg-gray-100">
                <td>{{$storageconsolidation->created_at}}</td>
                <td>{{$storageconsolidation->user->fullname}}</td>
                <td>
                    <x-buttonlink href='/storageconsolidation/{{$storageconsolidation->id}}'
                        class='bg-blue-900 text-blue-100 py-1 px-2 mb-3 rounded-md font-bold text-center'>
                        Generate Report
                    </x-buttonlink>
                </td>
            </tr>
            @endforeach
        </x-table>
    </div>
</x-layout>



<script>
    function consolidateModal() {
        return {
            showConsolidate: false,
            consolidateconf(model,modelname,id) {
                this.showConsolidate = true;
            },
        }
    }
</script>

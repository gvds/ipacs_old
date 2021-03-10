<div class='fixed inset-0 bg-gray-900 opacity-80' x-show="showDelete"
    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-80 transform scale-100"
    x-transition:leave-end="opacity-0 transform scale-0">
</div>
<div class="bg-white text-gray-800 shadow-md max-w-lg h-48 m-auto rounded-md fixed inset-0" x-show="showDelete"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-75"
    x-transition:enter-end="opacity-100 transform scale-100">
    <div class='flex flex-col h-full justify-between'>
        <header class='p-4 rounded-t-md'>
            <h3 class='font-bold text-xl'>Confirm Deletion</h3>
        </header>

        <main class='px-4 mb-4'>
            <p class='font-bold text-red-900'>
                <div x-html='msg'>
                    This item will be deleted!
                </div>
                <div>
                    Are you sure you want to do this?
                </div>
            </p>
        </main>

        <footer class='flex justify-end space-x-4 bg-gray-200 rounded-b-md px-4 py-3'>
            <x-button class='bg-gray-800 hover:bg-gray-500' @click='showDelete = false'>Cancel</x-button>
            {{ Form::open(['method' => 'DELETE', 'x-bind:action'=>'url']) }}
            <x-button class='bg-blue-800 hover:bg-blue-500'>Confirm</x-button>
            {{ Form::close() }}
        </footer>
    </div>
</div>

<script>
    function deleteModal() {
        return {
            showDelete: false,
            url: "",
            msg: "",
            deleteconf(model,modelname,id) {
                this.url = "/" + model + "/" + id;
                this.msg = "The " + model + " <b>'" + modelname + "'</b> will be deleted!";
                this.showDelete = true;
            },
        }
    }
</script>

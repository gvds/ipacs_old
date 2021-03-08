<div class='fixed inset-0 bg-gray-900 opacity-80' x-show="show"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-80 transform scale-100"
    x-transition:leave-end="opacity-0 transform scale-0">
</div>
<div class="bg-white text-gray-800 shadow-md max-w-sm h-48 m-auto rounded-md fixed inset-0" x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform scale-75"
    x-transition:enter-end="opacity-100 transform scale-100">
    <div class='flex flex-col h-full justify-between'>
        <header class='p-4 rounded-t-md'>
            <h3 class='font-bold text-xl'>Reset Project Data</h3>
        </header>

        <main class='px-4 mb-4'>
            <p class='font-bold text-red-900'>
                All data for this poject will be deleted!</br>
                Are you sure you want to do this?
            </p>
        </main>

        <footer class='flex justify-end space-x-4 bg-gray-200 rounded-b-md px-4 py-3'>
            <x-button class='bg-gray-800 hover:bg-gray-500' @click='show = false'>Cancel</x-button>
            <a href="" x-bind:href="url">
                <x-button class='bg-blue-800 hover:bg-blue-500'>Continue</x-button>
            </a>
        </footer>
    </div>
</div>

<script>
    function resetModal() {
        return {
            show: false,
            url: "",
            reset(project_id) {
                this.url = "/project/" + project_id + "/reset"
                this.show = true;
            },
        }
    }
</script>

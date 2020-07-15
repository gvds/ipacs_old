<x-layout>

    <x-pageheader>
        Select Project
    </x-pageheader>



    <div class="relative" x-data=search()>
        {{ Form::open(['url' => '', 'class' => 'form', 'method' => 'POST']) }}
        <input type="search" x-model="searchString" x-on:input=search() @click.away={searchString='';projectlist={}} />
        <div class='absolute block bg-white border border-gray-200 shadow px-2 cursor-pointer'
        x-show="Object.values(projectlist).length > 0"
        style="margin-top: -10px;"
        >
            {{-- <template x-for="(project, index) in projectlist" :key="index"> --}}
            <template x-for="(project, index) in Object.values(projectlist)">
                <div x-text="project" class='py-1'></div>
            </template>
        </div>
        {{ Form::close() }}
    </div>

</x-layout>

<script>

    let search = () => {
        return {
            searchString: '',
            projectlist: {},

            search() {
                if(this.searchString.length === 0){
                    this.projectlist = {};
                } else {
                axios.post('/projectlist', {
                    searchString: this.searchString
                })
                .then(response => this.projectlist = response.data);
                }
            }
        };
    }

</script>
<div x-data='subjectSearch()' class="flex flex-col">
    <input type="search" placeholder="Search for subject ID..." name="subjectSearch" x-model="subjectSearch"
        x-on:input.debounce.500="fetchSubject()" @click.outside="clearSearch()"
        class="bg-white focus:outline-none focus:ring border border-gray-300 rounded-lg px-4" autofocus />
    <template x-if="subjects">
        <div class="text-sm flex flex-col bg-white border border-gray-300 rounded shadow-md absolute mt-10 w-56"
            x-transition:enter="transition ease-out duration-250"
            x-transition:enter-start="opacity-0 transform scale-90"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-0">
            <template x-for="[id, subject] in Object.entries(subjects)">
                <a x-text="subject" x-bind:href=`/subjects/${id}`
                    class="flex px-3 py-1 text-xs font-semibold text-gray-700 hover:bg-gray-300">
                </a>
            </template>
        </div>
    </template>
</div>

<script>
    function subjectSearch() {
      return {
        subjectSearch: "",
        subjects: null,
        isLoading: false,
        fetchSubject() {
          if (this.subjectSearch == "") {
            this.subjects = null;
          } else {
            this.isLoading = true;
            fetch(`/subjectsearch/${this.subjectSearch}`)
            .then(response => response.json())
            .then(data => {
              this.isLoading = false;
              this.subjects = data;
            });
          }
        },
        clearSearch() {
          this.subjectSearch = "";
          this.subjects = null;
        }
      };
    }
</script>

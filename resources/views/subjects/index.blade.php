<x-layout>

  <x-pageheader>
    Subject Search
  </x-pageheader>

  @include('layouts.errormsg')


  <div x-data='subjectSearch()' class="md:w-1/3 flex flex-col mt-3">
    <div class='font-medium text-lg'>Subject ID</div>
    <input type="text" name="subjectSearch" x-model="subjectSearch" x-on:keyup="fetchSubject()"
      class="bg-white focus:outline-none focus:shadow-outline border border-gray-300 rounded-lg px-4" autofocus />
    <template x-if="subjects">
      <div class="text-sm flex flex-col bg-white border border-gray-300 rounded shadow-md">
        <template x-for="[id, subject] in Object.entries(subjects)">
          <a x-text="subject" x-bind:href=`/subjects/${id}`
            class="flex px-3 py-1 text-xs font-semibold text-gray-700"></a>
        </template>
      </div>
    </template>
  </div>

</x-layout>

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
      }
    };
  }
</script>
@props(['url' => '/'])

<span x-data="confirmDelete()">
  {{ Form::open(['url' => $url, 'method' => 'DELETE', 'x-on:click.away'=>'clear()']) }}
  <x-buttonlink @click="del()" x-text=" getDeleteText()" class="text-red-50" x-bind:class="getDeleteBg()">Delete
  </x-buttonlink>
  {{ Form::button('Confirm', ['type'=>'submit', "x-show"=>"confirming()", "class"=>"bg-red-600 text-red-50 text-sm font-bold px-2 py-1 rounded shadow-md leading-tight hover:text-indigo-500"]) }}
  {{ Form::close() }}
</span>

@once
@push('scripts')
<script>
  function confirmDelete() {
    return {
      showConfirm: false,
      deleteText: "Delete",
      deleteBgCol: "bg-red-600",
      del() {
        this.showConfirm = !this.showConfirm;
        if (this.showConfirm) {
          this.deleteBgCol = 'bg-green-600';
          this.deleteText = "Cancel"
        } else {
          this.deleteBgCol = 'bg-red-600';
          this.deleteText = "Delete"
        }
      },
      clear() {
        this.showConfirm = false;
        this.deleteBgCol = 'bg-red-600';
        this.deleteText = "Delete"
      },
      confirming() {
        return this.showConfirm === true
      },
      getDeleteText() {
        return this.deleteText
      },
      getDeleteBg() {
        return this.deleteBgCol
      },
    }
  }
</script>
@endpush
@endonce
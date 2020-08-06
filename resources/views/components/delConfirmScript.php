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
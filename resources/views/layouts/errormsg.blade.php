@if ($errors->any())
{{-- <div class="max-w-md bg-red-100 border-4 border-red-500 shadow-md rounded my-2 py-1 px-3 text-sm"> --}}
<div class="max-w-md border-2 border-red-500 bg-red-50 errorbox rounded my-2 py-1 px-3 text-sm">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
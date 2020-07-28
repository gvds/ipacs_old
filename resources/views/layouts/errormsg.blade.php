@if ($errors->any())
<div class="max-w-md border-2 border-red-500 bg-red-50 errorbox rounded my-2 py-1 px-3 text-sm">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
@if ($message = Session::get('error'))
<div class="max-w-md border-2 border-red-500 bg-red-50 errorbox rounded my-2 py-1 px-3 text-sm">
    {{ $message }}
</div>
@endif
@if ($message = Session::get('warning'))
<div class="max-w-md border-2 border-orange-500 bg-orange-50 warningbox rounded my-2 py-1 px-3 text-sm">
    {{ $message }}
</div>
@endif
@php
    $currentProject = \App\project::find(session('currentProject', null));
@endphp
@if ($currentProject)
<div class="text-2xl font-bold text-center text-blue-900">
    {{ $currentProject->project }}
</div>
@endif
<div class="flex justify-between mb-2">
    <div class='text-xl font-bold'>
        {{ $slot }}
    </div>
    {{ $button ?? ''}}
</div>
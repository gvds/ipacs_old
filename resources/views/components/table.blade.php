<div {{ $attributes->merge(['class'=>'inline-block border border-gray-300 rounded shadow-md mb-3']) }}>
    <table class="table-auto text-sm">
        <thead>
            @if ($head ?? null)
            <tr class="border-b border-gray-300 text-center">
                {{ $head }}
            </tr>
            @endif
        </thead>
        <tbody>
            {{$slot}}
        </tbody>
    </table>
</div>
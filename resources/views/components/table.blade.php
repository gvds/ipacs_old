{{-- <div {{ $attributes->merge(['class'=>'inline-block border border-gray-300 rounded shadow-md']) }}> --}}
    <table class="table-auto p-3 border-b mb-3">
        <thead>
            <tr class="border-b border-gray-300 text-center">
                {{ $head }}
            </tr>
        </thead>
        <tbody>
            {{$slot}}
        </tbody>
    </table>
{{-- </div> --}}
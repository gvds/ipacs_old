<div class="inline-block border border-gray-300 rounded shadow-md">
    <table class="table-auto m-3">
        <thead>
            <tr class="border-b border-gray-300 text-center">
                {{ $head }}
            </tr>
        </thead>
        <tbody>
            {{$slot}}
        </tbody>
    </table>
</div>
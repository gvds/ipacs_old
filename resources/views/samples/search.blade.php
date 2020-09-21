<x-layout>
    <x-pageheader>
        Search for Samples
    </x-pageheader>

    @include('layouts.errormsg')

    {{ Form::open(['url' => '/samples/search', 'class' => ' w-auto', 'method' => 'POST']) }}
    <x-table>
        <x-slot name='head'>
            <th>Subject ID List<br /><span class='italic'>Leave blank for all</span></th>
            <th>Events<br /><span class='italic'>Leave blank for all</span></th>
            <th>Sample Types<br /><span class='italic'>Leave blank for all</span></th>
            <th>Sites<br /><span class='italic'>Leave blank for all</span></th>
        </x-slot>
        <tr>
            <td>
                <textarea name="subjectIDlist" id="subjectIDlist" cols="50" rows="12" maxlength='6000' placeholder='List of Subject IDs separate by spaces, commas or new lines' style='resize:none' autocomplete='off' autofocus></textarea>
            </td>
            <td>
                {{ Form::select('events[]', $events, null, ['size'=>'14','multiple'])}}
            </td>
            <td>
                {{ Form::select('sampletypes[]', $sampletypes, null, ['size'=>'14','multiple'])}}
            </td>
            <td>
                {{ Form::select('sites[]', $sites, null, ['size'=>'14','multiple'])}}
            </td>
        </tr>
        <tr>
            <td>
                {{ Form::submit('Search', ['class'=>'w-full'])}}
            </td>
            <td></td>
            <td></td>
            <td>
                {{ Form::reset('Reset', ['class'=>'w-full bg-gray-200 text-orange-600 font-semibold'])}}
            </td>
        </tr>
    </x-table>
    {{ Form::close() }}
</x-layout>
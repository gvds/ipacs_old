@props(['url' => '/'])

<span x-data="confirmDelete()"">
  {{ Form::open(['url' => $url, 'method' => 'DELETE', 'x-on:click.away'=>'clear()']) }}
  <x-buttonlink @click=" del()" x-text=" getDeleteText()" class="text-red-50" x-bind:class="getDeleteBg()">Delete</x-buttonlink>
  {{ Form::button('Confirm', ['type'=>'submit', "x-show"=>"confirming()", "class"=>"bg-red-600 text-red-50 text-sm font-bold px-2 py-1 rounded shadow-md leading-tight hover:text-indigo-500"]) }}
  {{ Form::close() }}
</span>
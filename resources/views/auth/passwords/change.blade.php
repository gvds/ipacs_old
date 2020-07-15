<x-layout>
    <x-pageheader>
        Change password
    </x-pageheader>

    @include('layouts.errormsg')

    <div class='flex justify-center'>
        <form class='form' method="POST" action="{{ route('changePassword') }}">
            {{ csrf_field() }}

            <label for="new-password">Current Password</label>

            <input id="current-password" type="password" name="current-password" required>

            @if ($errors->has('current-password'))
            <span>
                <strong>{{ $errors->first('current-password') }}</strong>
            </span>
            @endif

            <label for="new-password">New Password</label>

            <input id="new-password" type="password" name="new-password" required>

            @if ($errors->has('new-password'))
            <span>
                <strong>{{ $errors->first('new-password') }}</strong>
            </span>
            @endif

            <label for="new-password-confirm">Confirm New
                Password</label>

            <input id="new-password-confirm" type="password" name="new-password_confirmation"
                required />

            <input type="submit" value='Change Password' class="bg-gray-300 mt-2 font-bold hover:text-indigo-500" />
        </form>
    </div>

</x-layout>
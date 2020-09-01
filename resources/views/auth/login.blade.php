<x-layout>

    <x-slot name='centred'>
        <div class="flex w-full flex-col justify-center items-center space-y-6">
            <form class='form' method="POST" action="{{ route('login') }}">
                @csrf

                <label for="username">{{ __('Username') }}</label>

                <input id="username" type="text" @error('username') is-invalid @enderror" name="username"
                    value="{{ old('username') }}" required autocomplete="username" autofocus />

                @error('username')
                <span role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

                <label for="password">{{ __('Password') }}</label>

                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                    name="password" required autocomplete="current-password" />

                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

                <div class="text-gray-700 font-bold my-3">
                    <input class='mr-3' type="checkbox" name="remember" id="remember"
                        {{ old('remember') ? 'checked' : '' }} />

                    {{ __('Remember Me') }}
                </div>

                <input type="submit" value={{ __('Login') }} />

                @if (Route::has('password.request'))
                <a class="font-medium text-indigo-600 ml-20" href="{{ route('password.request') }}">
                    {{ __('Forgot Your Password?') }}
                </a>
                @endif
            </form>
        </div>
    </x-slot>

</x-layout>
<x-layout>

    @include('layouts.message')

    <x-slot name='centred'>
        <div class="flex w-full flex-col justify-center items-center space-y-6">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="">
                    <label for="email" class="">{{ __('E-Mail Address') }}</label>

                    <div class="">
                        <input id="email" type="email" class=" @error('email') is-invalid @enderror"
                            name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>

                <div class="">
                    <label for="password" class="">{{ __('Password') }}</label>

                    <div class="">
                        <input id="password" type="password"
                            class=" @error('password') is-invalid @enderror" name="password" required
                            autocomplete="new-password">

                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>

                <div class="">
                    <label for="password-confirm"
                        class="">{{ __('Confirm Password') }}</label>

                    <div class="">
                        <input id="password-confirm" type="password" class="" name="password_confirmation"
                            required autocomplete="new-password">
                    </div>
                </div>

                <div class=" mb-0">
                    <div class="">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Reset Password') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </x-slot>
</x-layout>
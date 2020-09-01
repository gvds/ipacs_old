<x-layout>

    @include('layouts.errormsg')

    <x-slot name='centred'>
        <div class="flex w-full flex-col justify-center items-center">
            <div class=' border border-gray-300 bg-gray-100 rounded p-4 shadow-md  space-y-6'>
                <div class="text-xl font-semibold text-gray-600">{{ __('Reset Password') }}</div>

                @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="form-group row">
                        <label for="email"
                            class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                        <div class="col-md-6">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <button type="submit"
                                class="font-semibold text-gray-600 border border-gray-300 bg-gray-200 rounded mt-3 px-3 py-2 w-full">
                                {{ __('Send Password Reset Link') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </x-slot>
</x-layout>
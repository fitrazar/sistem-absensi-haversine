@section('title', 'Login')

<x-guest-layout>
    @if ($errors->any())
        <x-alert.error :errors="$errors->all()" />
    @endif

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <x-form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="w-full h-full">
            <div class="flex gap-6 w-full flex-col justify-center items-center lg:flex-row">
                <img src="{{ asset('assets/images/app.svg') }}" class="md:max-w-sm max-w-40 rounded-lg object-cover" />
                <div class="w-full">
                    <!-- Username -->
                    <div>
                        <x-input.input-label for="username" :value="__('Username')" />
                        <x-input.text-input id="username" class="mt-1 w-full" type="text" name="username"
                            :value="old('username')" required autofocus autocomplete="username" />
                        {{-- <x-input.input-error :messages="$errors->get('username')" class="mt-2" /> --}}
                    </div>

                    <!-- Password -->
                    <div class="mt-4">
                        <x-input.input-label for="password" :value="__('Password')" />

                        <x-input.text-input id="password" class="mt-1 w-full" type="password" name="password" required
                            autocomplete="current-password" />

                        {{-- <x-input.input-error :messages="$errors->get('password')" class="mt-2" /> --}}
                    </div>

                    <!-- Remember Me -->
                    <div class="mt-4">
                        <x-input.input-label for="remember" class="label cursor-pointer mr-3">
                            <x-input.checkbox name="remember" id="remember" :title="__('Remember Me')" />
                        </x-input.input-label>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">
                                <x-button.link-button type="button">
                                    {{ __('Daftar Akun') }}
                                </x-button.link-button>
                            </a>
                        @endif

                        <x-button.primary-button class="ms-3" type="submit">
                            {{ __('Log in') }}
                        </x-button.primary-button>
                    </div>
                </div>
            </div>
        </div>


    </x-form>
</x-guest-layout>

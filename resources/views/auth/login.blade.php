<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="mt-4 flex items-center justify-between gap-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="underline text-sm rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" style="color:#2563eb;" onmouseover="this.style.color='#1d4ed8'" onmouseout="this.style.color='#2563eb'" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <div class="flex justify-center mt-6">
            <x-primary-button style="background-color:#16a34a;border-color:#16a34a;" onmouseover="this.style.backgroundColor='#15803d';this.style.borderColor='#15803d'" onmouseout="this.style.backgroundColor='#16a34a';this.style.borderColor='#16a34a'">
                {{ __('Log in') }}
            </x-primary-button>
        </div>

        <div class="mt-4 text-center text-sm text-gray-600">
            <span>Don't have an account?</span>
            <a class="underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 ms-1" style="color:#2563eb;" onmouseover="this.style.color='#1d4ed8'" onmouseout="this.style.color='#2563eb'" href="{{ route('register') }}">
                Create an account
            </a>
        </div>
    </form>
</x-guest-layout>

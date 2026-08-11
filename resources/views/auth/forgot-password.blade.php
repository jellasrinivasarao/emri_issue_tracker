<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Official Email Address -->
        <div>
            <x-input-label for="official_email" :value="__('Official Email')" />
            <x-text-input id="official_email" class="block mt-1 w-full" type="email" name="official_email" :value="old('official_email')" required autofocus :readonly="session('show_reset')" />
            <x-input-error :messages="$errors->get('official_email')" class="mt-2" />
        </div>

        @if(session('show_reset') || old('otp') || old('password'))
            <div class="mt-4">
                <x-input-label for="otp" :value="__('OTP')" />
                <x-text-input id="otp" class="block mt-1 w-full" type="text" name="otp" :value="old('otp')" required maxlength="6" />
                <x-input-error :messages="$errors->get('otp')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="password" :value="__('New Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        @endif

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ session('show_reset') ? __('Reset Password') : __('Send OTP') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

<x-guest-layout>
    <div class="w-full max-w-lg rounded-[32px] border border-slate-200 bg-white/95 p-8 shadow-2xl shadow-slate-300/20 backdrop-blur-xl sm:p-10 lg:max-w-xl">
        <div class="flex flex-col items-center gap-4 text-center">
            <img src="{{ asset('images/logo.png') }}" alt="EMRI Logo" class="w-full max-w-[240px] object-contain" />
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">EMRI ISSUE TRACKER</h1>
                <p class="mt-2 text-sm text-slate-500">Enter your credentials to sign in to your account.</p>
            </div>
        </div>

        <x-auth-session-status class="mt-6 rounded-3xl border border-emerald-100 bg-emerald-50/80 px-4 py-3 text-sm text-emerald-700" :status="session('status')" />

        @php($sessionConflict = session('single_session_conflict'))
        <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
            @csrf
            @if ($sessionConflict)
                <input type="hidden" name="force_login_token" value="{{ session('force_login_token') }}" />
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-800">This user is already logged in on another device. Log out the previous session or continue with the button below.</div>
            @endif

            <div class="space-y-2">
                <label for="login_id" class="block text-sm font-semibold text-slate-700">Username / Email</label>
                <input id="login_id" name="login_id" type="text" value="{{ old('login_id', session('validated_end_user_gid')) }}" @if(session('validated_end_user_gid') || $sessionConflict) readonly @endif required autofocus autocomplete="username" class="w-full rounded-[28px] border border-slate-200 {{ session('validated_end_user_gid') || $sessionConflict ? 'bg-slate-100' : 'bg-slate-50' }} px-4 py-3 text-sm text-slate-900 outline-none transition duration-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-100" placeholder="Enter username or email" />
                @if (! $sessionConflict)
                    <x-input-error :messages="$errors->get('login_id')" class="mt-2 text-sm text-rose-600" />
                @endif
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-medium text-sky-600 hover:text-sky-700">Forgot Password?</a>
                    @endif
                </div>
                <input id="password" name="password" type="password" value="{{ $sessionConflict ? old('password', session('conflict_password')) : '' }}" required autocomplete="current-password" @if($sessionConflict) disabled @endif class="w-full rounded-[28px] border border-slate-200 {{ $sessionConflict ? 'cursor-not-allowed bg-slate-100 text-slate-400' : 'bg-slate-50 text-slate-900' }} px-4 py-3 text-sm outline-none transition duration-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-100" placeholder="{{ $sessionConflict ? 'Password already verified' : 'Enter password' }}" />
                @if ($sessionConflict)
                    <input type="hidden" name="password" value="{{ old('password', session('conflict_password')) }}" />
                @endif
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-rose-600" />
            </div>

            @if (! $sessionConflict)
                <button type="submit" class="mt-2 w-full rounded-[28px] bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-xl shadow-slate-900/10 transition duration-200 hover:bg-slate-800">Sign In</button>
            @endif

            @if ($sessionConflict)
                <button type="submit" name="force_login" value="1" class="w-full rounded-[28px] border border-rose-300 bg-rose-50 px-5 py-3 text-sm font-semibold text-rose-700 transition duration-200 hover:bg-rose-100">
                    Logout Previous Session and Sign In
                </button>
            @endif
        </form>

        <div class="mt-6 text-center text-sm text-slate-500">
            <a href="{{ route('end.user.gid') }}" class="font-medium text-sky-600 hover:text-sky-700">End User Login</a>
        </div>

        <div class="mt-6 text-center text-sm text-slate-500">© 2026 EMRI Green Health Services. All rights reserved.</div>
    </div>
</x-guest-layout>

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

        <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
            @csrf

            <div class="space-y-2">
                <label for="login_id" class="block text-sm font-semibold text-slate-700">Username / Email</label>
                <input id="login_id" name="login_id" type="text" value="{{ old('login_id', session('validated_end_user_gid')) }}" @if(session('validated_end_user_gid')) readonly @endif required autofocus autocomplete="username" class="w-full rounded-[28px] border border-slate-200 {{ session('validated_end_user_gid') ? 'bg-slate-100' : 'bg-slate-50' }} px-4 py-3 text-sm text-slate-900 outline-none transition duration-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-100" placeholder="Enter username or email" />
                <x-input-error :messages="$errors->get('login_id')" class="mt-2 text-sm text-rose-600" />
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-medium text-sky-600 hover:text-sky-700">Forgot Password?</a>
                    @endif
                </div>
                <input id="password" name="password" type="password" required autocomplete="current-password" class="w-full rounded-[28px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition duration-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-100" placeholder="Enter password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-rose-600" />
            </div>

            <div class="flex items-center justify-between text-sm text-slate-600">
                <label class="inline-flex items-center gap-2">
                    <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500" />
                    Remember me
                </label>
            </div>

            <button type="submit" class="mt-2 w-full rounded-[28px] bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-xl shadow-slate-900/10 transition duration-200 hover:bg-slate-800">Sign In</button>

            @if (session('single_session_conflict'))
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

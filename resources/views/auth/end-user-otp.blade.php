<x-guest-layout>
    <div class="w-full max-w-lg rounded-[28px] border border-slate-200 bg-white/95 p-8 shadow-2xl shadow-slate-300/20 sm:p-10">
        <div class="text-center">
            <img src="{{ asset('images/logo.png') }}" alt="EMRI Logo" class="mx-auto w-full max-w-[210px] object-contain" />
            <h1 class="mt-6 text-2xl font-semibold text-slate-900">Verify Your OTP</h1>
            <p class="mt-2 text-sm text-slate-500">A one-time password was sent to the registered email for GID <strong>{{ $gid }}</strong>.</p>
        </div>

        @if(session('error'))
            <div class="mt-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('end.user.otp.verify') }}" class="mt-8 space-y-5">
            @csrf
            <div class="space-y-2">
                <label for="otp" class="block text-sm font-semibold text-slate-700">One-Time Password</label>
                <input id="otp" name="otp" type="text" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required autofocus autocomplete="one-time-code" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-center text-lg tracking-[0.35em] text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100" placeholder="000000" />
                @error('otp')<p class="text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="w-full rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-600/20 transition hover:bg-sky-700">Verify OTP</button>
        </form>

        <form method="POST" action="{{ route('end.user.gid.check') }}" class="mt-4 text-center">
            @csrf
            <input type="hidden" name="gid" value="{{ $gid }}" />
            <input type="hidden" name="resend" value="1" />
            <button type="submit" class="text-sm font-medium text-sky-600 hover:text-sky-700">Resend OTP</button>
        </form>
    </div>
</x-guest-layout>

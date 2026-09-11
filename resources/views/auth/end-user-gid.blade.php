<x-guest-layout>
    <div class="w-full max-w-lg rounded-[28px] border border-slate-200 bg-white/95 p-8 shadow-2xl shadow-slate-300/20 sm:p-10">
        <div class="text-center">
            <img src="{{ asset('images/logo.png') }}" alt="EMRI Logo" class="mx-auto w-full max-w-[210px] object-contain" />
            <h1 class="mt-6 text-2xl font-semibold text-slate-900">End User GID Verification</h1>
            <p class="mt-2 text-sm text-slate-500">Enter your employee GID to receive a one-time password.</p>
        </div>

        @if(session('error'))
            <div class="mt-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('end.user.gid.check') }}" class="mt-8 space-y-5">
            @csrf
            <div class="space-y-2">
                <label for="gid" class="block text-sm font-semibold text-slate-700">GID</label>
                <input id="gid" name="gid" type="text" value="{{ old('gid') }}" required autofocus autocomplete="username" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100" placeholder="Enter employee GID" />
                @error('gid')<p class="text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="w-full rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-600/20 transition hover:bg-sky-700">Check / Continue</button>
        </form>

        <div class="mt-6 text-center text-sm text-slate-500">An OTP will be sent to your registered email address.</div>
    </div>
</x-guest-layout>

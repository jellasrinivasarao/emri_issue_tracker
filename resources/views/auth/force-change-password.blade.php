@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto py-12">
    <h2 class="text-2xl font-semibold mb-6">Change Your Password</h2>

    <form method="POST" action="{{ route('password.force.change.update') }}" class="space-y-4">
        @csrf

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
            <input id="password" name="password" type="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
            @if($errors->has('password'))
                <p class="text-sm text-red-600">{{ $errors->first('password') }}</p>
            @endif
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
        </div>

        <div>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md">Change Password</button>
        </div>
    </form>
    @php $flashStatus = $status ?? session('status'); @endphp
    @if($flashStatus)
        <div id="password-status" class="mt-6 rounded-md bg-green-50 border border-green-200 p-4 text-green-800">
            {{ $flashStatus }}
        </div>

        <script>
            (function(){
                // Redirect after short delay so the user sees the success message
                const redirectTo = @json($redirectTo ?? route('role.dashboard'));
                setTimeout(function(){ window.location.href = redirectTo; }, 2200);
            })();
        </script>
    @endif
</div>
@endsection

{{-- Success Message --}}
@if(session('success'))

<div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4">

    <div class="flex items-center gap-3">

        <span class="text-green-600">
            ✓
        </span>

        <p class="text-sm font-medium text-green-700">
            {{ session('success') }}
        </p>

    </div>

</div>

@endif


{{-- Error Message --}}
@if(session('error'))

<div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4">

    <div class="flex items-center gap-3">

        <span class="text-red-600">
            ✕
        </span>

        <p class="text-sm font-medium text-red-700">
            {{ session('error') }}
        </p>

    </div>

</div>

@endif


{{-- Warning Message --}}
@if(session('warning'))

<div class="mb-6 rounded-lg border border-yellow-200 bg-yellow-50 p-4">

    <div class="flex items-center gap-3">

        <span class="text-yellow-600">
            ⚠
        </span>

        <p class="text-sm font-medium text-yellow-700">
            {{ session('warning') }}
        </p>

    </div>

</div>

@endif


{{-- Info Message --}}
@if(session('info'))

<div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4">

    <div class="flex items-center gap-3">

        <span class="text-blue-600">
            ℹ
        </span>

        <p class="text-sm font-medium text-blue-700">
            {{ session('info') }}
        </p>

    </div>

</div>

@endif


{{-- Validation Errors --}}
@if($errors->any())

<div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4">

    <div class="mb-2 flex items-center gap-3">

        <span class="text-red-600">
            ✕
        </span>

        <p class="text-sm font-semibold text-red-700">
            Please correct the following errors:
        </p>

    </div>


    <ul class="ml-7 list-disc space-y-1">

        @foreach($errors->all() as $error)

        <li class="text-sm text-red-600">
            {{ $error }}
        </li>

        @endforeach

    </ul>

</div>

@endif
@props(['status'])

@php

    $styles = match ($status) {

        'BRD Raised' => 'bg-slate-100 text-slate-700',

        'Received at HO' => 'bg-blue-50 text-blue-700',

        'Sent to Vendor' => 'bg-indigo-50 text-indigo-700',

        'Clarification Pending' => 'bg-amber-50 text-amber-700',

        'In Progress' => 'bg-blue-50 text-blue-700',

        'UAT Requested' => 'bg-violet-50 text-violet-700',

        'UAT In Progress' => 'bg-purple-50 text-purple-700',

        'UAT Completed' => 'bg-emerald-50 text-emerald-700',

        'Moved to Production' => 'bg-green-50 text-green-700',

        'Closed' => 'bg-slate-900 text-white',

        'On Hold' => 'bg-red-50 text-red-700',

        default => 'bg-slate-100 text-slate-600',
    };

@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $styles }}">

    <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-current"></span>

    {{ $status }}

</span>
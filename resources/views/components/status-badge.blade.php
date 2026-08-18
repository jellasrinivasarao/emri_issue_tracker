@props(['status'])

@php

    $class = match ($status) {

        'BRD Raised' =>
            'bg-slate-100 text-slate-700',

        'Received at HO' =>
            'bg-blue-100 text-blue-700',

        'Sent to Vendor' =>
            'bg-indigo-100 text-indigo-700',

        'Clarification Pending' =>
            'bg-amber-100 text-amber-700',

        'In Progress' =>
            'bg-blue-100 text-blue-700',

        'UAT Requested',
        'UAT In Progress' =>
            'bg-violet-100 text-violet-700',

        'UAT Completed' =>
            'bg-emerald-100 text-emerald-700',

        'Moved to Production' =>
            'bg-green-100 text-green-700',

        'Closed' =>
            'bg-slate-900 text-white',

        'On Hold' =>
            'bg-red-100 text-red-700',

        default =>
            'bg-slate-100 text-slate-600',
    };

@endphp


<span
    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $class }}">

    <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-current"></span>

    {{ $status }}

</span>
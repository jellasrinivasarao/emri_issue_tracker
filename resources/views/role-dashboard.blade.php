@php
use Illuminate\Support\Facades\Route;
@endphp

<x-app-layout :without-sidebar="true">

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ auth()->user()->role_names ?: 'Role Dashboard' }}
        </h2>
    </x-slot>


    {{-- =========================================================
        DASHBOARD
    ========================================================== --}}

    <div class="py-10">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- =================================================
                WELCOME CARD
            ================================================== --}}

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

                    <div>

                        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">
                            Role-based landing
                        </p>

                        <h1 class="mt-2 text-3xl font-semibold text-slate-900">
                            {{ auth()->user()->name ?? auth()->user()->login_id }}
                        </h1>

                        <p class="mt-3 text-sm text-slate-600">
                            This page shows the menu items available for your assigned role.
                        </p>

                    </div>


                    <div
                        class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-700">

                        {{ auth()->user()->role_names ?: 'No role assigned' }}

                    </div>

                </div>

            </div>


            {{-- =================================================
                MENU ACCESS
            ================================================== --}}

            <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">

                    <div>

                        <h3 class="text-lg font-semibold text-slate-900">
                            Menu access for your role
                        </h3>

                        <p class="mt-1 text-sm text-slate-600">
                            Select a permitted section from the list below.
                        </p>

                    </div>


                    <div class="rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700">

                        {{ auth()->user()->menus->count() }} items

                    </div>

                </div>


                {{-- =================================================
                    MENU CARDS
                ================================================== --}}

                <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">

                    @forelse(auth()->user()->menus as $menu)

                    @php

                    $isRaiseIssue = $menu->route_name === 'raise.issue';

                    $href = $menu->route_name && Route::has($menu->route_name)
                    ? route($menu->route_name)
                    : ($menu->uri ?? '#');

                    @endphp


                    <a href="{{ $href }}" @if($isRaiseIssue) data-raise-issue="true" @endif class="group rounded-[22px] border border-slate-200
                                   bg-slate-50 p-6 transition
                                   hover:border-slate-300
                                   hover:bg-slate-100
                                   hover:shadow-sm">

                        <div class="flex items-center justify-between gap-3">

                            <div class="rounded-2xl bg-slate-900 p-3 text-white shadow-sm">

                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="{{ $menu->icon ?? 'M4 6h16M4 12h16M4 18h16' }}" />
                                </svg>

                            </div>


                            <span class="rounded-full bg-white px-3 py-1
                                           text-[10px] font-semibold
                                           uppercase tracking-[0.24em]
                                           text-slate-500">
                                Open
                            </span>

                        </div>


                        <h4 class="mt-6 text-xl font-semibold text-slate-900">

                            {{ $menu->display_name }}

                        </h4>


                        <p class="mt-2 text-sm text-slate-600">

                            {{ $menu->route_name ?? $menu->uri }}

                        </p>

                    </a>

                    @empty

                    <div class="rounded-3xl border border-slate-200
                                   bg-slate-50 p-8 text-slate-600 shadow-sm">

                        <h4 class="text-lg font-semibold text-slate-900">
                            No menu access assigned
                        </h4>

                        <p class="mt-2 text-sm">
                            Contact your administrator to assign
                            role-based menu permissions.
                        </p>

                    </div>

                    @endforelse

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
        RAISE ISSUE MODAL
    ========================================================== --}}

    <div id="raiseIssueModal" class="fixed inset-0 z-[99999] hidden" role="dialog" aria-modal="true"
        aria-labelledby="raiseIssueModalTitle">

        {{-- =====================================================
            BACKDROP
        ====================================================== --}}

        <div id="raiseIssueModalBackdrop" class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm"></div>


        {{-- =====================================================
            MODAL CONTAINER
        ====================================================== --}}

        <div class="relative flex min-h-screen items-center
                   justify-center p-4 sm:p-6">

            <div id="raiseIssueModalBox" class="relative flex w-full max-w-6xl
                       max-h-[94vh] flex-col overflow-hidden
                       rounded-2xl bg-white shadow-2xl">

                {{-- =================================================
                    MODAL HEADER
                ================================================== --}}

                <div class="flex shrink-0 items-center justify-between
                           border-b border-slate-200 bg-white
                           px-6 py-4">

                    <div>

                        <p class="text-[10px] font-semibold uppercase
                                   tracking-[0.25em] text-blue-600">
                            Issue Management
                        </p>


                        <h2 id="raiseIssueModalTitle" class="mt-1 text-xl font-bold text-slate-900">
                            Raise New Issue
                        </h2>

                    </div>


                    <button type="button" id="raiseIssueModalClose" class="flex h-9 w-9 items-center
                               justify-center rounded-full
                               text-slate-500 transition
                               hover:bg-slate-100
                               hover:text-slate-900" aria-label="Close">

                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>

                    </button>

                </div>


                {{-- =================================================
                    LOADING
                ================================================== --}}

                <div id="raiseIssueModalLoading" class="hidden flex-1 items-center
                           justify-center p-12">

                    <div class="text-center">

                        <div class="mx-auto h-10 w-10 animate-spin
                                   rounded-full border-4
                                   border-slate-200
                                   border-t-blue-600"></div>


                        <p class="mt-4 text-sm text-slate-500">
                            Loading Raise Issue...
                        </p>

                    </div>

                </div>


                {{-- =================================================
                    FORM CONTENT
                ================================================== --}}

                <div id="raiseIssueModalContent" class="min-h-0 flex-1 overflow-y-auto">
                    {{-- AJAX form loads here --}}
                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
        JAVASCRIPT
    ========================================================== --}}

    <script>
    document.addEventListener('DOMContentLoaded', function() {

        /*
        |--------------------------------------------------------------------------
        | ELEMENTS
        |--------------------------------------------------------------------------
        */

        const modal = document.getElementById('raiseIssueModal');

        const modalContent = document.getElementById(
            'raiseIssueModalContent'
        );

        const modalLoading = document.getElementById(
            'raiseIssueModalLoading'
        );

        const modalClose = document.getElementById(
            'raiseIssueModalClose'
        );

        const modalBackdrop = document.getElementById(
            'raiseIssueModalBackdrop'
        );


        /*
        |--------------------------------------------------------------------------
        | SAFETY CHECK
        |--------------------------------------------------------------------------
        */

        if (!modal) {

            console.error(
                'Raise Issue Modal: modal element not found.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | OPEN MODAL
        |--------------------------------------------------------------------------
        */

        window.openRaiseIssueModal = function() {

            modal.classList.remove('hidden');

            document.body.classList.add('overflow-hidden');


            /*
            | Clear old content
            */

            if (modalContent) {
                modalContent.innerHTML = '';
            }


            /*
            | Show loading
            */

            if (modalLoading) {

                modalLoading.classList.remove('hidden');

                modalLoading.classList.add('flex');

            }


            /*
            |--------------------------------------------------------------------------
            | LOAD RAISE ISSUE FORM
            |--------------------------------------------------------------------------
            */

            fetch(
                    "{{ route('raise.issue.modal') }}", {
                        method: 'GET',

                        credentials: 'same-origin',

                        headers: {
                            'Accept': 'text/html',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }
                )
                .then(function(response) {

                    if (!response.ok) {

                        throw new Error(
                            'Unable to load Raise Issue form. HTTP status: ' +
                            response.status
                        );

                    }

                    return response.text();

                })
                .then(function(html) {

                    /*
                    | Hide loader
                    */

                    if (modalLoading) {

                        modalLoading.classList.add('hidden');

                        modalLoading.classList.remove('flex');

                    }


                    /*
                    | Insert form
                    */

                    if (modalContent) {

                        modalContent.innerHTML = html;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | INITIALIZE FORM
                    |--------------------------------------------------------------------------
                    */

                    if (
                        typeof window.initializeRaiseIssueForm ===
                        'function'
                    ) {

                        window.initializeRaiseIssueForm();

                    }

                })
                .catch(function(error) {

                    console.error(
                        'Raise Issue modal error:',
                        error
                    );


                    if (modalLoading) {

                        modalLoading.classList.add('hidden');

                        modalLoading.classList.remove('flex');

                    }


                    if (modalContent) {

                        modalContent.innerHTML = `
                            <div class="flex min-h-[300px]
                                        items-center justify-center
                                        p-8">

                                <div class="text-center">

                                    <div class="mx-auto flex h-12 w-12
                                                items-center justify-center
                                                rounded-full bg-red-100">

                                        <svg
                                            class="h-6 w-6 text-red-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"
                                            />
                                        </svg>

                                    </div>


                                    <h3
                                        class="mt-4 text-sm
                                               font-semibold
                                               text-slate-900"
                                    >
                                        Unable to load Raise Issue
                                    </h3>


                                    <p
                                        class="mt-2 text-sm
                                               text-slate-500"
                                    >
                                        Please refresh the page
                                        and try again.
                                    </p>

                                </div>

                            </div>
                        `;

                    }

                });

        };


        /*
        |--------------------------------------------------------------------------
        | CLOSE MODAL
        |--------------------------------------------------------------------------
        */

        window.closeRaiseIssueModal = function() {

            modal.classList.add('hidden');

            document.body.classList.remove(
                'overflow-hidden'
            );


            if (modalContent) {

                modalContent.innerHTML = '';

            }

        };


        /*
        |--------------------------------------------------------------------------
        | RAISE ISSUE MENU CLICK
        |--------------------------------------------------------------------------
        */

        const raiseIssueLinks =
            document.querySelectorAll(
                '[data-raise-issue="true"]'
            );


        raiseIssueLinks.forEach(function(link) {

            link.addEventListener(
                'click',
                function(event) {

                    /*
                    | Stop normal route navigation
                    */

                    event.preventDefault();

                    event.stopPropagation();


                    /*
                    | Open modal
                    */

                    window.openRaiseIssueModal();

                }
            );

        });


        /*
        |--------------------------------------------------------------------------
        | CLOSE BUTTON
        |--------------------------------------------------------------------------
        */

        if (modalClose) {

            modalClose.addEventListener(
                'click',
                function() {

                    window.closeRaiseIssueModal();

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | BACKDROP
        |--------------------------------------------------------------------------
        */

        if (modalBackdrop) {

            modalBackdrop.addEventListener(
                'click',
                function() {

                    window.closeRaiseIssueModal();

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | ESC KEY
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'keydown',
            function(event) {

                if (event.key === 'Escape') {

                    if (
                        !modal.classList.contains('hidden')
                    ) {

                        window.closeRaiseIssueModal();

                    }

                }

            }
        );

    });
    </script>

</x-app-layout>
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

                    <a href="#" @if($isRaiseIssue) data-raise-issue="true" @endif
                        class="group rounded-[22px] border border-slate-200 bg-slate-50 p-6">


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

        console.log('Role Dashboard JS loaded');


        /*
        |--------------------------------------------------------------------------
        | GET ELEMENTS
        |--------------------------------------------------------------------------
        */

        const raiseIssueModal =
            document.getElementById('raiseIssueModal');

        const raiseIssueContent =
            document.getElementById('raiseIssueModalContent');

        const raiseIssueLoading =
            document.getElementById('raiseIssueModalLoading');

        const raiseIssueClose =
            document.getElementById('raiseIssueModalClose');

        const raiseIssueBackdrop =
            document.getElementById('raiseIssueModalBackdrop');


        /*
        |--------------------------------------------------------------------------
        | VALIDATE MODAL
        |--------------------------------------------------------------------------
        */

        if (!raiseIssueModal) {

            console.error(
                'ERROR: #raiseIssueModal not found.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | OPEN RAISE ISSUE MODAL
        |--------------------------------------------------------------------------
        */

        function openRaiseIssueModal() {

            console.log(
                'Opening Raise Issue Modal...'
            );


            /*
            |--------------------------------------------------------------------------
            | Show modal
            |--------------------------------------------------------------------------
            */

            raiseIssueModal.classList.remove('hidden');

            document.body.classList.add(
                'overflow-hidden'
            );


            /*
            |--------------------------------------------------------------------------
            | Clear previous content
            |--------------------------------------------------------------------------
            */

            if (raiseIssueContent) {

                raiseIssueContent.innerHTML = '';

            }


            /*
            |--------------------------------------------------------------------------
            | Show loader
            |--------------------------------------------------------------------------
            */

            if (raiseIssueLoading) {

                raiseIssueLoading.classList.remove(
                    'hidden'
                );

                raiseIssueLoading.classList.add(
                    'flex'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Load form
            |--------------------------------------------------------------------------
            */

            fetch(
                    "{{ route('raise.issue.modal') }}", {
                        method: 'GET',

                        credentials: 'same-origin',

                        cache: 'no-store',

                        headers: {
                            'Accept': 'text/html',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }
                )
                .then(function(response) {

                    console.log(
                        'Raise Issue response:',
                        response.status
                    );


                    if (!response.ok) {

                        throw new Error(
                            'HTTP ' + response.status
                        );

                    }


                    return response.text();

                })
                .then(function(html) {

                    console.log(
                        'Raise Issue form loaded successfully'
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Hide loader
                    |--------------------------------------------------------------------------
                    */

                    if (raiseIssueLoading) {

                        raiseIssueLoading.classList.add(
                            'hidden'
                        );

                        raiseIssueLoading.classList.remove(
                            'flex'
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Insert form
                    |--------------------------------------------------------------------------
                    */

                    if (raiseIssueContent) {

                        raiseIssueContent.innerHTML = html;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Initialize dynamically loaded form
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
                        'Raise Issue modal loading failed:',
                        error
                    );


                    if (raiseIssueLoading) {

                        raiseIssueLoading.classList.add(
                            'hidden'
                        );

                        raiseIssueLoading.classList.remove(
                            'flex'
                        );

                    }


                    if (raiseIssueContent) {

                        raiseIssueContent.innerHTML = `
                    <div class="flex min-h-[300px]
                                items-center justify-center p-8">

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
                                class="mt-4 text-sm font-semibold
                                       text-slate-900"
                            >
                                Unable to load Raise Issue
                            </h3>

                            <p
                                class="mt-2 text-sm text-slate-500"
                            >
                                Please try again.
                            </p>

                        </div>

                    </div>
                `;

                    }

                });

        }


        /*
        |--------------------------------------------------------------------------
        | CLOSE RAISE ISSUE MODAL
        |--------------------------------------------------------------------------
        */

        function closeRaiseIssueModal() {

            console.log(
                'Closing Raise Issue Modal'
            );


            raiseIssueModal.classList.add(
                'hidden'
            );


            document.body.classList.remove(
                'overflow-hidden'
            );


            if (raiseIssueContent) {

                raiseIssueContent.replaceChildren();

            }


            if (raiseIssueLoading) {

                raiseIssueLoading.classList.add(
                    'hidden'
                );

                raiseIssueLoading.classList.remove(
                    'flex'
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | MAKE FUNCTIONS GLOBAL
        |--------------------------------------------------------------------------
        |
        | If another script needs to open/close the modal,
        | these are available globally.
        |
        */

        window.openRaiseIssueModal =
            openRaiseIssueModal;

        window.closeRaiseIssueModal =
            closeRaiseIssueModal;


        /*
        |--------------------------------------------------------------------------
        | RAISE ISSUE MENU CLICK
        |--------------------------------------------------------------------------
        */

        const raiseIssueTriggers =
            document.querySelectorAll(
                '[data-raise-issue="true"]'
            );


        console.log(
            'Raise Issue menu count:',
            raiseIssueTriggers.length
        );


        raiseIssueTriggers.forEach(
            function(trigger) {

                trigger.addEventListener(
                    'click',
                    function(event) {

                        /*
                        | Prevent <a href="#"> navigation
                        */

                        event.preventDefault();

                        event.stopPropagation();


                        console.log(
                            'Raise Issue menu clicked'
                        );


                        openRaiseIssueModal();

                    }
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | CLOSE BUTTON
        |--------------------------------------------------------------------------
        */

        if (raiseIssueClose) {

            raiseIssueClose.addEventListener(
                'click',
                function() {

                    closeRaiseIssueModal();

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | BACKDROP CLICK
        |--------------------------------------------------------------------------
        */

        if (raiseIssueBackdrop) {

            raiseIssueBackdrop.addEventListener(
                'click',
                function() {

                    closeRaiseIssueModal();

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

                if (
                    event.key === 'Escape' &&
                    !raiseIssueModal.classList.contains('hidden')
                ) {

                    closeRaiseIssueModal();

                }

            }
        );


    });
    </script>

</x-app-layout>
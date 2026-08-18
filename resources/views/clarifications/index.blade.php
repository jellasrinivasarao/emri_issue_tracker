@extends('layouts.app')

@section('title', 'Clarifications')

@section('page-title', 'Clarifications')

@section('content')

<div class="mx-auto max-w-5xl space-y-4">

    @foreach($clarifications as $clarification)

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">

                <div>

                    <a
                        href="{{ route(
                            'requirements.show',
                            $clarification->requirement
                        ) }}"
                        class="font-semibold text-blue-900">

                        {{ $clarification->requirement->requirement_no }}

                    </a>

                    <p class="text-sm text-slate-600">
                        {{ $clarification->requirement->title }}
                    </p>

                </div>


                <span
                    class="rounded-full px-3 py-1 text-xs font-semibold
                    {{ $clarification->status === 'Open'
                        ? 'bg-amber-100 text-amber-700'
                        : 'bg-green-100 text-green-700' }}">

                    {{ $clarification->status }}

                </span>

            </div>


            <div class="p-6">

                <div class="rounded-xl bg-slate-50 p-4">

                    <p class="text-sm text-slate-700">
                        {{ $clarification->message }}
                    </p>

                    <p class="mt-2 text-xs text-slate-400">

                        {{ $clarification->user->name ?? '-' }}
                        ·
                        {{ $clarification->created_at->format('d M Y H:i') }}

                    </p>

                </div>


                @foreach($clarification->replies as $reply)

                    <div class="ml-8 mt-3 rounded-xl border border-slate-100 p-4">

                        <p class="text-sm">
                            {{ $reply->message }}
                        </p>

                        <p class="mt-2 text-xs text-slate-400">

                            {{ $reply->user->name ?? '-' }}
                            ·
                            {{ $reply->created_at->format('d M Y H:i') }}

                        </p>

                    </div>

                @endforeach


                @if($clarification->status === 'Open')

                    <form
                        method="POST"
                        action="{{ route(
                            'clarifications.reply',
                            $clarification
                        ) }}"
                        class="mt-5">

                        @csrf

                        <textarea
                            name="message"
                            rows="3"
                            required
                            placeholder="Write a reply..."
                            class="w-full rounded-xl border-slate-300 text-sm"></textarea>


                        <div class="mt-3 flex justify-end">

                            <button
                                class="rounded-xl bg-blue-900 px-4 py-2 text-sm font-semibold text-white">

                                Reply

                            </button>

                        </div>

                    </form>


                    <form
                        method="POST"
                        action="{{ route(
                            'clarifications.close',
                            $clarification
                        ) }}"
                        class="mt-2 flex justify-end">

                        @csrf

                        <button
                            class="text-xs font-semibold text-slate-500 hover:text-red-600">

                            Close Clarification

                        </button>

                    </form>

                @endif

            </div>

        </div>

    @endforeach


    {{ $clarifications->links() }}

</div>

@endsection
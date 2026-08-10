<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Issue Category Master
        </h2>
    </x-slot>


    <div class="py-8">

        <div class="mx-auto max-w-7xl px-4">


            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">


                <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-6 py-5">

                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">
                            Issue Categories
                        </h3>

                        <p class="text-sm text-slate-600">
                            Manage issue categories used for support routing.
                        </p>
                    </div>


                    <a href="{{ route('admin.issue-categories.create') }}"
                        class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">

                        Add Category

                    </a>

                </div>



                <div class="px-6 py-5">


                    @if(session('success'))

                    <div class="mb-5 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        {{ session('success') }}
                    </div>

                    @endif


                    @if(session('error'))

                    <div class="mb-5 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        {{ session('error') }}
                    </div>

                    @endif



                    <form method="GET" class="mb-5 grid gap-4 md:grid-cols-3">


                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search code or category name"
                            class="rounded-xl border border-slate-200 px-3 py-2.5">


                        <select name="status" class="rounded-xl border border-slate-200 px-3 py-2.5">


                            <option value="">
                                All Status
                            </option>


                            <option value="1" @selected(request('status')==='1' )>
                                Active
                            </option>


                            <option value="0" @selected(request('status')==='0' )>
                                Inactive
                            </option>


                        </select>


                        <button class="rounded-xl bg-slate-800 px-5 py-2.5 text-sm font-semibold text-white">

                            Search

                        </button>


                    </form>





                    <div class="overflow-x-auto">


                        <table class="w-full text-sm">


                            <thead class="bg-slate-100">

                                <tr>

                                    <th class="px-4 py-3 text-left">
                                        Code
                                    </th>

                                    <th class="px-4 py-3 text-left">
                                        Category Name
                                    </th>

                                    <th class="px-4 py-3 text-left">
                                        Description
                                    </th>

                                    <th class="px-4 py-3 text-left">
                                        Status
                                    </th>


                                    <th class="px-4 py-3 text-right">
                                        Action
                                    </th>

                                </tr>

                            </thead>



                            <tbody>


                                @forelse($issueCategories as $category)


                                <tr class="border-b">


                                    <td class="px-4 py-3">
                                        {{ $category->category_code }}
                                    </td>


                                    <td class="px-4 py-3 font-medium">
                                        {{ $category->category_name }}
                                    </td>


                                    <td class="px-4 py-3">
                                        {{ $category->description }}
                                    </td>


                                    <td class="px-4 py-3">

                                        @if($category->is_active)

                                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs text-emerald-700">
                                            Active
                                        </span>

                                        @else

                                        <span class="rounded-full bg-rose-100 px-3 py-1 text-xs text-rose-700">
                                            Inactive
                                        </span>

                                        @endif

                                    </td>


                                    <td class="px-4 py-3 text-right">


                                        <a href="{{ route('admin.issue-categories.edit',$category->issue_category_id) }}"
                                            class="mr-3 text-blue-600">

                                            Edit

                                        </a>



                                        <form method="POST"
                                            action="{{ route('admin.issue-categories.destroy',$category->issue_category_id) }}"
                                            class="inline">

                                            @csrf
                                            @method('DELETE')


                                            <button onclick="return confirm('Delete this category?')"
                                                class="text-rose-600">

                                                Delete

                                            </button>


                                        </form>


                                    </td>


                                </tr>


                                @empty

                                <tr>

                                    <td colspan="5" class="px-4 py-5 text-center text-slate-500">

                                        No issue categories found.

                                    </td>

                                </tr>


                                @endforelse


                            </tbody>


                        </table>


                    </div>


                    <div class="mt-5">

                        {{ $issueCategories->links() }}

                    </div>


                </div>


            </div>


        </div>

    </div>


</x-app-layout>
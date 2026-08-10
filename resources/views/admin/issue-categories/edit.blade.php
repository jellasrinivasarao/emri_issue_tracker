<x-app-layout>

    <x-slot name="header">

        <h2 class="text-xl font-semibold text-gray-800">
            Edit Issue Category
        </h2>

    </x-slot>



    <div class="py-8">

        <div class="mx-auto max-w-4xl px-4">


            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">


                <form method="POST"
                    action="{{route('admin.issue-categories.update',$issueCategory->issue_category_id)}}"
                    class="space-y-5 px-6 py-6">


                    @csrf
                    @method('PUT')


                    <div>

                        <label class="mb-1 block text-sm font-medium">
                            Category Code
                        </label>

                        <input name="category_code" value="{{$issueCategory->category_code}}"
                            class="w-full rounded-xl border px-3 py-2.5">

                    </div>



                    <div>

                        <label class="mb-1 block text-sm font-medium">
                            Category Name
                        </label>

                        <input name="category_name" value="{{$issueCategory->category_name}}"
                            class="w-full rounded-xl border px-3 py-2.5">

                    </div>



                    <div>

                        <label class="mb-1 block text-sm font-medium">
                            Description
                        </label>

                        <textarea name="description" rows="5"
                            class="w-full rounded-xl border px-3 py-2.5">{{$issueCategory->description}}</textarea>

                    </div>



                    <div>

                        <label>

                            <input type="checkbox" name="is_active" value="1" @if($issueCategory->is_active) checked
                            @endif>

                            Active

                        </label>


                    </div>



                    <div class="flex justify-end gap-3 border-t pt-5">


                        <a href="{{route('admin.issue-categories.index')}}" class="rounded-xl border px-5 py-2.5">

                            Cancel

                        </a>


                        <button class="rounded-xl bg-emerald-600 px-5 py-2.5 text-white">

                            Update Category

                        </button>


                    </div>


                </form>


            </div>

        </div>

    </div>


</x-app-layout>
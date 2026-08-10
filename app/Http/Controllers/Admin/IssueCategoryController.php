<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\Admin\IssueCategoryRequest;
use App\Models\IssueCategory;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class IssueCategoryController extends Controller
{
    public function index(): View
    {

        $issueCategories = IssueCategory::query()

            ->when(
                request('search'),
                function ($query, $search) {

                    $query->where(function ($q) use ($search) {

                        $q->where(
                            'category_code',
                            'LIKE',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'category_name',
                            'LIKE',
                            "%{$search}%"
                        );

                    });

                }
            )

            ->when(
                request('status') !== null &&
                request('status') !== '',
                function ($query) {

                    $query->where(
                        'is_active',
                        request('status')
                    );

                }
            )

            ->orderBy('category_name')

            ->paginate(20)

            ->withQueryString();



        return view(
            'admin.issue_categories.index',
            compact('issueCategories')
        );
    }



    public function create(): View
    {
        return view(
            'admin.issue_categories.create'
        );
    }



    public function store(
        IssueCategoryRequest $request
    ): RedirectResponse {


        DB::transaction(function () use ($request) {


            IssueCategory::create([


                'category_code' =>
                    strtoupper(
                        trim(
                            $request->category_code
                        )
                    ),


                'category_name' =>
                    trim(
                        $request->category_name
                    ),


                'description' =>
                    $request->filled('description')
                        ? trim($request->description)
                        : null,


                'is_active' =>
                    $request->boolean('is_active'),


                'created_by' =>
                    auth()->id(),

            ]);


        });



        return redirect()

            ->route(
                'admin.issue-categories.index'
            )

            ->with(
                'success',
                'Issue Category created successfully.'
            );
    }





    public function edit(
        IssueCategory $issueCategory
    ): View {


        return view(
            'admin.issue_categories.edit',
            compact(
                'issueCategory'
            )
        );

    }





    public function update(
        IssueCategoryRequest $request,
        IssueCategory $issueCategory
    ): RedirectResponse {


        DB::transaction(function () use (
            $request,
            $issueCategory
        ) {


            $issueCategory->update([


                'category_code' =>
                    strtoupper(
                        trim(
                            $request->category_code
                        )
                    ),


                'category_name' =>
                    trim(
                        $request->category_name
                    ),


                'description' =>
                    $request->filled('description')
                        ? trim($request->description)
                        : null,


                'is_active' =>
                    $request->boolean('is_active'),


                'updated_by' =>
                    auth()->id(),

            ]);


        });



        return redirect()

            ->route(
                'admin.issue-categories.index'
            )

            ->with(
                'success',
                'Issue Category updated successfully.'
            );

    }





    public function destroy(
        IssueCategory $issueCategory
    ): RedirectResponse {


        /*
         * Issue Category is a parent master.
         *
         * Do not delete when dependent
         * transaction records exist.
         */


        if (
            DB::table('txn_issue')
                ->where(
                    'issue_category_id',
                    $issueCategory->issue_category_id
                )
                ->exists()
        ) {


            return back()->with(
                'error',
                'Issue Category cannot be deleted because issues are associated with it.'
            );

        }



        $issueCategory->delete();



        return redirect()

            ->route(
                'admin.issue-categories.index'
            )

            ->with(
                'success',
                'Issue Category deleted successfully.'
            );

    }
}
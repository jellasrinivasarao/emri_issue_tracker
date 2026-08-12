<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IssueCategory;
use Illuminate\Support\Facades\Schema;

class IssueCategoryConfigurationController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'category_code' => ['required', 'string', 'max:100'],
            'category_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $cat = new IssueCategory();
        $cat->category_code = $data['category_code'];
        $cat->category_name = $data['category_name'];
        $cat->description = $data['description'] ?? null;
        $cat->is_active = 1;
        if (Schema::hasColumn($cat->getTable(), 'created_by')) {
            $cat->created_by = auth()->id();
        }
        $cat->save();

        return redirect()->route('issue.category.configuration')->with('success', 'Issue category created.');
    }

    public function update(Request $request, int $id)
    {
        $cat = IssueCategory::findOrFail($id);

        $data = $request->validate([
            'category_code' => ['required', 'string', 'max:100'],
            'category_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $cat->category_code = $data['category_code'];
        $cat->category_name = $data['category_name'];
        $cat->description = $data['description'] ?? null;
        if (Schema::hasColumn($cat->getTable(), 'updated_by')) {
            $cat->updated_by = auth()->id();
        }
        $cat->save();

        return redirect()->route('issue.category.configuration')->with('success', 'Issue category updated.');
    }

    public function toggle(Request $request, int $id)
    {
        $cat = IssueCategory::findOrFail($id);
        $cat->is_active = ! $cat->is_active;
        if (Schema::hasColumn($cat->getTable(), 'updated_by')) {
            $cat->updated_by = auth()->id();
        }
        $cat->save();

        return redirect()->route('issue.category.configuration')->with('success', $cat->is_active ? 'Activated.' : 'Deactivated.');
    }

    public function destroy(int $id)
    {
        $cat = IssueCategory::findOrFail($id);
        $cat->delete();

        return redirect()->route('issue.category.configuration')->with('success', 'Issue category deleted.');
    }
}

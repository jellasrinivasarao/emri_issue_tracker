<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IssueRoutingRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AutomaticRoutingController extends Controller
{
    public function index(Request $request): View
    {
        $query = IssueRoutingRule::query();

        if ($search = $request->input('search')) {
            $search = trim($search);
            $query->where(function ($q) use ($search) {
                $q->where('rule_code', 'LIKE', "%{$search}%")
                    ->orWhere('rule_name', 'LIKE', "%{$search}%");
            });
        }

        $rules = $query->orderBy('rule_name')->paginate(20)->withQueryString();

        return view('admin.operational.automatic-routing', compact('rules'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'rule_code' => 'required|string|max:50',
            'rule_name' => 'required|string|max:255',
            'priority' => 'nullable|string|max:50',
            'routing_level' => 'nullable|integer|min:0',
            'is_default' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ]);

        IssueRoutingRule::create([
            'rule_code' => strtoupper($data['rule_code']),
            'rule_name' => $data['rule_name'],
            'priority' => $data['priority'] ?? null,
            'routing_level' => $data['routing_level'] ?? null,
            'is_default' => isset($data['is_default']) ? (bool)$data['is_default'] : false,
            'is_active' => isset($data['is_active']) ? (bool)$data['is_active'] : true,
        ]);

        return redirect()->route('automatic.routing')->with('success', 'Routing rule created.');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $rule = IssueRoutingRule::findOrFail($id);

        $data = $request->validate([
            'rule_code' => 'required|string|max:50',
            'rule_name' => 'required|string|max:255',
            'priority' => 'nullable|string|max:50',
            'routing_level' => 'nullable|integer|min:0',
            'is_default' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ]);

        $rule->update([
            'rule_code' => strtoupper($data['rule_code']),
            'rule_name' => $data['rule_name'],
            'priority' => $data['priority'] ?? $rule->priority,
            'routing_level' => $data['routing_level'] ?? $rule->routing_level,
            'is_default' => isset($data['is_default']) ? (bool)$data['is_default'] : $rule->is_default,
            'is_active' => isset($data['is_active']) ? (bool)$data['is_active'] : $rule->is_active,
        ]);

        return redirect()->route('automatic.routing')->with('success', 'Routing rule updated.');
    }

    public function toggle($id): RedirectResponse
    {
        $rule = IssueRoutingRule::findOrFail($id);
        $rule->is_active = !$rule->is_active;
        $rule->save();

        return redirect()->route('automatic.routing')->with('success', 'Routing rule status updated.');
    }

    public function destroy($id): RedirectResponse
    {
        $rule = IssueRoutingRule::findOrFail($id);
        $rule->delete();

        return redirect()->route('automatic.routing')->with('success', 'Routing rule deleted.');
    }
}

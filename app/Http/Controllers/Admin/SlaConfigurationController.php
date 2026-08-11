<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SlaPolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SlaConfigurationController extends Controller
{
    public function index(Request $request): View
    {
        $query = SlaPolicy::query()->with('calendar');

        if ($search = $request->input('search')) {
            $search = trim($search);
            $query->where(function ($q) use ($search) {
                $q->where('sla_policy_code', 'LIKE', "%{$search}%")
                    ->orWhere('sla_policy_name', 'LIKE', "%{$search}%");
            });
        }

        $slas = $query->orderBy('sla_policy_id','desc')->paginate(20)->withQueryString();

        return view('admin.operational.sla-configuration', compact('slas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'sla_policy_code' => 'required|string|max:50',
            'sla_policy_name' => 'required|string|max:255',
            'response_time_minutes' => 'nullable|integer|min:0',
            'resolution_time_minutes' => 'nullable|integer|min:0',
            'calendar_id' => 'nullable|integer',
            'is_active' => 'sometimes|boolean',
        ]);

        SlaPolicy::create([
            'sla_policy_code' => strtoupper($data['sla_policy_code']),
            'sla_policy_name' => $data['sla_policy_name'],
            'response_time_minutes' => $data['response_time_minutes'] ?? null,
            'resolution_time_minutes' => $data['resolution_time_minutes'] ?? null,
            'calendar_id' => $data['calendar_id'] ?? null,
            'is_active' => isset($data['is_active']) ? (bool)$data['is_active'] : true,
        ]);

        return redirect()->route('sla.configuration')->with('success','SLA policy created.');
    }

    public function update(Request $request, $sla_id): RedirectResponse
    {
        $sla = SlaPolicy::findOrFail($sla_id);

        $data = $request->validate([
            'sla_policy_code' => 'required|string|max:50',
            'sla_policy_name' => 'required|string|max:255',
            'response_time_minutes' => 'nullable|integer|min:0',
            'resolution_time_minutes' => 'nullable|integer|min:0',
            'calendar_id' => 'nullable|integer',
            'is_active' => 'sometimes|boolean',
        ]);

        $sla->update([
            'sla_policy_code' => strtoupper($data['sla_policy_code']),
            'sla_policy_name' => $data['sla_policy_name'],
            'response_time_minutes' => $data['response_time_minutes'] ?? $sla->response_time_minutes,
            'resolution_time_minutes' => $data['resolution_time_minutes'] ?? $sla->resolution_time_minutes,
            'calendar_id' => $data['calendar_id'] ?? $sla->calendar_id,
            'is_active' => isset($data['is_active']) ? (bool)$data['is_active'] : $sla->is_active,
        ]);

        return redirect()->route('sla.configuration')->with('success','SLA policy updated.');
    }

    public function toggle($sla_id): RedirectResponse
    {
        $sla = SlaPolicy::findOrFail($sla_id);
        $sla->is_active = !$sla->is_active;
        $sla->save();

        return redirect()->route('sla.configuration')->with('success','SLA status toggled.');
    }

    public function destroy($sla_id): RedirectResponse
    {
        $sla = SlaPolicy::findOrFail($sla_id);
        $sla->delete();

        return redirect()->route('sla.configuration')->with('success','SLA policy deleted.');
    }
}

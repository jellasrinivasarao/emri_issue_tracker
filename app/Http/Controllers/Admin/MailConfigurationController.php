<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MailConfiguration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MailConfigurationController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'state_id' => ['nullable', 'integer', 'exists:mst_state,state_id'],
            'to_emails' => ['required'],
            'cc_emails' => ['nullable'],
        ]);

        $to = is_array($data['to_emails']) ? $data['to_emails'] : array_filter(array_map('trim', explode(',', (string) $data['to_emails'])));
        $cc = isset($data['cc_emails']) ? (is_array($data['cc_emails']) ? $data['cc_emails'] : array_filter(array_map('trim', explode(',', (string) $data['cc_emails'])))) : [];

        $mc = new MailConfiguration();
        [$mc->state_id, $mc->state_name, $mc->recipient_type, $mc->vendor_id] = $this->mailScope($request);
        $mc->to_emails = $to;
        $mc->cc_emails = $cc;
        $mc->is_active = 1;
        if (Schema::hasColumn($mc->getTable(), 'created_by')) {
            $mc->created_by = auth()->id();
        }
        $mc->save();

        $route = $request->routeIs('mail.configuration.*') ? 'mail.configuration' : 'notification.configuration';
        return redirect()->route($route)->with('success', 'Mail configuration saved.');
    }

    public function update(Request $request, int $id)
    {
        $mc = MailConfiguration::findOrFail($id);

        $data = $request->validate([
            'state_id' => ['nullable', 'integer', 'exists:mst_state,state_id'],
            'to_emails' => ['required'],
            'cc_emails' => ['nullable'],
        ]);

        $to = is_array($data['to_emails']) ? $data['to_emails'] : array_filter(array_map('trim', explode(',', (string) $data['to_emails'])));
        $cc = isset($data['cc_emails']) ? (is_array($data['cc_emails']) ? $data['cc_emails'] : array_filter(array_map('trim', explode(',', (string) $data['cc_emails'])))) : [];

        [$mc->state_id, $mc->state_name, $mc->recipient_type, $mc->vendor_id] = $this->mailScope($request);
        $mc->to_emails = $to;
        $mc->cc_emails = $cc;
        if (Schema::hasColumn($mc->getTable(), 'updated_by')) {
            $mc->updated_by = auth()->id();
        }
        $mc->updated_at = now();
        $mc->save();

        $route = $request->routeIs('mail.configuration.*') ? 'mail.configuration' : 'notification.configuration';
        return redirect()->route($route)->with('success', 'Mail configuration updated.');
    }

    public function toggle(Request $request, int $id)
    {
        $mc = MailConfiguration::findOrFail($id);
        $mc->is_active = ! $mc->is_active;
        if (Schema::hasColumn($mc->getTable(), 'updated_by')) {
            $mc->updated_by = auth()->id();
        }
        $mc->updated_at = now();
        $mc->save();

        $route = $request->routeIs('mail.configuration.*') ? 'mail.configuration' : 'notification.configuration';
        return redirect()->route($route)->with('success', $mc->is_active ? 'Configuration activated.' : 'Configuration deactivated.');
    }

    public function toggleApplication(Request $request, int $id, int $applicationId)
    {
        $mc = MailConfiguration::findOrFail($id);
        $applicationIds = array_values(array_unique(array_map('intval', $mc->application_ids ?: array_filter([(int) $mc->application_id]))));

        if (! in_array($applicationId, $applicationIds, true)) {
            return redirect()->route('mail.configuration')->with('error', 'Application is not part of this configuration.');
        }

        $mc->is_active = ! $mc->is_active;
        if (Schema::hasColumn($mc->getTable(), 'updated_by')) {
            $mc->updated_by = auth()->id();
        }
        $mc->updated_at = now();
        $mc->save();

        return redirect()->route('mail.configuration')->with('success', $mc->is_active ? 'Application enabled.' : 'Application disabled.');
    }

    public function destroy(int $id)
    {
        $mc = MailConfiguration::findOrFail($id);
        $mc->delete();

        return redirect()->route('notification.configuration')->with('success', 'Mail configuration deleted.');
    }

    private function mailScope(Request $request): array
    {
        $user = auth()->user();

        if ($user?->hasRole('Central Admin') || $user?->hasRole('HO Admin') || $user?->hasRole('Vendor Admin')) {
            if ($user?->hasRole('Vendor Admin')) {
                return [null, 'All States', 'vendor', $user->vendor_id ?: null];
            }

            return [null, 'All States', 'ho', null];
        }

        $stateIds = collect(preg_split('/\s*,\s*/', (string) ($user?->state_id ?? ''), -1, PREG_SPLIT_NO_EMPTY))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->all();
        $stateId = (int) $request->input('state_id');

        if (! $stateId || ! in_array($stateId, $stateIds, true)) {
            throw ValidationException::withMessages(['state_id' => 'Select one of your assigned states.']);
        }

        $stateName = DB::table('mst_state')
            ->where('state_id', $stateId)
            ->where('is_active', 1)
            ->value('state_name');

        if (! $stateName) {
            throw ValidationException::withMessages(['state_id' => 'The selected state is not active.']);
        }

        return [$stateId, $stateName, 'state', null];
    }

    public function projects(Request $request)
    {
        $query = DB::table('mst_project as p')
            ->select('p.project_id', 'p.project_name')
            ->where('p.is_active', 1)
            ->orderBy('p.project_name');

        if ($request->filled('state_id') && $request->input('state_id') !== 'all') {
            $query->join('map_project_state as ps', 'ps.project_id', '=', 'p.project_id')
                ->where('ps.state_id', (int) $request->input('state_id'))
                ->where('ps.is_active', 1)
                ->distinct();
        }

        return response()->json($query->get());
    }

    public function applications(Request $request)
    {
        $applications = DB::table('map_project_application_module as m')
            ->join('mst_application as a', 'a.application_id', '=', 'm.application_id')
            ->where('m.project_id', (int) $request->input('project_id'))
            ->where('a.is_active', 1)
            ->when(Schema::hasColumn('map_project_application_module', 'is_active'), fn ($query) => $query->where('m.is_active', 1))
            ->orderBy('a.application_name')
            ->distinct()
            ->get(['a.application_id', 'a.application_name']);

        return response()->json($applications);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MailConfiguration;
use Illuminate\Support\Facades\Schema;

class MailConfigurationController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'state_name' => ['required', 'string', 'max:255'],
            'to_emails' => ['required'],
            'cc_emails' => ['nullable'],
        ]);

        $to = is_array($data['to_emails']) ? $data['to_emails'] : array_filter(array_map('trim', explode(',', (string) $data['to_emails'])));
        $cc = isset($data['cc_emails']) ? (is_array($data['cc_emails']) ? $data['cc_emails'] : array_filter(array_map('trim', explode(',', (string) $data['cc_emails'])))) : [];

        $mc = new MailConfiguration();
        if ($request->filled('state_id')) {
            $mc->state_id = $request->state_id;
        }
        $mc->state_name = $data['state_name'];
        $mc->to_emails = $to;
        $mc->cc_emails = $cc;
        $mc->is_active = 1;
        if (Schema::hasColumn($mc->getTable(), 'created_by')) {
            $mc->created_by = auth()->id();
        }
        $mc->save();

        return redirect()->route('notification.configuration')->with('success', 'Mail configuration saved.');
    }

    public function update(Request $request, int $id)
    {
        $mc = MailConfiguration::findOrFail($id);

        $data = $request->validate([
            'state_name' => ['required', 'string', 'max:255'],
            'to_emails' => ['required'],
            'cc_emails' => ['nullable'],
        ]);

        $to = is_array($data['to_emails']) ? $data['to_emails'] : array_filter(array_map('trim', explode(',', (string) $data['to_emails'])));
        $cc = isset($data['cc_emails']) ? (is_array($data['cc_emails']) ? $data['cc_emails'] : array_filter(array_map('trim', explode(',', (string) $data['cc_emails'])))) : [];

        $mc->state_name = $data['state_name'];
        $mc->to_emails = $to;
        $mc->cc_emails = $cc;
        if (Schema::hasColumn($mc->getTable(), 'updated_by')) {
            $mc->updated_by = auth()->id();
        }
        $mc->save();

        return redirect()->route('notification.configuration')->with('success', 'Mail configuration updated.');
    }

    public function toggle(Request $request, int $id)
    {
        $mc = MailConfiguration::findOrFail($id);
        $mc->is_active = ! $mc->is_active;
        if (Schema::hasColumn($mc->getTable(), 'updated_by')) {
            $mc->updated_by = auth()->id();
        }
        $mc->save();

        return redirect()->route('notification.configuration')->with('success', $mc->is_active ? 'Configuration activated.' : 'Configuration deactivated.');
    }

    public function destroy(int $id)
    {
        $mc = MailConfiguration::findOrFail($id);
        $mc->delete();

        return redirect()->route('notification.configuration')->with('success', 'Mail configuration deleted.');
    }
}

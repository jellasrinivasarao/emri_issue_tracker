<?php

namespace App\Http\Controllers;

use App\Models\MailConfiguration;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PageController extends Controller
{
    public function roleDashboard(): View
    {
        return view('role-dashboard');
    }

    public function roleIssueDashboard(Request $request): View
    {
        $user = auth()->user();

        $roleIds = collect($user?->roles ?? collect())
            ->pluck('role_id')
            ->map(fn ($roleId) => (int) $roleId)
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($roleIds) && ! empty($user?->user_id)) {
            $roleIds = DB::table('map_user_role')
                ->where('user_id', $user->user_id)
                ->where('is_active', 1)
                ->pluck('role_id')
                ->map(fn ($roleId) => (int) $roleId)
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        $roleNames = [];
        if (! empty($roleIds)) {
            $roleNames = DB::table('mst_role')
                ->whereIn('role_id', $roleIds)
                ->pluck('role_name')
                ->map(fn ($roleName) => strtolower((string) $roleName))
                ->all();
        } elseif (! empty($user?->role_id)) {
            $roleNames = [strtolower((string) DB::table('mst_role')->where('role_id', $user->role_id)->value('role_name'))];
        }

        $roleText = implode(' ', array_filter($roleNames));
        
        // Initialize all role detection variables
        $isHoAdmin = str_contains($roleText, 'ho admin');
        $isHoIt = str_contains($roleText, 'ho it');
        $isHoRole = $isHoAdmin || $isHoIt || str_contains($roleText, 'head office') || str_contains($roleText, 'head-office');
        $isVendorAdmin = str_contains($roleText, 'vendor admin');
        $isVendorIt = str_contains($roleText, 'vendor it');
        $isVendorRole = $isVendorAdmin || $isVendorIt || str_contains($roleText, 'vendor');
        $isStateAdmin = str_contains($roleText, 'state admin');
        $isStateIt = str_contains($roleText, 'state it');
        $isStateRole = $isStateAdmin || $isStateIt || str_contains($roleText, 'state');
        
        $vendorId = Schema::hasColumn('mst_user', 'vendor_id') ? $user->vendor_id : null;

        $statusRows = DB::table('map_role_issue_status as m')
            ->join('mst_issue_status as s', 'm.status_id', '=', 's.status_id')
            ->whereIn('m.role_id', $roleIds)
            ->where('m.is_allowed', 1)
            ->where(function ($query) {
                $query->where('s.is_active', 1)->orWhereNull('s.is_active');
            })
            ->orderBy('m.display_order')
            ->orderBy('s.status_name')
            ->get(['m.status_id', 's.status_name', 'm.display_order']);

        if ($statusRows->isEmpty()) {
            $statusRows = DB::table('mst_issue_status as s')
                ->where(function ($query) {
                    $query->where('s.is_active', 1)->orWhereNull('s.is_active');
                })
                ->orderBy('s.display_order')
                ->orderBy('s.status_name')
                ->get(['s.status_id', 's.status_name', 's.display_order']);
        }

        $statusOptions = $statusRows->map(fn ($row) => [
            'status_id' => (int) $row->status_id,
            'status_name' => (string) $row->status_name,
        ])->values()->all();
        $allowedStatusIds = collect($statusOptions)->pluck('status_id')->map(fn ($id) => (int) $id)->filter()->unique()->values()->all();

        $statusNameLookup = function ($statusName) {
            return strtolower(trim((string) $statusName));
        };

        // Always get resolved/closed status IDs from ALL statuses (not just role-allowed)
        // This ensures filtering works regardless of role status restrictions
        $allStatusRows = DB::table('mst_issue_status as s')
            ->where(function ($query) {
                $query->where('s.is_active', 1)->orWhereNull('s.is_active');
            })
            ->get(['s.status_id', 's.status_name']);
        
        $allStatusOptions = $allStatusRows->map(fn ($row) => [
            'status_id' => (int) $row->status_id,
            'status_name' => (string) $row->status_name,
        ])->values()->all();

        $resolvedStatusIds = collect($allStatusOptions)
            ->filter(function ($row) use ($statusNameLookup) {
                $name = $statusNameLookup($row['status_name'] ?? '');
                return str_contains($name, 'resolved')
                    || str_contains($name, 'completed');
            })
            ->pluck('status_id')
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $closedStatusIds = collect($allStatusOptions)
            ->filter(function ($row) use ($statusNameLookup) {
                $name = $statusNameLookup($row['status_name'] ?? '');
                return str_contains($name, 'close')
                    || str_contains($name, 'closed')
                    || str_contains($name, 'cancelled')
                    || str_contains($name, 'canceled');
            })
            ->pluck('status_id')
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $inProcessStatusIds = collect($statusOptions)
            ->filter(function ($row) use ($statusNameLookup) {
                $name = $statusNameLookup($row['status_name'] ?? '');
                return str_contains($name, 'process')
                    || str_contains($name, 'progress')
                    || str_contains($name, 'in progress')
                    || str_contains($name, 'in-process')
                    || str_contains($name, 'open')
                    || str_contains($name, 'active');
            })
            ->pluck('status_id')
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $onHoldStatusIds = collect($statusOptions)
            ->filter(function ($row) use ($statusNameLookup) {
                $name = $statusNameLookup($row['status_name'] ?? '');
                return str_contains($name, 'hold')
                    || str_contains($name, 'pending');
            })
            ->pluck('status_id')
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $stateQuery = DB::table('mst_state as st')->select('st.state_id', 'st.state_name');
        if (Schema::hasColumn('mst_state', 'is_active')) {
            $stateQuery->where('st.is_active', 1);
        }

        if (($isStateAdmin || $isStateIt) && ! empty($user->state_id)) {
            $stateIds = array_filter(array_map('trim', explode(',', (string) $user->state_id)), fn ($id) => $id !== '');
            if (! empty($stateIds)) {
                $stateOptions = $stateQuery->whereIn('st.state_id', $stateIds)->orderBy('st.state_name')->get();
            } else {
                $stateOptions = collect();
            }
        } elseif ($isVendorRole) {
            if (! empty($vendorId)) {
                $stateOptions = DB::table('map_vendor_state as m')
                    ->join('mst_state as st', 'm.state_id', '=', 'st.state_id')
                    ->select('st.state_id', 'st.state_name')
                    ->where('m.vendor_id', $vendorId)
                    ->where('m.is_active', 1)
                    ->when(Schema::hasColumn('mst_state', 'is_active'), fn ($query) => $query->where('st.is_active', 1))
                    ->distinct()
                    ->orderBy('st.state_name')
                    ->get();
            } else {
                $stateOptions = collect();
            }
        } else {
            $stateOptions = $stateQuery->orderBy('st.state_name')->get();
        }

        $projectQuery = DB::table('mst_project as pr')->select('pr.project_id', 'pr.project_name');
        if (Schema::hasColumn('mst_project', 'is_active')) {
            $projectQuery->where('pr.is_active', 1);
        }

        if ($isVendorRole) {
            if (! empty($vendorId)) {
                $projectQuery->join('map_vendor_state as m', 'pr.project_id', '=', 'm.project_id')
                    ->where('m.vendor_id', $vendorId)
                    ->where('m.is_active', 1)
                    ->distinct();

                if ($request->filled('state_id')) {
                    $projectQuery->where('m.state_id', $request->input('state_id'));
                }
            } else {
                $projectQuery->whereRaw('0 = 1');
            }
        } elseif ($request->filled('state_id')) {
            $projectQuery->join('map_project_state as m', 'pr.project_id', '=', 'm.project_id')
                ->where('m.state_id', $request->input('state_id'))
                ->where('m.is_active', 1)
                ->distinct();
        } elseif ($isStateRole && ! empty($stateIds)) {
            $projectQuery->join('map_project_state as m', 'pr.project_id', '=', 'm.project_id')
                ->whereIn('m.state_id', $stateIds)
                ->where('m.is_active', 1)
                ->distinct();
        }

        $projectOptions = $projectQuery->orderBy('pr.project_name')->get();

        $applicationQuery = DB::table('mst_application as a')->select('a.application_id', 'a.application_name');
        if (Schema::hasColumn('mst_application', 'is_active')) {
            $applicationQuery->where('a.is_active', 1);
        }

        if ($request->filled('project_id')) {
            if ($isVendorRole) {
                if (! empty($vendorId)) {
                    $applicationIds = DB::table('map_vendor_state as m')
                        ->where('m.vendor_id', $vendorId)
                        ->where('m.project_id', $request->input('project_id'))
                        ->where('m.is_active', 1)
                        ->when($request->filled('state_id'), function ($query) use ($request) {
                            return $query->where('m.state_id', $request->input('state_id'));
                        })
                        ->distinct()
                        ->pluck('m.application_id')
                        ->filter()
                        ->all();
                } else {
                    $applicationIds = [];
                }
            } else {
                $applicationIds = DB::table('map_project_application_module as m')
                    ->where('m.project_id', $request->input('project_id'))
                    ->where('m.is_active', 1)
                    ->distinct()
                    ->pluck('m.application_id')
                    ->filter()
                    ->all();
            }

            if (! empty($applicationIds)) {
                $applicationQuery->whereIn('a.application_id', $applicationIds);
            } else {
                $applicationQuery->whereRaw('0 = 1');
            }
        } else {
            // Do not populate application options until a project is selected.
            $applicationQuery->whereRaw('0 = 1');
        }

        $applicationOptions = $applicationQuery->orderBy('a.application_name')->get();

        $priorityQuery = DB::table('mst_priority as p')->select('p.priority_id', 'p.priority_name');
        if (Schema::hasColumn('mst_priority', 'display_order')) {
            $priorityQuery->addSelect('p.display_order');
        }
        if (Schema::hasColumn('mst_priority', 'is_active')) {
            $priorityQuery->where('p.is_active', 1);
        }
        $priorityOptions = $priorityQuery->orderBy('p.priority_name')->get();

        $vendorQuery = DB::table('mst_vendor as v')->select('v.vendor_id', 'v.vendor_name');
        if (Schema::hasColumn('mst_vendor', 'is_active')) {
            $vendorQuery->where('v.is_active', 1);
        }
        $vendorOptions = $vendorQuery->orderBy('v.vendor_name')->get();

        $vendorStateMappings = DB::table('map_vendor_state as m')
            ->select('m.state_id', 'm.project_id', 'm.vendor_id')
            ->where('m.is_active', 1)
            ->distinct()
            ->get();

        $issuesQuery = DB::table('txn_issue as i')
            ->leftJoin('mst_issue_status as s', 'i.status_id', '=', 's.status_id')
            ->leftJoin('mst_priority as p', 'i.priority_id', '=', 'p.priority_id')
            ->leftJoin('mst_state as st', 'i.state_id', '=', 'st.state_id')
            ->leftJoin('mst_project as pr', 'i.project_id', '=', 'pr.project_id')
            ->leftJoin('mst_application as a', 'i.application_id', '=', 'a.application_id')
            ->leftJoin('mst_module as m', 'i.module_id', '=', 'm.module_id')
            ->select(
                'i.issue_id',
                'i.issue_number',
                'i.issue_title',
                'i.issue_description',
                'i.state_id',
                'st.state_name',
                'i.project_id',
                'pr.project_name',
                'i.application_id',
                'a.application_name',
                'i.module_id',
                'm.module_name',
                'i.status_id',
                's.status_name',
                'i.priority_id',
                'p.priority_name',
                'p.display_order as priority_display_order',
                'i.first_level_vendor_ids',
                'i.second_level_vendor_ids',
                'i.created_at',
                'i.raised_at',
                'i.updated_at'
            )
            ->orderByDesc('i.raised_at');

        if (! empty($allowedStatusIds) && ! $isHoRole && ! $isStateRole && ! $isVendorRole) {
            $issuesQuery->whereIn('i.status_id', $allowedStatusIds);
        }

        if ($request->filled('state_id')) {
            $issuesQuery->where('i.state_id', $request->input('state_id'));
        }
        if ($request->filled('project_id')) {
            $issuesQuery->where('i.project_id', $request->input('project_id'));
        }
        if ($request->filled('application_id')) {
            $issuesQuery->where('i.application_id', $request->input('application_id'));
        }
        if ($request->filled('priority_id')) {
            $issuesQuery->where('i.priority_id', $request->input('priority_id'));
        }
        if ($request->filled('date_from')) {
            $issuesQuery->whereDate('i.raised_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $issuesQuery->whereDate('i.raised_at', '<=', $request->input('date_to'));
        }
        if ($request->filled('ticket_id')) {
            $ticketId = trim((string) $request->input('ticket_id'));
            $issuesQuery->where(function ($query) use ($ticketId) {
                $query->where('i.issue_number', 'like', '%' . $ticketId . '%')
                    ->orWhere('i.issue_id', 'like', '%' . $ticketId . '%');
            });
        }
        if ($request->filled('search')) {
            $searchText = trim((string) $request->input('search'));
            $issuesQuery->where(function ($query) use ($searchText) {
                $query->where('i.issue_title', 'like', '%' . $searchText . '%')
                    ->orWhere('i.issue_number', 'like', '%' . $searchText . '%')
                    ->orWhere('i.issue_description', 'like', '%' . $searchText . '%');
            });
        }

        if ($isHoRole) {
            // Show all issues for HO roles.
        } elseif ($isVendorRole) {
            $vendorId = Schema::hasColumn('mst_user', 'vendor_id') ? ($user->vendor_id ?? null) : null;

            if (! empty($vendorId)) {
                $issuesQuery->where(function ($query) use ($vendorId) {
                    $query->whereRaw('FIND_IN_SET(?, COALESCE(i.first_level_vendor_ids, "")) > 0', [$vendorId])
                        ->orWhereRaw('FIND_IN_SET(?, COALESCE(i.second_level_vendor_ids, "")) > 0', [$vendorId])
                        ->orWhereExists(function ($q) use ($vendorId) {
                            $q->from('map_issue_vendor_assignment as mva')
                              ->whereColumn('mva.issue_id', 'i.issue_id')
                              ->where('mva.vendor_id', $vendorId)
                              ->where('mva.is_active', 1);
                        });
                });
            } else {
                $issuesQuery->whereRaw('0 = 1');
            }
        } elseif ($isStateAdmin && ! empty($user->state_id)) {
            $stateIds = array_filter(array_map('trim', explode(',', (string) $user->state_id)), fn ($id) => $id !== '');
            if (! empty($stateIds)) {
                $issuesQuery->whereIn('i.state_id', $stateIds);
            }
        } elseif ($isStateIt && ! empty($user->state_id)) {
            $stateIds = array_filter(array_map('trim', explode(',', (string) $user->state_id)), fn ($id) => $id !== '');
            if (! empty($stateIds)) {
                $issuesQuery->where(function ($query) use ($stateIds, $user) {
                    $query->whereIn('i.state_id', $stateIds)
                          ->where('i.raised_by_user_id', $user->user_id);
                });
            }
        }

        $summaryIssues = $issuesQuery->get();

        if ($isHoRole) {
            Log::info('HO role issue dashboard query', [
                'user_id' => $user?->user_id,
                'role_names' => $roleNames,
                'sql' => $issuesQuery->toSql(),
                'bindings' => $issuesQuery->getBindings(),
            ]);
        }

        $statusSummary = [
            'all' => (clone $issuesQuery)->count(),
            'in_process' => (clone $issuesQuery)
                ->whereNotIn('i.status_id', $resolvedStatusIds ?: [0])
                ->whereNotIn('i.status_id', $closedStatusIds ?: [0])
                ->count(),
            'resolved' => (clone $issuesQuery)
                ->whereIn('i.status_id', $resolvedStatusIds ?: [0])
                ->count(),
            'closed' => (clone $issuesQuery)
                ->whereIn('i.status_id', $closedStatusIds ?: [0])
                ->count(),
        ];

        if ($isStateIt) {
            $stateIds = array_filter(array_map('trim', explode(',', (string) ($user->state_id ?? ''))), fn ($id) => $id !== '');
            
            $statusSummary['resolved'] = DB::table('txn_issue as i')
                ->whereIn('i.state_id', $stateIds ?: [0])
                ->where('i.raised_by_user_id', $user->user_id)
                ->whereIn('i.status_id', $resolvedStatusIds ?: [0])
                ->count();
            
            $statusSummary['closed'] = DB::table('txn_issue as i')
                ->whereIn('i.issue_id', function ($query) use ($user, $closedStatusIds) {
                    $query->select('issue_id')
                        ->from('txn_issue_status_history')
                        ->where('changed_by_user_id', $user->user_id)
                        ->whereIn('new_status_id', $closedStatusIds ?: [0]);
                })
                ->where('i.raised_by_user_id', $user->user_id)
                ->whereIn('i.state_id', $stateIds ?: [0])
                ->count();
        }

        if ($isHoIt) {
            $statusSummary['resolved'] = DB::table('txn_issue as i')
                ->whereIn('i.issue_id', function ($query) use ($user, $resolvedStatusIds) {
                    $query->select('issue_id')
                        ->from('txn_issue_status_history')
                        ->where('changed_by_user_id', $user->user_id)
                        ->whereIn('new_status_id', $resolvedStatusIds ?: [0]);
                })
                ->count();
            
            $statusSummary['closed'] = DB::table('txn_issue as i')
                ->whereIn('i.issue_id', function ($query) use ($user, $closedStatusIds) {
                    $query->select('issue_id')
                        ->from('txn_issue_status_history')
                        ->where('changed_by_user_id', $user->user_id)
                        ->whereIn('new_status_id', $closedStatusIds ?: [0]);
                })
                ->count();
        }

        if ($isVendorIt) {
            $vendorId = Schema::hasColumn('mst_user', 'vendor_id') ? $user->vendor_id : null;
            
            if (! empty($vendorId)) {
                $statusSummary['resolved'] = DB::table('txn_issue as i')
                    ->whereIn('i.issue_id', function ($query) use ($user, $resolvedStatusIds) {
                        $query->select('issue_id')
                            ->from('txn_issue_status_history')
                            ->where('changed_by_user_id', $user->user_id)
                            ->whereIn('new_status_id', $resolvedStatusIds ?: [0]);
                    })
                    ->where(function ($query) use ($vendorId) {
                        $query->whereRaw('FIND_IN_SET(?, COALESCE(i.first_level_vendor_ids, "")) > 0', [$vendorId])
                            ->orWhereRaw('FIND_IN_SET(?, COALESCE(i.second_level_vendor_ids, "")) > 0', [$vendorId])
                            ->orWhereExists(function ($q) use ($vendorId) {
                                $q->from('map_issue_vendor_assignment as mva')
                                  ->whereColumn('mva.issue_id', 'i.issue_id')
                                  ->where('mva.vendor_id', $vendorId)
                                  ->where('mva.is_active', 1);
                            });
                    })
                    ->count();
                
                $statusSummary['closed'] = DB::table('txn_issue as i')
                    ->whereIn('i.issue_id', function ($query) use ($user, $closedStatusIds) {
                        $query->select('issue_id')
                            ->from('txn_issue_status_history')
                            ->where('changed_by_user_id', $user->user_id)
                            ->whereIn('new_status_id', $closedStatusIds ?: [0]);
                    })
                    ->where(function ($query) use ($vendorId) {
                        $query->whereRaw('FIND_IN_SET(?, COALESCE(i.first_level_vendor_ids, "")) > 0', [$vendorId])
                            ->orWhereRaw('FIND_IN_SET(?, COALESCE(i.second_level_vendor_ids, "")) > 0', [$vendorId])
                            ->orWhereExists(function ($q) use ($vendorId) {
                                $q->from('map_issue_vendor_assignment as mva')
                                  ->whereColumn('mva.issue_id', 'i.issue_id')
                                  ->where('mva.vendor_id', $vendorId)
                                  ->where('mva.is_active', 1);
                            });
                    })
                    ->count();
            }
        }

        $requestedStatusValue = trim((string) $request->input('status_id', ''));
        if ($request->has('status_id') && $requestedStatusValue !== '') {
            $requestedStatusValue = strtolower($requestedStatusValue);

            if ($requestedStatusValue === 'total' || $requestedStatusValue === 'all') {
                // Keep all applicable issues for the selected role.
            } elseif ($requestedStatusValue === 'in_process' || $requestedStatusValue === 'in_progress') {
                // Exclude resolved and closed issues from in-progress view
                if (!empty($resolvedStatusIds)) {
                    $issuesQuery->whereNotIn('i.status_id', $resolvedStatusIds);
                }
                if (!empty($closedStatusIds)) {
                    $issuesQuery->whereNotIn('i.status_id', $closedStatusIds);
                }
            } elseif ($requestedStatusValue === 'resolved') {
                if ($isStateIt) {
                    $issuesQuery->where('i.raised_by_user_id', $user->user_id)
                        ->whereIn('i.status_id', $resolvedStatusIds ?: [0]);
                } elseif ($isHoIt) {
                    $issuesQuery->whereIn('i.issue_id', function ($query) use ($user, $resolvedStatusIds) {
                        $query->select('issue_id')
                            ->from('txn_issue_status_history')
                            ->where('changed_by_user_id', $user->user_id)
                            ->whereIn('new_status_id', $resolvedStatusIds ?: [0]);
                    });
                } elseif ($isVendorIt) {
                    $vendorId = Schema::hasColumn('mst_user', 'vendor_id') ? $user->vendor_id : null;
                    if (! empty($vendorId)) {
                        $issuesQuery->whereIn('i.issue_id', function ($query) use ($user, $resolvedStatusIds) {
                            $query->select('issue_id')
                                ->from('txn_issue_status_history')
                                ->where('changed_by_user_id', $user->user_id)
                                ->whereIn('new_status_id', $resolvedStatusIds ?: [0]);
                        })->where(function ($query) use ($vendorId) {
                            $query->whereRaw('FIND_IN_SET(?, COALESCE(i.first_level_vendor_ids, "")) > 0', [$vendorId])
                                ->orWhereRaw('FIND_IN_SET(?, COALESCE(i.second_level_vendor_ids, "")) > 0', [$vendorId])
                                ->orWhereExists(function ($q) use ($vendorId) {
                                    $q->from('map_issue_vendor_assignment as mva')
                                      ->whereColumn('mva.issue_id', 'i.issue_id')
                                      ->where('mva.vendor_id', $vendorId)
                                      ->where('mva.is_active', 1);
                                });
                        });
                    }
                } else {
                    $issuesQuery->whereIn('i.status_id', $resolvedStatusIds ?: [0]);
                }
            } elseif ($requestedStatusValue === 'closed') {
                if ($isStateIt) {
                    $issuesQuery->whereIn('i.issue_id', function ($query) use ($user, $closedStatusIds) {
                        $query->select('issue_id')
                            ->from('txn_issue_status_history')
                            ->where('changed_by_user_id', $user->user_id)
                            ->whereIn('new_status_id', $closedStatusIds ?: [0]);
                    })->where('i.raised_by_user_id', $user->user_id);
                } elseif ($isHoIt) {
                    $issuesQuery->whereIn('i.issue_id', function ($query) use ($user, $closedStatusIds) {
                        $query->select('issue_id')
                            ->from('txn_issue_status_history')
                            ->where('changed_by_user_id', $user->user_id)
                            ->whereIn('new_status_id', $closedStatusIds ?: [0]);
                    });
                } elseif ($isVendorIt) {
                    $vendorId = Schema::hasColumn('mst_user', 'vendor_id') ? $user->vendor_id : null;
                    if (! empty($vendorId)) {
                        $issuesQuery->whereIn('i.issue_id', function ($query) use ($user, $closedStatusIds) {
                            $query->select('issue_id')
                                ->from('txn_issue_status_history')
                                ->where('changed_by_user_id', $user->user_id)
                                ->whereIn('new_status_id', $closedStatusIds ?: [0]);
                        })->where(function ($query) use ($vendorId) {
                            $query->whereRaw('FIND_IN_SET(?, COALESCE(i.first_level_vendor_ids, "")) > 0', [$vendorId])
                                ->orWhereRaw('FIND_IN_SET(?, COALESCE(i.second_level_vendor_ids, "")) > 0', [$vendorId])
                                ->orWhereExists(function ($q) use ($vendorId) {
                                    $q->from('map_issue_vendor_assignment as mva')
                                      ->whereColumn('mva.issue_id', 'i.issue_id')
                                      ->where('mva.vendor_id', $vendorId)
                                      ->where('mva.is_active', 1);
                                });
                        });
                    }
                } else {
                    $issuesQuery->whereIn('i.status_id', $closedStatusIds ?: [0]);
                }
            } elseif (is_numeric($requestedStatusValue)) {
                $requestedStatusId = (int) $requestedStatusValue;
                if (in_array($requestedStatusId, $inProcessStatusIds, true)) {
                    $issuesQuery->whereNotIn('i.status_id', $resolvedStatusIds ?: [0])
                        ->whereNotIn('i.status_id', $closedStatusIds ?: [0]);
                } elseif (in_array($requestedStatusId, $resolvedStatusIds, true)) {
                    $issuesQuery->whereIn('i.status_id', $resolvedStatusIds ?: [0]);
                } elseif (in_array($requestedStatusId, $closedStatusIds, true)) {
                    $issuesQuery->whereIn('i.status_id', $closedStatusIds ?: [0]);
                } else {
                    $issuesQuery->where('i.status_id', $requestedStatusId);
                }
            } else {
                $issuesQuery->where('i.status_id', (int) $requestedStatusValue);
            }
        } elseif (empty($requestedStatusValue) && ! $request->has('status_id')) {
            // Default view: show only in-progress (non-resolved, non-closed) issues
            if (!empty($resolvedStatusIds)) {
                $issuesQuery->whereNotIn('i.status_id', $resolvedStatusIds);
            }
            if (!empty($closedStatusIds)) {
                $issuesQuery->whereNotIn('i.status_id', $closedStatusIds);
            }
        }

        $issues = $issuesQuery->get();

        $priorityOrder = [
            'critical' => 1,
            'high' => 2,
            'medium' => 3,
            'low' => 4,
        ];

        $issueRows = $issues->map(function ($issue) use ($priorityOrder) {
            $fallbackTitle = 'Issue #' . ($issue->issue_number ?: $issue->issue_id);
            $fallbackDescription = 'Issue details are available in the system.';
            $dateValue = $issue->updated_at ?: $issue->raised_at;
            $formattedDate = $dateValue ? Carbon::parse($dateValue)->format('Y-m-d H:i:s') : '—';
            $priorityName = strtolower((string) ($issue->priority_name ?: 'medium'));
            $priorityWeight = $priorityOrder[$priorityName] ?? 99;

            $history = [];

            $initialVendorIds = [];
            foreach (['first_level_vendor_ids', 'second_level_vendor_ids'] as $vendorIdColumn) {
                if (! Schema::hasColumn('txn_issue', $vendorIdColumn)) {
                    continue;
                }

                $rawValue = trim((string) ($issue->{$vendorIdColumn} ?? ''));
                if ($rawValue === '') {
                    continue;
                }

                $parts = array_map('trim', explode(',', $rawValue));
                foreach ($parts as $part) {
                    $numeric = (int) $part;
                    if ($numeric > 0) {
                        $initialVendorIds[] = $numeric;
                    }
                }
            }

            $initialVendorIds = array_values(array_unique(array_filter($initialVendorIds, fn ($id) => $id > 0)));
            if (! empty($initialVendorIds)) {
                $vendorNames = DB::table('mst_vendor')
                    ->whereIn('vendor_id', $initialVendorIds)
                    ->where(function ($q) {
                        $q->where('is_active', 1)->orWhereNull('is_active');
                    })
                    ->pluck('vendor_name')
                    ->map(fn ($name) => (string) $name)
                    ->all();

                if (! empty($vendorNames)) {
                    $history[] = [
                        'changed_at' => $issue->created_at ? Carbon::parse($issue->created_at)->format('d M y h:i A') : $formattedDate,
                        'changed_by' => 'System',
                        'status_name' => 'Vendor Assignment',
                        'comment' => 'Initial vendor assignment: ' . implode(', ', $vendorNames),
                    ];
                }
            }

            if (Schema::hasTable('map_issue_vendor_assignment') && Schema::hasTable('mst_vendor')) {
                $vendorAssignmentRows = DB::table('map_issue_vendor_assignment as m')
                    ->leftJoin('mst_vendor as v', 'm.vendor_id', '=', 'v.vendor_id')
                    ->where('m.issue_id', $issue->issue_id)
                    ->orderBy('m.created_at')
                    ->get([
                        'm.created_at as created_at',
                        'm.updated_at as updated_at',
                        'm.is_active as is_active',
                        'v.vendor_name as vendor_name',
                    ]);

                foreach ($vendorAssignmentRows as $row) {
                    $vendorName = trim((string) ($row->vendor_name ?? ''));
                    if ($vendorName === '') {
                        continue;
                    }

                    $history[] = [
                        'changed_at' => $row->created_at ? Carbon::parse($row->created_at)->format('d M y h:i A') : '—',
                        'changed_by' => 'System',
                        'status_name' => 'Vendor Assignment',
                        'comment' => 'Vendor assignment: ' . $vendorName . ($row->is_active ? '' : ' (inactive)'),
                    ];
                }
            }

            if (Schema::hasTable('txn_issue_status_history')) {
                $historyRows = DB::table('txn_issue_status_history as h')
                    ->leftJoin('mst_issue_status as s', 'h.new_status_id', '=', 's.status_id')
                    ->leftJoin('mst_user as u', 'h.changed_by_user_id', '=', 'u.user_id')
                    ->where('h.issue_id', $issue->issue_id)
                    ->orderByDesc('h.changed_at')
                    ->get([
                        'h.changed_at as changed_at',
                        'h.comment as comment',
                        's.status_name as status_name',
                        'u.user_name as changed_by',
                    ]);

                foreach ($historyRows as $row) {
                    $history[] = [
                        'changed_at' => $row->changed_at ? Carbon::parse($row->changed_at)->format('d M y h:i A') : '—',
                        'changed_by' => $row->changed_by ?: 'Update user',
                        'status_name' => $row->status_name ?: 'Open',
                        'comment' => $row->comment ?: 'Status updated',
                    ];
                }
            }

            usort($history, function ($a, $b) {
                $aTime = $a['changed_at'] === '—' ? 0 : Carbon::parse($a['changed_at'])->timestamp;
                $bTime = $b['changed_at'] === '—' ? 0 : Carbon::parse($b['changed_at'])->timestamp;
                return $bTime <=> $aTime;
            });

            if (empty($history)) {
                $history[] = [
                    'changed_at' => $formattedDate,
                    'changed_by' => 'Update user',
                    'status_name' => $issue->status_name ?: 'Open',
                    'comment' => 'Status updated to ' . ($issue->status_name ?: 'Open'),
                ];
            }

            $attachments = [];
            if (Schema::hasTable('txn_issue_attachment')) {
                $attachmentRows = DB::table('txn_issue_attachment as a')
                    ->where('a.issue_id', $issue->issue_id)
                    ->orderByDesc('a.uploaded_at')
                    ->get([
                        'a.attachment_id',
                        'a.original_file_name',
                        'a.stored_file_name',
                        'a.file_path',
                        'a.uploaded_at',
                    ]);

                $attachments = $attachmentRows->map(function ($row) {
                    return [
                        'attachment_id' => $row->attachment_id,
                        'file_name' => $row->original_file_name ?: ($row->stored_file_name ?: 'Attachment'),
                        'path' => $row->file_path ?: '',
                        'view_url' => route('attachment.preview', ['id' => $row->attachment_id]),
                        'download_url' => route('attachment.download', ['id' => $row->attachment_id]),
                        'created_at' => $row->uploaded_at ? Carbon::parse($row->uploaded_at)->format('d M y h:i A') : '—',
                    ];
                })->values()->all();
            }

            return [
                'id' => $issue->issue_number ?: 'IT-' . $issue->issue_id,
                'issue_id' => (int) $issue->issue_id,
                'title' => $issue->issue_title ?: $fallbackTitle,
                'state' => $issue->state_name ?: '—',
                'state_id' => (int) ($issue->state_id ?? 0),
                'project' => $issue->project_name ?: '—',
                'project_id' => (int) ($issue->project_id ?? 0),
                'application' => $issue->application_name ?: '—',
                'module' => $issue->module_name ?: '—',
                'status' => $issue->status_name ?: 'Open',
                'status_id' => (int) ($issue->status_id ?? 0),
                'priority' => $issue->priority_name ?: 'Medium',
                'priority_weight' => $priorityWeight,
                'updated_on' => $formattedDate,
                'description' => $issue->issue_description ?: $fallbackDescription,
                'history' => $history,
                'attachments' => $attachments,
            ];
        })->values();

        $issueRows = $issueRows->sortBy(function ($row) {
            return $row['priority_weight'];
        })->values();

        return view('pages.role-issue-dashboard', [
            'issues' => $issueRows,
            'statusSummary' => $statusSummary,
            'statusOptions' => $statusOptions,
            'stateOptions' => $stateOptions,
            'projectOptions' => $projectOptions,
            'applicationOptions' => $applicationOptions,
            'priorityOptions' => $priorityOptions,
            'vendorOptions' => $vendorOptions,
            'vendorStateMappings' => $vendorStateMappings,
            'filterValues' => [
                'state_id' => $request->input('state_id'),
                'project_id' => $request->input('project_id'),
                'application_id' => $request->input('application_id'),
                'priority_id' => $request->input('priority_id'),
                'status_id' => $request->input('status_id'),
                'date_from' => $request->input('date_from'),
                'date_to' => $request->input('date_to'),
                'ticket_id' => $request->input('ticket_id'),
                'search' => $request->input('search'),
            ],
        ]);
    }

    public function updateIssueStatus(Request $request)
    {
        try {
            $request->validate([
                'issue_id' => ['required', 'integer', 'exists:txn_issue,issue_id'],
                'status_name' => ['required', 'string'],
            ]);

            $issueId = (int) $request->input('issue_id');
            $statusName = trim((string) $request->input('status_name'));

            $statusRow = DB::table('mst_issue_status')
                ->where('status_name', $statusName)
                ->first();

            if (! $statusRow) {
                $statusRow = DB::table('mst_issue_status')
                    ->where('status_id', (int) $request->input('status_id', 0))
                    ->first();
            }

            if (! $statusRow) {
                return redirect()->back()->with('error', 'Unable to resolve the selected status.');
            }

            $newStatusId = (int) ($statusRow->status_id ?? 0);
            $vendorIds = [];

            if ($statusName === 'Vendor Assignment' || str_contains(strtolower($statusName), 'vendor')) {
                $vendorIds = $request->input('vendor_ids', []);
                $vendorIds = is_array($vendorIds) ? array_values(array_filter(array_map('intval', $vendorIds))) : [];
            }

            // Fetch the current (old) status before updating
            $currentIssue = DB::table('txn_issue')
                ->where('issue_id', $issueId)
                ->first();
            $oldStatusId = (int) ($currentIssue->status_id ?? 0);

            DB::transaction(function () use ($issueId, $newStatusId, $oldStatusId, $statusName, $request, $vendorIds) {
                DB::table('txn_issue')
                    ->where('issue_id', $issueId)
                    ->update([
                        'status_id' => $newStatusId,
                        'updated_at' => now(),
                    ]);

                $historyComment = trim((string) $request->input('remarks', 'Status updated')) ?: 'Status updated';

                if (! empty($vendorIds)) {
                    DB::table('map_issue_vendor_assignment')
                        ->where('issue_id', $issueId)
                        ->update(['is_active' => 0]);

                    foreach ($vendorIds as $vendorId) {
                        $exists = DB::table('map_issue_vendor_assignment')
                            ->where('issue_id', $issueId)
                            ->where('vendor_id', $vendorId)
                            ->exists();

                        if (! $exists) {
                            DB::table('map_issue_vendor_assignment')->insert([
                                'issue_id' => $issueId,
                                'vendor_id' => $vendorId,
                                'is_active' => 1,
                                'created_by' => auth()->id(),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        } else {
                            DB::table('map_issue_vendor_assignment')
                                ->where('issue_id', $issueId)
                                ->where('vendor_id', $vendorId)
                                ->update([
                                    'is_active' => 1,
                                    'updated_at' => now(),
                                ]);
                        }
                    }

                    $vendorNames = DB::table('mst_vendor')
                        ->whereIn('vendor_id', $vendorIds)
                        ->pluck('vendor_name')
                        ->map(fn ($name) => (string) $name)
                        ->all();

                    $historyComment = 'Vendor assignment: ' . implode(', ', $vendorNames);
                }

                DB::table('txn_issue_status_history')->insert([
                    'issue_id' => $issueId,
                    'old_status_id' => $oldStatusId,
                    'new_status_id' => $newStatusId,
                    'changed_by_user_id' => auth()->id(),
                    'comment' => $historyComment,
                    'changed_at' => now(),
                ]);

                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        if (! $file || ! $file->isValid()) {
                            continue;
                        }

                        $originalName = $file->getClientOriginalName();
                        $storedName = $file->hashName();
                        $folder = 'issue_attachments/' . $issueId;
                        $relativePath = $file->storeAs($folder, $storedName, 'public');

                        DB::table('txn_issue_attachment')->insert([
                            'issue_id' => $issueId,
                            'original_file_name' => $originalName,
                            'stored_file_name' => $storedName,
                            'file_path' => $relativePath,
                            'uploaded_at' => now(),
                        ]);
                    }
                }
            });

            if ($request->expectsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Ticket status updated successfully.'
                ]);
            }

            return redirect()->back()->with('success', 'Ticket status updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Issue status update failed', [
                'issue_id' => $request->input('issue_id'),
                'user_id' => auth()->id(),
                'message' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to update the ticket. Please try again.'
                ], 422);
            }

            return redirect()->back()->withInput()->with('error', 'Unable to update the ticket. Please try again.');
        }
    }

    public function issues(): View
    {
        return view('pages.issues');
    }

    public function raiseIssue(): View
    {
        return view('pages.raise-issue');
    }

    public function reports(): View
    {
        return view('pages.reports');
    }

    public function administration(): View
    {
        return view('pages.administration');
    }

    public function mailConfiguration(): View
    {
        $user = auth()->user();
        $statesQuery = DB::table('mst_state')
            ->select('state_id', 'state_name')
            ->where('is_active', 1)
            ->orderBy('state_name');

        $configurationQuery = MailConfiguration::query()->orderByDesc('mail_configuration_id');

        if ($user?->hasRole('Vendor Admin') && ! empty($user->vendor_id)) {
            $states = DB::table('map_vendor_state as m')
                ->join('mst_state as s', 'm.state_id', '=', 's.state_id')
                ->where('m.vendor_id', $user->vendor_id)
                ->where('s.is_active', 1)
                ->distinct()
                ->orderBy('s.state_name')
                ->get(['s.state_id', 's.state_name']);

            $stateIds = $states->pluck('state_id')->all();
            if (! empty($stateIds)) {
                $configurationQuery->whereIn('state_id', $stateIds);
            }
        } elseif ($user?->hasRole('State Admin') && ! empty($user->state_id)) {
            $stateIds = explode(',', (string) $user->state_id);
            $stateIds = array_filter(array_map('trim', $stateIds), fn ($id) => $id !== '');
            $states = $statesQuery->whereIn('state_id', $stateIds)->get();
            $configurationQuery->whereIn('state_id', $stateIds);
        } else {
            $states = $statesQuery->get();
        }

        $routeName = 'mail.configuration';
        $permissions = [
            'view' => $user?->hasPrivilegeOnRoute($routeName, 'view'),
            'create' => $user?->hasPrivilegeOnRoute($routeName, 'create'),
            'edit' => $user?->hasPrivilegeOnRoute($routeName, 'edit'),
            'delete' => $user?->hasPrivilegeOnRoute($routeName, 'delete'),
            'export' => $user?->hasPrivilegeOnRoute($routeName, 'export'),
            'activate' => $user?->hasPrivilegeOnRoute($routeName, 'activate'),
            'deactivate' => $user?->hasPrivilegeOnRoute($routeName, 'deactivate'),
        ];

        $mailConfigurations = $configurationQuery->get();

        return view('pages.mail-configuration', [
            'states' => $states,
            'permissions' => $permissions,
            'mailConfigurations' => $mailConfigurations,
        ]);
    }

    public function genericAdminPage(Request $request): View
    {
        $title = $request->query('title', 'Administration');
        $description = $request->query('description', 'Manage administrative settings.');

        return view('pages.generic-admin-page', compact('title', 'description'));
    }
}

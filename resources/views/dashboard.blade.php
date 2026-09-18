<x-app-layout>
    <main class="central-dashboard h-[calc(100vh-76px)] overflow-hidden bg-slate-100 p-0">
                    <div class="flex h-full min-h-0 flex-col overflow-hidden border border-slate-200 bg-white shadow-sm">
                        <div class="flex min-h-0 flex-1 flex-col overflow-hidden">
                            <div class="border-b border-slate-200 px-5 py-4">
                                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                                    <div>
                                        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">Dashboard</p>
                                        <p class="mt-1 text-base text-slate-700">Consolidated view of issues across all states and services.</p>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-3 text-sm text-slate-500">
                                        <span>Data Period: 01 Jul 2026 - 31 Jul 2026</span>
                                        <span class="h-4 w-px bg-slate-200"></span>
                                        <span>Updated: <time data-live-updated>Loading...</time></span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex-1 overflow-y-auto px-2 py-2">
                                <div class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                                    @php
                                        $cards = [
                                            ['label' => 'Total Issues', 'key' => 'total_issues', 'value' => '0', 'color' => 'blue', 'trend' => 'Live'],
                                            ['label' => 'Active Services', 'key' => 'active_services', 'value' => '0', 'color' => 'emerald', 'trend' => 'Live'],
                                            ['label' => 'Open Issues', 'key' => 'open_issues', 'value' => '0', 'color' => 'violet', 'trend' => 'Live'],
                                            ['label' => 'Critical Issues', 'key' => 'critical_issues', 'value' => '0', 'color' => 'red', 'trend' => 'Live'],
                                            ['label' => 'Resolved Today', 'key' => 'resolved_today', 'value' => '0', 'color' => 'orange', 'trend' => 'Live'],
                                            ['label' => 'Pending Vendors', 'key' => 'pending_vendors', 'value' => '0', 'color' => 'cyan', 'trend' => 'Live'],
                                        ];
                                    @endphp
                                    @foreach($cards as $card)
                                        <div class="rounded-[14px] border border-slate-200 bg-slate-50 p-4 shadow-sm">
                                            <div class="flex items-center justify-between gap-3">
                                                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-{{ $card['color'] }}-100 text-{{ $card['color'] }}-700">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                                                </div>
                                                <span class="rounded-full bg-white px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-500 shadow-sm">{{ $card['trend'] }}</span>
                                            </div>
                                            <p class="mt-4 text-[10px] uppercase tracking-[0.2em] text-slate-500">{{ $card['label'] }}</p>
                                            <p data-live-card="{{ $card['key'] }}" class="mt-2 text-2xl font-semibold text-slate-900">{{ $card['value'] }}</p>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-3">
                                    <div class="grid gap-3 grid-cols-1 lg:grid-cols-2 xl:grid-cols-3">
                                        <div class="rounded-[14px] border border-slate-200 bg-slate-50 p-4 shadow-sm">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="text-sm font-semibold text-slate-900">Average Resolution Time</p>
                                                    <p data-live-metric="average_resolution_hours" class="mt-3 text-2xl font-semibold text-slate-900">—</p>
                                                </div>
                                                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-white text-slate-900 shadow-sm">
                                                    <span data-live-metric="sla_compliance" class="text-xl font-semibold">—</span>
                                                </div>
                                            </div>
                                            <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-slate-200">
                                                <div data-live-sla-bar class="h-full w-0 rounded-full bg-blue-500"></div>
                                            </div>
                                        </div>
                                        <div class="rounded-[14px] border border-slate-200 bg-slate-50 p-4 shadow-sm">
                                            <p class="text-sm font-semibold text-slate-900">Monthly Issue Trend</p>
                                            <p class="mt-2 text-[11px] uppercase tracking-[0.22em] text-slate-500">Open · Closed · Pending</p>
                                            <div data-live-monthly class="mt-4 h-[180px] rounded-xl border border-slate-200 bg-white p-3">
                                                <div data-live-monthly-chart class="grid h-32 grid-cols-12 items-end gap-1"></div>
                                                <div data-live-month-labels class="mt-3 grid grid-cols-12 gap-1 text-[10px] text-slate-500">
                                                    @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $month)
                                                        <div class="text-center">{{ $month }}</div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3 grid gap-3 grid-cols-1 lg:grid-cols-2 xl:grid-cols-3">
                                    <div class="rounded-[14px] border border-slate-200 bg-slate-50 p-4 shadow-sm">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-semibold text-slate-900">State-wise Analysis</p>
                                            <a href="#" class="text-xs font-semibold text-slate-500">View all</a>
                                        </div>
                                        <div class="mt-4">
                                            <div class="grid w-full min-w-0 grid-cols-[minmax(0,1.3fr)_minmax(0,0.8fr)_minmax(0,0.7fr)_minmax(0,0.7fr)_minmax(0,0.6fr)] gap-2 text-[10px] uppercase tracking-[0.2em] text-slate-400">
                                                <span>State</span>
                                                <span>Issues</span>
                                                <span>Open</span>
                                                <span>Resolved</span>
                                                <span>SLA</span>
                                            </div>
                                            <div data-live-analysis="state" class="mt-2 space-y-3 text-[11px] text-slate-700">
                                                @foreach([] as $row)
                                                    <div class="grid w-full min-w-0 grid-cols-[minmax(0,1.3fr)_minmax(0,0.8fr)_minmax(0,0.7fr)_minmax(0,0.7fr)_minmax(0,0.6fr)] gap-2 text-slate-600">
                                                        <span class="font-semibold text-slate-900">{{ $row[0] }}</span>
                                                        <span>{{ $row[1] }}</span>
                                                        <span>{{ $row[2] }}</span>
                                                        <span>{{ $row[3] }}</span>
                                                        <span>{{ $row[4] }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="rounded-[14px] border border-slate-200 bg-slate-50 p-4 shadow-sm">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-semibold text-slate-900">Service-wise Analysis</p>
                                            <a href="#" class="text-xs font-semibold text-slate-500">View all</a>
                                        </div>
                                        <div class="mt-4">
                                            <div class="grid w-full min-w-0 grid-cols-[minmax(0,1.3fr)_minmax(0,0.8fr)_minmax(0,0.7fr)_minmax(0,0.7fr)_minmax(0,0.6fr)] gap-2 text-[10px] uppercase tracking-[0.2em] text-slate-400">
                                                <span>Service</span>
                                                <span>Issues</span>
                                                <span>Open</span>
                                                <span>Resolved</span>
                                                <span>SLA</span>
                                            </div>
                                            <div data-live-analysis="service" class="mt-2 space-y-3 text-[11px] text-slate-700">
                                                @foreach([] as $row)
                                                    <div class="grid w-full min-w-0 grid-cols-[minmax(0,1.3fr)_minmax(0,0.8fr)_minmax(0,0.7fr)_minmax(0,0.7fr)_minmax(0,0.6fr)] gap-2 text-slate-600">
                                                        <span class="font-semibold text-slate-900">{{ $row[0] }}</span>
                                                        <span>{{ $row[1] }}</span>
                                                        <span>{{ $row[2] }}</span>
                                                        <span>{{ $row[3] }}</span>
                                                        <span>{{ $row[4] }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="rounded-[14px] border border-slate-200 bg-slate-50 p-4 shadow-sm">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-semibold text-slate-900">Vendor Performance</p>
                                            <a href="#" class="text-xs font-semibold text-slate-500">View all</a>
                                        </div>
                                        <div class="mt-4">
                                            <div class="grid w-full min-w-0 grid-cols-[minmax(0,1.3fr)_minmax(0,0.8fr)_minmax(0,0.7fr)_minmax(0,0.7fr)_minmax(0,0.6fr)] gap-2 text-[10px] uppercase tracking-[0.2em] text-slate-400">
                                                <span>Vendor</span>
                                                <span>Issues</span>
                                                <span>Open</span>
                                                <span>Resolved</span>
                                                <span>SLA</span>
                                            </div>
                                            <div data-live-analysis="vendor" class="mt-2 space-y-3 text-[11px] text-slate-700">
                                                @foreach([] as $row)
                                                    <div class="grid w-full min-w-0 grid-cols-[minmax(0,1.3fr)_minmax(0,0.8fr)_minmax(0,0.7fr)_minmax(0,0.7fr)_minmax(0,0.6fr)] gap-2 text-slate-600">
                                                        <span class="font-semibold text-slate-900">{{ $row[0] }}</span>
                                                        <span>{{ $row[1] }}</span>
                                                        <span>{{ $row[2] }}</span>
                                                        <span>{{ $row[3] }}</span>
                                                        <span>{{ $row[4] }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3 flex flex-col gap-3 xl:flex-row xl:items-start">
                                    <div class="flex-1 min-w-0 flex flex-col rounded-[14px] border border-slate-200 bg-slate-50 shadow-sm">
                                        <div class="flex items-center justify-between border-b border-slate-200 px-4 py-4">
                                            <div>
                                                <p class="text-sm font-semibold text-slate-900">Detailed Issue Report</p>
                                                <p class="mt-1 text-xs text-slate-500">Latest ticket status with priority and SLA</p>
                                            </div>
                                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                                <button class="rounded-lg border border-slate-200 bg-white px-3 py-2 hover:bg-slate-100">Export XLS</button>
                                                <button class="rounded-lg border border-slate-200 bg-white px-3 py-2 hover:bg-slate-100">Export PDF</button>
                                            </div>
                                        </div>
                                        <div class="flex flex-col gap-3 border-b border-slate-200 px-4 py-3">
                                            <div class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-600">
                                                <div class="flex items-center gap-2">
                                                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1111.5 4.5a7.5 7.5 0 015.15 12.65z"></path></svg>
                                                    <span>Search in tickets</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="px-4 pb-4">
                                            <div class="overflow-hidden rounded-b-[14px]">
                                                <table class="min-w-full w-full text-left text-[11px] text-slate-700">
                                                    <thead class="sticky top-0 bg-slate-50 text-[10px] uppercase tracking-[0.24em] text-slate-500">
                                                        <tr>
                                                            <th class="w-[8%] px-3 py-2">Ticket</th>
                                                            <th class="w-[13%] px-3 py-2">Incident</th>
                                                            <th class="w-[7%] px-3 py-2">State</th>
                                                            <th class="w-[7%] px-3 py-2">District</th>
                                                            <th class="w-[8%] px-3 py-2">Service</th>
                                                            <th class="w-[8%] px-3 py-2">Vendor</th>
                                                            <th class="w-[7%] px-3 py-2">Priority</th>
                                                            <th class="w-[7%] px-3 py-2">Status</th>
                                                            <th class="w-[10%] px-3 py-2">Assigned</th>
                                                            <th class="w-[9%] px-3 py-2">Created</th>
                                                            <th class="w-[8%] px-3 py-2">Resolution</th>
                                                            <th class="w-[8%] px-3 py-2">SLA</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody data-live-report class="divide-y divide-slate-200">
                                                        @foreach([] as $ticket)
                                                            <tr class="hover:bg-slate-100 {{ $loop->even ? 'bg-slate-50' : '' }}">
                                                                <td class="truncate px-3 py-2 font-semibold text-slate-900">{{ $ticket[0] }}</td>
                                                                <td class="truncate px-3 py-2">{{ $ticket[1] }}</td>
                                                                <td class="truncate px-3 py-2">{{ $ticket[2] }}</td>
                                                                <td class="truncate px-3 py-2">{{ $ticket[3] }}</td>
                                                                <td class="truncate px-3 py-2">{{ $ticket[4] }}</td>
                                                                <td class="truncate px-3 py-2">{{ $ticket[5] }}</td>
                                                                <td class="truncate px-3 py-2">
                                                                    <span class="inline-flex rounded-full bg-slate-100 px-2 py-1 text-[10px] font-semibold text-slate-700">{{ $ticket[6] }}</span>
                                                                </td>
                                                                <td class="truncate px-3 py-2">
                                                                    @php
                                                                        $statusClasses = ['Open' => 'bg-emerald-100 text-emerald-700', 'Pending' => 'bg-amber-100 text-amber-700', 'Closed' => 'bg-slate-100 text-slate-600'];
                                                                    @endphp
                                                                    <span class="inline-flex rounded-full px-2 py-1 text-[10px] font-semibold {{ $statusClasses[$ticket[7]] }}">{{ $ticket[7] }}</span>
                                                                </td>
                                                                <td class="truncate px-3 py-2">{{ $ticket[8] }}</td>
                                                                <td class="truncate px-3 py-2">{{ $ticket[9] }}</td>
                                                                <td class="truncate px-3 py-2">{{ $ticket[10] }}</td>
                                                                <td class="truncate px-3 py-2">{{ $ticket[11] }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="flex items-center justify-between border-t border-slate-200 px-3 py-3 text-[11px] text-slate-500">
                                                <span>Showing 6 of 248 records</span>
                                                <div class="flex items-center gap-2">
                                                    <button class="rounded-lg border border-slate-200 bg-white px-3 py-1 hover:bg-slate-50">Prev</button>
                                                    <button class="rounded-lg border border-slate-200 bg-white px-3 py-1 hover:bg-slate-50">Next</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex w-full max-w-full min-h-0 min-w-0 flex-col gap-3 xl:w-[360px] xl:flex-shrink-0">
                                        <div class="rounded-[14px] border border-slate-200 bg-slate-50 p-4 shadow-sm">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="text-sm font-semibold text-slate-900">Filters</p>
                                                    <p class="mt-1 text-xs text-slate-500">Date, state, district, service</p>
                                                </div>
                                                <span class="rounded-full bg-white px-3 py-1 text-[10px] uppercase tracking-[0.2em] text-slate-500">Compact</span>
                                            </div>
                                            <div class="mt-4 grid gap-3">
                                                @foreach(['Date Range','State','District','Service','Vendor','Application','Priority','Status'] as $label)
                                                    <label class="block text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-500">{{ $label }}</label>
                                                    <input type="text" placeholder="Select {{ strtolower($label) }}" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-200" />
                                                @endforeach
                                            </div>
                                            <div class="mt-4 flex gap-3">
                                                <button class="flex-1 rounded-2xl bg-slate-900 px-3 py-3 text-sm font-semibold text-white hover:bg-slate-800">Apply Filters</button>
                                                <button class="flex-1 rounded-2xl border border-slate-200 bg-white px-3 py-3 text-sm font-semibold text-slate-900 hover:bg-slate-50">Reset</button>
                                            </div>
                                        </div>

                                        <div class="rounded-[14px] border border-slate-200 bg-slate-50 p-4 shadow-sm">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="text-sm font-semibold text-slate-900">Report Shortcuts</p>
                                                    <p class="mt-1 text-xs text-slate-500">Quick access to reports</p>
                                                </div>
                                                <span class="rounded-full bg-white px-3 py-1 text-[10px] uppercase tracking-[0.2em] text-slate-500">9</span>
                                            </div>
                                            <div class="mt-4 grid gap-2 text-sm text-slate-700">
                                                @foreach(['Daily Report','Weekly Report','Monthly Report','Vendor Performance Report','SLA Compliance Report','Service Availability Report','State Analysis Report','Audit Report','Export Reports'] as $label)
                                                    <button class="flex w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-3 py-3 text-left text-sm text-slate-700 transition hover:bg-slate-100">
                                                        <span>{{ $label }}</span>
                                                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
</x-app-layout>

<style>
    .central-dashboard {
        --dashboard-blue: #1976f3;
        --dashboard-violet: #6941e8;
        --dashboard-green: #11b981;
        --dashboard-orange: #f59e0b;
        --dashboard-cyan: #06b6d4;
    }

    .central-dashboard > div > div:first-child > div:first-child {
        border: 0;
        border-radius: 0 0 14px 14px;
        background: linear-gradient(110deg, #1688ee 0%, #345bf1 48%, #743be9 100%);
        color: white;
        box-shadow: 0 10px 24px rgba(59, 91, 232, .22);
    }

    .central-dashboard > div > div:first-child > div:first-child p,
    .central-dashboard > div > div:first-child > div:first-child span {
        color: white !important;
    }

    .central-dashboard [data-live-card] {
        font-size: 2rem;
        color: #123875;
    }

    .central-dashboard > div > div:first-child > div:nth-child(2) > div:first-child > div {
        border-radius: 14px;
        background: linear-gradient(145deg, #eef7ff, #dceeff);
        border-color: #8bc5ff;
    }

    .central-dashboard > div > div:first-child > div:nth-child(2) > div:first-child > div:nth-child(2) {
        background: linear-gradient(145deg, #ecfff7, #d8f9e9);
        border-color: #8be6bd;
    }

    .central-dashboard > div > div:first-child > div:nth-child(2) > div:first-child > div:nth-child(3) {
        background: linear-gradient(145deg, #f7f0ff, #eee4ff);
        border-color: #c7aaff;
    }

    .central-dashboard > div > div:first-child > div:nth-child(2) > div:first-child > div:nth-child(4) {
        background: linear-gradient(145deg, #fff0f3, #ffe0e8);
        border-color: #ff9db6;
    }

    .central-dashboard > div > div:first-child > div:first-child + div > div {
        border-radius: 14px;
        background: white;
    }

    .central-dashboard .bg-slate-50 {
        background-color: #fff;
    }

    .central-dashboard .bg-slate-50.rounded-\[14px\] {
        box-shadow: 0 8px 20px rgba(50, 75, 130, .08);
    }

    .central-dashboard .mt-3.grid > div:nth-child(1) {
        border-color: #8bc5ff;
        background: linear-gradient(145deg, #f2f8ff, #e4f1ff);
    }

    .central-dashboard .mt-3.grid > div:nth-child(2) {
        border-color: #c5b1ff;
        background: linear-gradient(145deg, #faf6ff, #f1ebff);
    }

    .central-dashboard .mt-3.grid > div:nth-child(3) {
        border-color: #8be6ed;
        background: linear-gradient(145deg, #f0fdff, #e1f9fc);
    }

    .central-dashboard table thead {
        background: linear-gradient(90deg, #2964ef, #6941e8, #08a9dc);
        color: white;
    }

    .central-dashboard .bg-slate-200 {
        background-color: #2f7cf0;
    }
</style>

                <script>
                    (function () {
                        const endpoint = @json(route('dashboard.live-data'));
                        const cards = document.querySelectorAll('[data-live-card]');
                        const updatedLabel = document.querySelector('[data-live-updated]');

                        const escapeHtml = (value) => String(value ?? '-').replace(/[&<>'"]/g, (character) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[character]));
                        const formatMetric = (value) => value === null || value === undefined || value === '' ? '—' : value;

                        function renderAnalysisRows(key, rows) {
                            const target = document.querySelector(`[data-live-analysis="${key}"]`);
                            if (!target) return;
                            target.innerHTML = (rows || []).map((row) => `<div class="grid w-full min-w-0 grid-cols-[minmax(0,1.3fr)_minmax(0,0.8fr)_minmax(0,0.7fr)_minmax(0,0.7fr)_minmax(0,0.6fr)] gap-2 text-slate-600"><span class="font-semibold text-slate-900">${escapeHtml(row[0])}</span><span>${escapeHtml(row[1])}</span><span>${escapeHtml(row[2])}</span><span>${escapeHtml(row[3])}</span><span>${escapeHtml(row[4])}</span></div>`).join('') || '<p class="text-slate-500">No data available.</p>';
                        }

                        function renderMonthly(rows) {
                            const target = document.querySelector('[data-live-monthly-chart]');
                            if (!target) return;
                            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                            const max = Math.max(1, ...(rows || []).map((row) => Number(row[0] || 0)));
                            target.innerHTML = (rows || []).map((row) => {
                                const total = Number(row[0] || 0);
                                const resolved = Number(row[1] || 0);
                                const open = Number(row[2] || 0);
                                const pending = Math.max(0, total - resolved - open);
                                return `<div class="flex h-full items-end justify-center gap-px" title="${total} issues"><span class="w-1/3 rounded-t-sm bg-blue-500" style="height:${Math.max(2, open / max * 100)}%"></span><span class="w-1/3 rounded-t-sm bg-emerald-500" style="height:${Math.max(2, resolved / max * 100)}%"></span><span class="w-1/3 rounded-t-sm bg-amber-400" style="height:${Math.max(2, pending / max * 100)}%"></span></div>`;
                            }).join('');
                            const labels = target.parentElement.querySelector('[data-live-month-labels]');
                            if (labels) labels.innerHTML = months.map((month) => `<div class="text-center">${month}</div>`).join('');
                        }

                        function renderReport(rows) {
                            const target = document.querySelector('[data-live-report]');
                            if (!target) return;
                            target.innerHTML = (rows || []).map((row, index) => `<tr class="hover:bg-slate-100 ${index % 2 ? 'bg-slate-50' : ''}">${row.map((value, column) => `<td class="truncate px-3 py-2 ${column === 0 ? 'font-semibold text-slate-900' : ''}">${escapeHtml(value)}</td>`).join('')}</tr>`).join('') || '<tr><td colspan="12" class="px-3 py-5 text-center text-slate-500">No issues found.</td></tr>';
                        }

                        async function refreshCentralDashboard() {
                            try {
                                const response = await fetch(endpoint, { headers: { Accept: 'application/json' } });
                                if (!response.ok) return;
                                const data = await response.json();
                                cards.forEach((card) => {
                                    const value = Number(data[card.dataset.liveCard] || 0);
                                    card.textContent = value.toLocaleString();
                                });
                                document.querySelectorAll('[data-live-metric]').forEach((metric) => {
                                    metric.textContent = formatMetric(data[metric.dataset.liveMetric]);
                                });
                                const slaBar = document.querySelector('[data-live-sla-bar]');
                                if (slaBar && data.sla_compliance !== '—') slaBar.style.width = `${Number.parseInt(data.sla_compliance, 10) || 0}%`;
                                renderMonthly(data.monthly);
                                renderAnalysisRows('state', data.state_rows);
                                renderAnalysisRows('service', data.service_rows);
                                renderAnalysisRows('vendor', data.vendor_rows);
                                renderReport(data.recent_issues);
                                if (updatedLabel) updatedLabel.textContent = data.updated_at || 'Just now';
                            } catch (error) {
                                return;
                            }
                        }

                        refreshCentralDashboard();
                        window.setInterval(refreshCentralDashboard, 5000);
                    }());
                </script>

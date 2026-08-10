<x-app-layout>
    <main class="min-h-screen bg-slate-100 p-0">
                    <div class="flex h-full flex-col overflow-hidden border border-slate-200 bg-white shadow-sm">
                        <div class="flex flex-1 flex-col overflow-hidden">
                            <div class="border-b border-slate-200 px-5 py-4">
                                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                                    <div>
                                        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">Dashboard</p>
                                        <p class="mt-1 text-base text-slate-700">Consolidated view of issues across all states and services.</p>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-3 text-sm text-slate-500">
                                        <span>Data Period: 01 Jul 2026 - 31 Jul 2026</span>
                                        <span class="h-4 w-px bg-slate-200"></span>
                                        <span>Updated: 31 Jul 2026</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex-1 overflow-y-auto px-2 py-2">
                                <div class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                                    @php
                                        $cards = [
                                            ['label' => 'Total Issues', 'value' => '2,450', 'color' => 'blue', 'trend' => '+12%'],
                                            ['label' => 'Active Services', 'value' => '680', 'color' => 'emerald', 'trend' => '+8%'],
                                            ['label' => 'Open Issues', 'value' => '1,770', 'color' => 'violet', 'trend' => '-4%'],
                                            ['label' => 'Critical Issues', 'value' => '45', 'color' => 'red', 'trend' => '+3%'],
                                            ['label' => 'Resolved Today', 'value' => '62', 'color' => 'orange', 'trend' => '+7%'],
                                            ['label' => 'Pending Vendors', 'value' => '28', 'color' => 'cyan', 'trend' => '+9%'],
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
                                            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $card['value'] }}</p>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-3 grid gap-3 grid-cols-1 lg:grid-cols-2 xl:grid-cols-3">
                                    <div class="rounded-[14px] border border-slate-200 bg-slate-50 p-4 shadow-sm">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-semibold text-slate-900">Average Resolution Time</p>
                                                <p class="mt-3 text-2xl font-semibold text-slate-900">18.6 h</p>
                                            </div>
                                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-white text-slate-900 shadow-sm">
                                                <span class="text-xl font-semibold">78%</span>
                                            </div>
                                        </div>
                                        <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-slate-200">
                                            <div class="h-full w-[78%] rounded-full bg-blue-500"></div>
                                        </div>
                                    </div>
                                    <div class="rounded-[14px] border border-slate-200 bg-slate-50 p-4 shadow-sm">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-semibold text-slate-900">Overall SLA Compliance</p>
                                                <p class="mt-3 text-2xl font-semibold text-slate-900">91%</p>
                                            </div>
                                            <div class="relative flex h-16 w-16 items-center justify-center rounded-full bg-white text-slate-900 shadow-sm">
                                                <div class="absolute inset-0 rounded-full bg-slate-200"></div>
                                                <div class="absolute inset-2 rounded-full bg-white"></div>
                                                <span class="relative text-sm font-semibold text-slate-900">91%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="rounded-[14px] border border-slate-200 bg-slate-50 p-4 shadow-sm">
                                        <p class="text-sm font-semibold text-slate-900">Monthly Issue Trend</p>
                                        <p class="mt-2 text-[11px] uppercase tracking-[0.22em] text-slate-500">Open · Closed · Pending</p>
                                        <div class="mt-4 grid h-24 grid-cols-12 gap-1 items-end">
                                            @foreach([5,7,6,8,7,9,8,7,10,9,11,10] as $value)
                                                <div class="rounded-sm bg-slate-200" style="height: {{ $value * 8 }}px"></div>
                                            @endforeach
                                        </div>
                                        <div class="mt-3 grid grid-cols-12 gap-1 text-[10px] text-slate-500">
                                            @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $month)
                                                <div class="text-center">{{ $month }}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3 grid gap-3 grid-cols-1 lg:grid-cols-2 xl:grid-cols-3">
                                    <div class="rounded-[14px] border border-slate-200 bg-slate-50 p-4 shadow-sm">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-semibold text-slate-900">State-wise Analysis</p>
                                            <a href="#" class="text-xs font-semibold text-slate-500">View all</a>
                                        </div>
                                        <div class="mt-4 overflow-hidden">
                                            <div class="grid w-full min-w-0 grid-cols-[minmax(0,1.3fr)_minmax(0,0.8fr)_minmax(0,0.7fr)_minmax(0,0.7fr)_minmax(0,0.6fr)] gap-2 text-[10px] uppercase tracking-[0.2em] text-slate-400">
                                                <span>State</span>
                                                <span>Issues</span>
                                                <span>Open</span>
                                                <span>Resolved</span>
                                                <span>SLA</span>
                                            </div>
                                            <div class="mt-2 space-y-3 text-[11px] text-slate-700">
                                                @foreach([['Telangana','1,120','920','120','82%'],['Andhra Pradesh','740','560','110','76%'],['Odisha','430','340','70','79%'],['Karnataka','290','210','50','72%'],['Chhattisgarh','180','140','35','78%']] as $row)
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
                                        <div class="mt-4 overflow-hidden">
                                            <div class="grid w-full min-w-0 grid-cols-[minmax(0,1.3fr)_minmax(0,0.8fr)_minmax(0,0.7fr)_minmax(0,0.7fr)_minmax(0,0.6fr)] gap-2 text-[10px] uppercase tracking-[0.2em] text-slate-400">
                                                <span>Service</span>
                                                <span>Issues</span>
                                                <span>Open</span>
                                                <span>Resolved</span>
                                                <span>SLA</span>
                                            </div>
                                            <div class="mt-2 space-y-3 text-[11px] text-slate-700">
                                                @foreach([['Connectivity','980','760','120','80%'],['Application','640','510','90','79%'],['Hardware','420','330','60','79%'],['Support','280','210','45','75%'],['Security','170','140','30','81%']] as $row)
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
                                        <div class="mt-4 overflow-hidden">
                                            <div class="grid w-full min-w-0 grid-cols-[minmax(0,1.3fr)_minmax(0,0.8fr)_minmax(0,0.7fr)_minmax(0,0.7fr)_minmax(0,0.6fr)] gap-2 text-[10px] uppercase tracking-[0.2em] text-slate-400">
                                                <span>Vendor</span>
                                                <span>Issues</span>
                                                <span>Open</span>
                                                <span>Resolved</span>
                                                <span>SLA</span>
                                            </div>
                                            <div class="mt-2 space-y-3 text-[11px] text-slate-700">
                                                @foreach([['Vendor A','310','250','40','81%'],['Vendor B','220','180','30','78%'],['Vendor C','150','120','25','80%'],['Vendor D','110','82','18','74%'],['Vendor E','95','74','15','78%']] as $row)
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
                                                    <tbody class="divide-y divide-slate-200">
                                                        @foreach([
                                                            ['#2011','Power outage','Telangana','Hyderabad','Network','Vendor A','High','Open','R. Kumar','2026-07-30','2.8h','85%'],
                                                            ['#2008','Server failure','Andhra Pradesh','Vijayawada','Infrastructure','Vendor B','Critical','Pending','S. Patel','2026-07-29','4.2h','71%'],
                                                            ['#1999','App latency','Odisha','Bhubaneswar','Application','Vendor C','Medium','Closed','A. Das','2026-07-28','6.4h','92%'],
                                                            ['#1984','Backup delay','Karnataka','Bengaluru','Storage','Vendor D','High','Open','M. Rao','2026-07-28','3.1h','88%'],
                                                            ['#1977','Security alert','Chhattisgarh','Raipur','Security','Vendor E','Critical','Pending','N. Singh','2026-07-27','5.5h','68%'],
                                                            ['#1963','Compliance review','Telangana','Warangal','Process','Vendor A','Low','Closed','S. Iyer','2026-07-26','8.0h','94%'],
                                                        ] as $ticket)
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

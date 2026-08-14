@php
    use Illuminate\Support\Facades\Route;
@endphp

<x-app-layout :without-sidebar="true">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ auth()->user()->role_names ?: 'Role Dashboard' }}
        </h2>
    </x-slot>

    <!-- Signal to client navigation: hide sidebar when this page is loaded via AJAX -->
    <div data-hide-sidebar hidden></div>

    <div class="min-h-screen bg-[#F7F9FC] py-10 text-[#0F172A]" style="font-family: 'Poppins', sans-serif;">
        <div class="mx-auto w-[97%] max-w-[1700px]">
            @php
                $greeting = now()->hour < 12 ? 'Good Morning' : (now()->hour < 17 ? 'Good Afternoon' : 'Good Evening');
                $roleLabel = auth()->user()->role_names ?: 'Central Admin';
            @endphp

            <div class="mb-6 rounded-[18px] border border-[#E6ECF5] bg-gradient-to-r from-white to-[#EEF5FF] px-6 py-4 shadow-[0_6px_24px_rgba(15,23,42,0.05)]">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div class="inline-flex items-center gap-3">
                        <span class="text-2xl">👋</span>
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-[#64748B]">Welcome back</p>
                            <p class="text-lg font-semibold text-[#0F172A]">{{ $greeting }}, {{ auth()->user()->name ?? auth()->user()->login_id }}!</p>
                        </div>
                    </div>
                    <div class="inline-flex items-center gap-3 rounded-[18px] border border-[#E6ECF5] bg-white px-3 py-2 shadow-sm">
                        <span class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#64748B]">Role</span>
                        <span class="h-8 border-r border-[#E6ECF5]"></span>
                        <span class="rounded-[30px] bg-gradient-to-r from-[#3B82F6] to-[#9333EA] px-4 py-1.5 text-sm font-semibold text-white">{{ $roleLabel }}</span>
                    </div>
                </div>
            </div>


            <div class="grid gap-[22px] [grid-template-columns:repeat(auto-fit,minmax(320px,1fr))]">
                @forelse(auth()->user()->menus as $menu)
                    @php
                        $routeName = $menu->route_name ?? $menu->uri;
                        $category = 'default';
                        if (str_contains($routeName, 'dashboard')) {
                            $category = 'dashboard';
                        } elseif (str_contains($routeName, 'issue') && !str_contains($routeName, 'raise')) {
                            $category = 'issues';
                        } elseif (str_contains($routeName, 'raise') || str_contains($routeName, 'create')) {
                            $category = 'raise';
                        } elseif (str_contains($routeName, 'report')) {
                            $category = 'reports';
                        } elseif (str_contains($routeName, 'state') && str_contains($routeName, 'master')) {
                            $category = 'state';
                        } elseif (str_contains($routeName, 'vendor') && str_contains($routeName, 'master')) {
                            $category = 'vendor';
                        } elseif (str_contains($routeName, 'level2') || str_contains($routeName, 'level-2')) {
                            $category = 'vendor-level2';
                        } elseif (str_contains($routeName, 'vendor.state')) {
                            $category = 'vendor-state';
                        } elseif (str_contains($routeName, 'project') && str_contains($routeName, 'mapping')) {
                            $category = 'project-mapping';
                        } elseif (str_contains($routeName, 'project')) {
                            $category = 'project';
                        } elseif (str_contains($routeName, 'application')) {
                            $category = 'application';
                        } elseif (str_contains($routeName, 'module')) {
                            $category = 'module';
                        } elseif (str_contains($routeName, 'user') && str_contains($routeName, 'mapping')) {
                            $category = 'user-mapping';
                        } elseif (str_contains($routeName, 'user')) {
                            $category = 'user';
                        } elseif (str_contains($routeName, 'role') && str_contains($routeName, 'mapping')) {
                            $category = 'role-mapping';
                        } elseif (str_contains($routeName, 'role')) {
                            $category = 'role';
                        } elseif (str_contains($routeName, 'holiday')) {
                            $category = 'holiday';
                        } elseif (str_contains($routeName, 'activity')) {
                            $category = 'activity';
                        } elseif (str_contains($routeName, 'audit') || str_contains($routeName, 'logs')) {
                            $category = 'audit';
                        } elseif (str_contains($routeName, 'working') || str_contains($routeName, 'hours')) {
                            $category = 'working';
                        }

                        $palette = [
                            'dashboard' => ['border' => 'border-l-8 border-[#1D4ED8]', 'icon' => 'bg-gradient-to-br from-[#3B82F6] to-[#2563EB]', 'button' => 'from-[#3B82F6] to-[#2563EB]', 'cardBg' => 'rgba(59,130,246,0.08)', 'cardShadow' => 'rgba(59,130,246,0.16)', 'iconShadow' => 'rgba(59,130,246,0.24)'],
                            'issues' => ['border' => 'border-l-8 border-[#4338CA]', 'icon' => 'bg-gradient-to-br from-[#4338CA] to-[#3730A3]', 'button' => 'from-[#4338CA] to-[#3730A3]', 'cardBg' => 'rgba(67,56,202,0.08)', 'cardShadow' => 'rgba(67,56,202,0.16)', 'iconShadow' => 'rgba(67,56,202,0.24)'],
                            'raise' => ['border' => 'border-l-8 border-[#F97316]', 'icon' => 'bg-gradient-to-br from-[#FB923C] to-[#F97316]', 'button' => 'from-[#FB923C] to-[#F97316]', 'cardBg' => 'rgba(251,146,60,0.08)', 'cardShadow' => 'rgba(251,146,60,0.16)', 'iconShadow' => 'rgba(251,146,60,0.24)'],
                            'reports' => ['border' => 'border-l-8 border-[#16A34A]', 'icon' => 'bg-gradient-to-br from-[#22C55E] to-[#16A34A]', 'button' => 'from-[#22C55E] to-[#16A34A]', 'cardBg' => 'rgba(34,197,94,0.08)', 'cardShadow' => 'rgba(34,197,94,0.16)', 'iconShadow' => 'rgba(34,197,94,0.24)'],
                            'state' => ['border' => 'border-l-8 border-[#7C3AED]', 'icon' => 'bg-gradient-to-br from-[#8B5CF6] to-[#7C3AED]', 'button' => 'from-[#8B5CF6] to-[#7C3AED]', 'cardBg' => 'rgba(139,92,246,0.08)', 'cardShadow' => 'rgba(139,92,246,0.16)', 'iconShadow' => 'rgba(139,92,246,0.24)'],
                            'vendor' => ['border' => 'border-l-8 border-[#EC4899]', 'icon' => 'bg-gradient-to-br from-[#F472B6] to-[#EC4899]', 'button' => 'from-[#F472B6] to-[#EC4899]', 'cardBg' => 'rgba(244,114,182,0.08)', 'cardShadow' => 'rgba(244,114,182,0.16)', 'iconShadow' => 'rgba(244,114,182,0.24)'],
                            'project' => ['border' => 'border-l-8 border-[#F59E0B]', 'icon' => 'bg-gradient-to-br from-[#FBBF24] to-[#F59E0B]', 'button' => 'from-[#FBBF24] to-[#F59E0B]', 'cardBg' => 'rgba(251,191,36,0.08)', 'cardShadow' => 'rgba(251,191,36,0.16)', 'iconShadow' => 'rgba(251,191,36,0.24)'],
                            'project-mapping' => ['border' => 'border-l-8 border-[#F97316]', 'icon' => 'bg-gradient-to-br from-[#FB923C] to-[#F97316]', 'button' => 'from-[#FB923C] to-[#F97316]', 'cardBg' => 'rgba(251,146,60,0.08)', 'cardShadow' => 'rgba(251,146,60,0.16)', 'iconShadow' => 'rgba(251,146,60,0.24)'],
                            'application' => ['border' => 'border-l-8 border-[#8B5CF6]', 'icon' => 'bg-gradient-to-br from-[#A78BFA] to-[#8B5CF6]', 'button' => 'from-[#A78BFA] to-[#8B5CF6]', 'cardBg' => 'rgba(167,139,250,0.08)', 'cardShadow' => 'rgba(167,139,250,0.16)', 'iconShadow' => 'rgba(167,139,250,0.24)'],
                            'module' => ['border' => 'border-l-8 border-[#06B6D4]', 'icon' => 'bg-gradient-to-br from-[#38BDF8] to-[#06B6D4]', 'button' => 'from-[#38BDF8] to-[#06B6D4]', 'cardBg' => 'rgba(56,189,248,0.08)', 'cardShadow' => 'rgba(56,189,248,0.16)', 'iconShadow' => 'rgba(56,189,248,0.24)'],
                            'user' => ['border' => 'border-l-8 border-[#2DD4BF]', 'icon' => 'bg-gradient-to-br from-[#5EEAD4] to-[#2DD4BF]', 'button' => 'from-[#5EEAD4] to-[#2DD4BF]', 'cardBg' => 'rgba(93,234,212,0.08)', 'cardShadow' => 'rgba(93,234,212,0.16)', 'iconShadow' => 'rgba(93,234,212,0.24)'],
                            'user-mapping' => ['border' => 'border-l-8 border-[#06B6D4]', 'icon' => 'bg-gradient-to-br from-[#38BDF8] to-[#06B6D4]', 'button' => 'from-[#38BDF8] to-[#06B6D4]', 'cardBg' => 'rgba(56,189,248,0.08)', 'cardShadow' => 'rgba(56,189,248,0.16)', 'iconShadow' => 'rgba(56,189,248,0.24)'],
                            'role' => ['border' => 'border-l-8 border-[#2563EB]', 'icon' => 'bg-gradient-to-br from-[#3B82F6] to-[#2563EB]', 'button' => 'from-[#3B82F6] to-[#2563EB]', 'cardBg' => 'rgba(59,130,246,0.08)', 'cardShadow' => 'rgba(59,130,246,0.16)', 'iconShadow' => 'rgba(59,130,246,0.24)'],
                            'role-mapping' => ['border' => 'border-l-8 border-[#7C3AED]', 'icon' => 'bg-gradient-to-br from-[#A78BFA] to-[#7C3AED]', 'button' => 'from-[#A78BFA] to-[#7C3AED]', 'cardBg' => 'rgba(167,139,250,0.08)', 'cardShadow' => 'rgba(167,139,250,0.16)', 'iconShadow' => 'rgba(167,139,250,0.24)'],
                            'vendor-level2' => ['border' => 'border-l-8 border-[#9333EA]', 'icon' => 'bg-gradient-to-br from-[#A78BFA] to-[#9333EA]', 'button' => 'from-[#A78BFA] to-[#9333EA]', 'cardBg' => 'rgba(167,139,250,0.08)', 'cardShadow' => 'rgba(167,139,250,0.16)', 'iconShadow' => 'rgba(167,139,250,0.24)'],
                            'vendor-state' => ['border' => 'border-l-8 border-[#7C3AED]', 'icon' => 'bg-gradient-to-br from-[#C084FC] to-[#7C3AED]', 'button' => 'from-[#C084FC] to-[#7C3AED]', 'cardBg' => 'rgba(192,132,252,0.08)', 'cardShadow' => 'rgba(192,132,252,0.16)', 'iconShadow' => 'rgba(192,132,252,0.24)'],
                            'holiday' => ['border' => 'border-l-8 border-[#22C55E]', 'icon' => 'bg-gradient-to-br from-[#34D399] to-[#22C55E]', 'button' => 'from-[#34D399] to-[#22C55E]', 'cardBg' => 'rgba(52,211,153,0.08)', 'cardShadow' => 'rgba(52,211,153,0.16)', 'iconShadow' => 'rgba(52,211,153,0.24)'],
                            'activity' => ['border' => 'border-l-8 border-[#0EA5E9]', 'icon' => 'bg-gradient-to-br from-[#38BDF8] to-[#0EA5E9]', 'button' => 'from-[#38BDF8] to-[#0EA5E9]', 'cardBg' => 'rgba(56,189,248,0.08)', 'cardShadow' => 'rgba(56,189,248,0.16)', 'iconShadow' => 'rgba(56,189,248,0.24)'],
                            'audit' => ['border' => 'border-l-8 border-[#10B981]', 'icon' => 'bg-gradient-to-br from-[#34D399] to-[#10B981]', 'button' => 'from-[#34D399] to-[#10B981]', 'cardBg' => 'rgba(34,211,153,0.08)', 'cardShadow' => 'rgba(34,211,153,0.16)', 'iconShadow' => 'rgba(34,211,153,0.24)'],
                            'working' => ['border' => 'border-l-8 border-[#22D3EE]', 'icon' => 'bg-gradient-to-br from-[#67E8F9] to-[#22D3EE]', 'button' => 'from-[#67E8F9] to-[#22D3EE]', 'cardBg' => 'rgba(102,232,249,0.08)', 'cardShadow' => 'rgba(102,232,249,0.16)', 'iconShadow' => 'rgba(102,232,249,0.24)'],
                            'default' => ['border' => 'border-l-8 border-[#60A5FA]', 'icon' => 'bg-gradient-to-br from-[#60A5FA] to-[#0EA5E9]', 'button' => 'from-[#60A5FA] to-[#0EA5E9]', 'cardBg' => 'rgba(96,165,250,0.08)', 'cardShadow' => 'rgba(96,165,250,0.16)', 'iconShadow' => 'rgba(96,165,250,0.24)'],
                        ][$category];
                    @endphp

                    <a data-no-ajax="true" href="{{ $menu->route_name && Route::has($menu->route_name) ? route($menu->route_name) : ($menu->uri ?? '#') }}" style="--card-shadow: {{ $palette['cardShadow'] }}; --icon-shadow: {{ $palette['iconShadow'] }};" class="group relative flex h-full min-h-[190px] flex-col justify-between overflow-hidden rounded-[18px] border-t-4 {{ $palette['border'] }} border border-[#EEF2F7] bg-white p-[18px] shadow-[0_12px_30px_var(--card-shadow)] transition duration-300 hover:-translate-y-[6px] hover:shadow-[0_20px_45px_var(--card-shadow)]">
                        <div class="flex items-start justify-between gap-4">
                            <div class="inline-flex h-[56px] w-[56px] flex-shrink-0 items-center justify-center rounded-[16px] {{ $palette['icon'] }} shadow-[0_12px_24px_var(--icon-shadow)] text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $menu->icon ?? 'M4 6h16M4 12h16M4 18h16' }}"></path></svg>
                            </div>
                            <span class="inline-flex shrink-0 items-center rounded-full bg-[#F8FAFC] px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.30em] text-[#64748B]">Open</span>
                        </div>
                        <div class="mt-4 min-w-0">
                            <h4 class="text-[20px] font-semibold leading-tight text-[#0F172A] overflow-hidden" style="display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">
                                {{ $menu->display_name }}
                            </h4>
                        </div>
                        <div class="mt-6 flex items-center justify-between gap-3 text-sm text-[#64748B]">
                            <span class="flex-1 min-w-0 inline-flex items-center gap-2 truncate">
                                <svg class="h-4 w-4 text-inherit" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"></path></svg>
                                <span class="truncate">{{ $menu->route_name ?? $menu->uri }}</span>
                            </span>
                            <button class="inline-flex h-[38px] min-w-[90px] items-center justify-center rounded-[12px] bg-gradient-to-r {{ $palette['button'] }} px-4 text-sm font-semibold text-white transition duration-300 group-hover:scale-105">
                                Open
                                <svg class="ml-2 h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </button>
                        </div>
                    </a>
                @empty
                    <div class="rounded-[18px] border border-[#EEF2F7] bg-white p-[22px] text-[#64748B] shadow-[0_8px_24px_rgba(0,0,0,0.05)]">
                        <h4 class="text-lg font-semibold text-[#0F172A]">No menu access assigned</h4>
                        <p class="mt-2 text-sm">Contact your administrator to assign role-based menu permissions.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>

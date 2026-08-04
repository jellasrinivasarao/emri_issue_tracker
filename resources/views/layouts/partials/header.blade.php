<header class="top-header">

    <div class="header-left">

        <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
            ☰
        </button>

        <div class="brand-mobile">
            Centralised Admin
        </div>

    </div>


    <div class="header-right">

        {{-- Notifications --}}

        <button type="button" class="header-icon" title="Notifications">
            🔔
        </button>


        {{-- User Menu --}}

        <div class="user-menu">

            <button type="button" class="user-menu-button" id="userMenuButton">

                <span class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </span>

                <span class="user-info">

                    <strong>
                        {{ auth()->user()->name ?? 'Administrator' }}
                    </strong>

                    <small>
                        Administrator
                    </small>

                </span>

                <span class="user-arrow">
                    ▼
                </span>

            </button>


            <div class="user-dropdown" id="userDropdown">

                <a href="#">
                    Profile
                </a>

                <a href="#">
                    Change Password
                </a>

                <div class="dropdown-divider"></div>

                <form method="POST" action="{{ route('logout') }}">

                    @csrf

                    <button type="submit">
                        Logout
                    </button>

                </form>

            </div>

        </div>

    </div>

</header>
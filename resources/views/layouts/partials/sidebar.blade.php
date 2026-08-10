<aside class="sidebar" id="sidebar">

    {{-- Logo --}}

    <div class="sidebar-brand">

        <div class="brand-logo">
            CA
        </div>

        <div class="brand-text">

            <strong>
                Centralised
            </strong>

            <small>
                Administration
            </small>

        </div>

    </div>


    {{-- Navigation --}}

    <nav class="sidebar-nav">

        {{-- Dashboard --}}

        <a href="{{ route('dashboard') }}" class="sidebar-link
                {{ request()->routeIs('dashboard')
                    ? 'active'
                    : '' }}">

            <span class="sidebar-icon">
                🏠
            </span>

            <span>
                Dashboard
            </span>

        </a>


        {{-- MASTER DATA --}}

        <div class="sidebar-section">

            <span>
                MASTER DATA
            </span>

        </div>


        {{-- Organisation --}}

        <a href="#" class="sidebar-link">

            <span class="sidebar-icon">
                🏢
            </span>

            <span>
                Organisation
            </span>

        </a>


        {{-- State --}}

        <a href="" class="sidebar-link {{ request()->routeIs('admin.states.*') ? 'active' : '' }}">

            <span class="sidebar-icon">
                📍
            </span>

            <span>
                State
            </span>

        </a>


        {{-- Head Office --}}

        <a href="#" class="sidebar-link">

            <span class="sidebar-icon">
                🏛
            </span>

            <span>
                Head Office
            </span>

        </a>


        {{-- Vendor --}}

        <a href="#" class="sidebar-link">

            <span class="sidebar-icon">
                🚚
            </span>

            <span>
                Vendor
            </span>

        </a>


        {{-- Support Group --}}

        <a href="#" class="sidebar-link">

            <span class="sidebar-icon">
                👥
            </span>

            <span>
                Support Group
            </span>

        </a>


        {{-- Application --}}

        <a href="#" class="sidebar-link">

            <span class="sidebar-icon">
                🖥
            </span>

            <span>
                Application
            </span>

        </a>


        {{-- Application Module --}}

        <a href="#" class="sidebar-link">

            <span class="sidebar-icon">
                🧩
            </span>

            <span>
                Application Module
            </span>

        </a>


        {{-- Project --}}

        <a href="#" class="sidebar-link">

            <span class="sidebar-icon">
                📁
            </span>

            <span>
                Project
            </span>

        </a>


        {{-- Project Application --}}

        <a href="#" class="sidebar-link
                {{ request()->routeIs('admin.project-applications.*')
                    ? 'active'
                    : '' }}">

            <span class="sidebar-icon">
                🔗
            </span>

            <span>
                Project ↔ Application
            </span>

        </a>


        {{-- Project Service --}}

        <a href="" class="sidebar-link
                {{ request()->routeIs('admin.project-services.*')
                    ? 'active'
                    : '' }}">

            <span class="sidebar-icon">
                🔗
            </span>

            <span>
                Project ↔ Service
            </span>

        </a>


        {{-- Priority --}}

        <a href="#" class="sidebar-link">

            <span class="sidebar-icon">
                ⚡
            </span>

            <span>
                Priority
            </span>

        </a>


        {{-- Issue Category --}}

        <a href="#" class="sidebar-link">

            <span class="sidebar-icon">
                🗂
            </span>

            <span>
                Issue Category
            </span>

        </a>


        {{-- Issue Status --}}

        <a href="#" class="sidebar-link">

            <span class="sidebar-icon">
                🔄
            </span>

            <span>
                Issue Status
            </span>

        </a>


        {{-- Service --}}

        <a href="#" class="sidebar-link">

            <span class="sidebar-icon">
                🛠
            </span>

            <span>
                Service
            </span>

        </a>


        {{-- Working Calendar --}}

        <a href="#" class="sidebar-link">

            <span class="sidebar-icon">
                📅
            </span>

            <span>
                Working Calendar
            </span>

        </a>


        {{-- CONFIGURATION --}}

        <div class="sidebar-section">

            <span>
                CONFIGURATION
            </span>

        </div>


        {{-- SLA --}}

        <a href="#" class="sidebar-link">

            <span class="sidebar-icon">
                ⏱
            </span>

            <span>
                SLA Policy
            </span>

        </a>


        {{-- Issue Routing --}}

        <a href="#" class="sidebar-link">

            <span class="sidebar-icon">
                🔀
            </span>

            <span>
                Issue Routing
            </span>

        </a>


        {{-- SECURITY --}}

        <div class="sidebar-section">

            <span>
                SECURITY
            </span>

        </div>


        <a href="#" class="sidebar-link">

            <span class="sidebar-icon">
                👤
            </span>

            <span>
                Users
            </span>

        </a>


        <a href="#" class="sidebar-link">

            <span class="sidebar-icon">
                🔐
            </span>

            <span>
                Roles & Privileges
            </span>

        </a>

    </nav>

</aside>
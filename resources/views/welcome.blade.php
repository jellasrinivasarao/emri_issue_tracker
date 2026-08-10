<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>EMRI Issue Tracker</title>

    @vite([
        'resources/css/landing.css',
        'resources/css/responsive.css',
        'resources/css/animation.css',
        'resources/js/landing.js'
    ])
</head>

<body>

    @php

        /*
        |--------------------------------------------------------------------------
        | Image References
        |--------------------------------------------------------------------------
        */

        $logo = asset('images/logo.png');

        $heroDashboard = asset('images/hero-dashboard.png');

        $dashboardPreview = asset('images/dashboard-preview.png');

        $footerBackground = asset('images/footer-bg.svg');

    @endphp



    <header class="header">

        <div class="container">

            <!-- Logo -->

            <div class="logo">

                <img src="{{ $logo }}" alt="EMRI Logo" class="logo-img">

                <div class="logo-text">

                    <h2>EMRI ISSUE TRACKER</h2>

                    <p>Enterprise Issue Management System</p>

                </div>

            </div>

            <!-- Navigation -->

           <nav class="navbar">

                <ul>

                    <li><a href="#hero">Home</a></li>

                    <li><a href="#features">Features</a></li>

                    <li><a href="#modules">Modules</a></li>

                    <li><a href="#about">About</a></li>

                    <li><a href="#contact">Contact</a></li>

                </ul>

            </nav>

            <!-- Actions -->

            <div class="header-right">

                <a href="{{ route('login') }}" class="btn-login">

                    Login

                </a>

                <a href="#features" class="btn-register">

                    Register

                </a>

            </div>

        </div>

    </header>



    <section class="hero" id="hero">

        <div class="container hero-wrapper">

            <!-- Left -->

            <div class="hero-left">

                <span class="hero-tag">Enterprise Issue Management</span>

                <h1>
                    Enterprise Issue Tracker
                </h1>

                <p>
                    A centralized platform to manage, track, assign, monitor and resolve issues across States, Head Office, Vendors, Projects and Services with SLA monitoring, dashboards, reports and complete audit trails.
                </p>

                <div class="hero-buttons">

                    <a href="#features" class="btn-primary">
                        Get Started
                    </a>

                    <a href="{{ route('login') }}" class="btn-secondary">
                        Login to Dashboard
                    </a>

                </div>

                <div class="hero-highlights">
                    <div class="highlight-item">Secure & Reliable</div>
                    <div class="highlight-item">Real-time Tracking</div>
                    <div class="highlight-item">SLA Monitoring</div>
                    <div class="highlight-item">Analytics & Reports</div>
                </div>

            </div>

            <!-- Right -->

            <div class="hero-right">

                <img src="{{ $heroDashboard }}" alt="Dashboard">

            </div>

        </div>

    </section>


    <section class="features" id="features">

        <div class="container">

            <div class="section-title">

                <span>Powerful Features</span>

                <h2>Powerful Features</h2>

                <p>
                    Everything you need to manage issues efficiently.
                </p>

            </div>

            <div class="feature-grid">

                <div class="feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="4" y="6" width="16" height="12" rx="2" />
                            <path d="M8 10h4" />
                            <path d="M8 14h8" />
                        </svg>
                    </div>
                    <div class="feature-content">
                        <h4>Issue Management</h4>
                        <p>Create, assign, track and resolve issues across organizations.</p>
                    </div>
                </div>

                <div class="feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 7h6" />
                            <path d="M6 4l3 3-3 3" />
                            <path d="M14 17h6" />
                            <path d="M20 14l-3 3 3 3" />
                            <path d="M6 13h12" />
                        </svg>
                    </div>
                    <div class="feature-content">
                        <h4>Work Queue</h4>
                        <p>Role based queues for effective issue handling and prioritization.</p>
                    </div>
                </div>

                <div class="feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="8" />
                            <path d="M12 8v4l3 2" />
                        </svg>
                    </div>
                    <div class="feature-content">
                        <h4>SLA Monitoring</h4>
                        <p>Monitor SLA compliance, breaches and resolution timelines.</p>
                    </div>
                </div>

                <div class="feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="4" y="6" width="16" height="12" rx="2" />
                            <path d="M8 10h8" />
                            <path d="M8 14h4" />
                        </svg>
                    </div>
                    <div class="feature-content">
                        <h4>Dashboard & Analytics</h4>
                        <p>Real-time dashboards with insights and performance metrics.</p>
                    </div>
                </div>

                <div class="feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="4" y="6" width="16" height="12" rx="2" />
                            <path d="M8 10h4" />
                            <path d="M8 14h4" />
                        </svg>
                    </div>
                    <div class="feature-content">
                        <h4>Reports & MIS</h4>
                        <p>Comprehensive reports for issues, SLA, vendors, states and more.</p>
                    </div>
                </div>

                <div class="feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="7" r="3" />
                            <path d="M5 21c0-3 2.5-5 7-5s7 2 7 5" />
                        </svg>
                    </div>
                    <div class="feature-content">
                        <h4>User & Role Management</h4>
                        <p>Manage users, roles, privileges and organization mappings.</p>
                    </div>
                </div>

                <div class="feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="4" y="7" width="16" height="10" rx="2" />
                            <path d="M8 11h8" />
                        </svg>
                    </div>
                    <div class="feature-content">
                        <h4>State Administration</h4>
                        <p>State-wise user, project and service administration.</p>
                    </div>
                </div>

                <div class="feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14" />
                            <path d="M12 5v14" />
                            <path d="M6 19h12" />
                        </svg>
                    </div>
                    <div class="feature-content">
                        <h4>Vendor Management</h4>
                        <p>Vendor onboarding, mapping, performance and evaluations.</p>
                    </div>
                </div>

                <div class="feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 6h16" />
                            <path d="M4 12h16" />
                            <path d="M4 18h16" />
                        </svg>
                    </div>
                    <div class="feature-content">
                        <h4>Audit Logs</h4>
                        <p>Track all activities with detailed audit logs and change history.</p>
                    </div>
                </div>

                <div class="feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8a6 6 0 00-12 0v5a4 4 0 004 4h4a4 4 0 004-4V8" />
                            <path d="M12 19v3" />
                        </svg>
                    </div>
                    <div class="feature-content">
                        <h4>Notification Center</h4>
                        <p>Real-time email and in-app notifications and alerts.</p>
                    </div>
                </div>

            </div>

        </div>

    </section>


   


    <!-- ===================================================== -->
<!-- APPLICATION MODULES -->
<!-- ===================================================== -->

    <!-- <section class="modules" id="modules">

        <div class="container">

            <div class="section-title">

                <span>APPLICATION MODULES</span>

                <h2>Everything You Need In One Platform</h2>

                <p>
                    Enterprise Issue Tracker provides complete lifecycle management with dedicated modules for every business operation.
                </p>

            </div>

            <div class="module-grid">

                <div class="module-card">

                    <div class="module-icon">🎫</div>

                    <h3>Issue Management</h3>

                    <p>Create, assign, prioritize and track issues until closure.</p>

                </div>

                <div class="module-card">

                    <div class="module-icon">📋</div>

                    <h3>Change Request</h3>

                    <p>Manage enhancement requests with complete approval workflow.</p>

                </div>

                <div class="module-card">

                    <div class="module-icon">📊</div>

                    <h3>Dashboard</h3>

                    <p>Real-time dashboards with SLA, pending issues and KPIs.</p>

                </div>

                <div class="module-card">

                    <div class="module-icon">⚙️</div>

                    <h3>Masters</h3>

                    <p>Manage States, Vendors, Projects, Applications and Modules.</p>

                </div>

                <div class="module-card">

                    <div class="module-icon">👥</div>

                    <h3>User Management</h3>

                    <p>Users, Roles, Privileges and Organization Mapping.</p>

                </div>

                <div class="module-card">

                    <div class="module-icon">📈</div>

                    <h3>Reports</h3>

                    <p>Generate detailed operational and management reports.</p>

                </div>

                <div class="module-card">

                    <div class="module-icon">🔔</div>

                    <h3>Notifications</h3>

                    <p>Email alerts, reminders and escalation notifications.</p>

                </div>

                <div class="module-card">

                    <div class="module-icon">📝</div>

                    <h3>Audit Trail</h3>

                    <p>Complete activity history with user-wise audit logs.</p>

                </div>

            </div>

        </div>

    </section> -->


        <section class="stats">

        <div class="container">

            <div class="stats-grid">

                <div class="stat-card">

                    <div class="stat-icon">📍</div>

                    <h2>36+</h2>

                    <p>States</p>

                </div>

                <div class="stat-card">

                    <div class="stat-icon">🏥</div>

                    <h2>120+</h2>

                    <p>Vendors</p>

                </div>

                <div class="stat-card">

                    <div class="stat-icon">📁</div>

                    <h2>250+</h2>

                    <p>Projects</p>

                </div>

                <div class="stat-card">

                    <div class="stat-icon">🧩</div>

                    <h2>450+</h2>

                    <p>Applications</p>

                </div>

                <div class="stat-card">

                    <div class="stat-icon">👥</div>

                    <h2>1,250+</h2>

                    <p>Registered Users</p>

                </div>

                <div class="stat-card">

                    <div class="stat-icon">📊</div>

                    <h2>2,450</h2>

                    <p>Total Issues</p>

                </div>

                <div class="stat-card">

                    <div class="stat-icon">🟢</div>

                    <h2>680</h2>

                    <p>Open Issues</p>

                </div>

                <div class="stat-card">

                    <div class="stat-icon">✅</div>

                    <h2>92.6%</h2>

                    <p>SLA Compliance</p>

                </div>

            </div>

        </div>

    </section>


<!-- ===================================================== -->
<!-- FOOTER -->
<!-- ===================================================== -->

    <footer class="footer" id="contact">

        <div class="container">

            <div class="footer-grid">

                <div class="footer-column company-column">

                    <img src="{{ $logo }}" class="footer-logo" alt="Logo">

                    <h3>EMRI Issue Tracker</h3>

                    <p>Enterprise Issue Management System for efficient issue tracking, SLA compliance, and audit-ready reporting.</p>

                </div>

                <div class="footer-column">

                    <h4>Quick Links</h4>

                    <ul>
                        <li><a href="#hero">Home</a></li>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#modules">Modules</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>

                </div>

                <div class="footer-column">

                    <h4>Resources</h4>

                    <ul>
                        <li><a href="#">User Guide</a></li>
                        <li><a href="#">FAQs</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>

                </div>

                <div class="footer-column">

                    <h4>Support</h4>

                    <ul>
                        <li><a href="#">Help Desk</a></li>
                        <li><a href="#">Raise a Ticket</a></li>
                        <li><a href="#">System Status</a></li>
                    </ul>

                    <div class="footer-contact">
                        <span>✉ support@emri.in</span>
                        <span>☎ +91-XXXXXXXXXX</span>
                    </div>

                </div>

            </div>

            <hr>

            <div class="footer-bottom">

                <p>© 2025 EMRI Issue Tracker. All rights reserved.</p>

                <p>Enterprise Issue Tracker v1.0</p>

            </div>

        </div>

    </footer>
<!-- 
                <div class="footer-column">

                    <h4>Application</h4>

                    <ul>

                        <li>Issue Management</li>

                        <li>Change Requests</li>

                        <li>Dashboard</li>

                        <li>Reports</li>

                        <li>User Management</li>

                    </ul>

                </div> -->

                <!-- Contact -->
<!-- 
                <div class="footer-column">

                    <h4>Support</h4>

                    <p>Email : support@emri.in</p>

                    <p>Phone : +91-XXXXXXXXXX</p>

                    <a href="{{ route('login') }}" class="footer-login">

                        Login

                    </a>

                </div> -->

            </div>

            <hr>

            <div class="footer-bottom">

                <p>

                    © {{ date('Y') }} EMRI Green Health Services.
                    All Rights Reserved.

                </p>

                <p>

                    Enterprise Issue Tracker v1.0

                </p>

            </div>

        </div>

    </footer>

</body>

</html>
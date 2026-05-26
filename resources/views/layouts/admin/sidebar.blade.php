<aside class="app-sidebar sticky" id="sidebar">
    <div class="main-sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="header-logo">
            <img src="{{ asset('assets/images/brand-logos/desktop-logo.png') }}" alt="logo" class="desktop-logo">
            <img src="{{ asset('assets/images/brand-logos/toggle-logo.png') }}" alt="logo" class="toggle-logo">
            <img src="{{ asset('assets/images/brand-logos/desktop-dark.png') }}" alt="logo" class="desktop-dark">
            <img src="{{ asset('assets/images/brand-logos/toggle-dark.png') }}" alt="logo" class="toggle-dark">
            <img src="{{ asset('assets/images/brand-logos/desktop-white.png') }}" alt="logo" class="desktop-white">
            <img src="{{ asset('assets/images/brand-logos/toggle-white.png') }}" alt="logo" class="toggle-white">
        </a>
    </div>

    <div class="main-sidebar" id="sidebar-scroll">
        <nav class="main-menu-container nav nav-pills flex-column sub-open">
            <ul class="main-menu">
                <li class="slide__category"><span class="category-name">Overview</span></li>
                <li class="slide">
                    <a href="{{ route('admin.dashboard') }}" class="side-menu__item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <span class="side-menu__label">Dashboard</span>
                    </a>
                </li>
                <li class="slide">
                    <a href="{{ route('admin.calendar') }}" class="side-menu__item {{ request()->routeIs('admin.calendar') ? 'active' : '' }}">
                        <span class="side-menu__label">Calendar & Slots</span>
                    </a>
                </li>

                <li class="slide__category"><span class="category-name">Booking Flow</span></li>
                <li class="slide">
                    <a href="{{ route('admin.bookings.requests') }}" class="side-menu__item {{ request()->routeIs('admin.bookings.requests') ? 'active' : '' }}">
                        <span class="side-menu__label">Booking Requests</span>
                    </a>
                </li>
                <li class="slide">
                    <a href="{{ route('admin.bookings.active') }}" class="side-menu__item {{ request()->routeIs('admin.bookings.active') ? 'active' : '' }}">
                        <span class="side-menu__label">Bookings Active</span>
                    </a>
                </li>
                <li class="slide">
                    <a href="{{ route('admin.reschedules') }}" class="side-menu__item {{ request()->routeIs('admin.reschedules') ? 'active' : '' }}">
                        <span class="side-menu__label">Reschedule Requests</span>
                    </a>
                </li>
                <li class="slide">
                    <a href="{{ route('admin.cancellations') }}" class="side-menu__item {{ request()->routeIs('admin.cancellations') ? 'active' : '' }}">
                        <span class="side-menu__label">Cancellations</span>
                    </a>
                </li>
                <li class="slide">
                    <a href="{{ route('admin.force.majeure') }}" class="side-menu__item {{ request()->routeIs('admin.force.majeure') ? 'active' : '' }}">
                        <span class="side-menu__label">Force Majeure</span>
                    </a>
                </li>

                <li class="slide__category"><span class="category-name">Payments</span></li>
                <li class="slide">
                    <a href="{{ route('admin.payments.dp') }}" class="side-menu__item {{ request()->routeIs('admin.payments.dp') ? 'active' : '' }}">
                        <span class="side-menu__label">DP Verification</span>
                    </a>
                </li>
                <li class="slide">
                    <a href="{{ route('admin.payments.final') }}" class="side-menu__item {{ request()->routeIs('admin.payments.final') ? 'active' : '' }}">
                        <span class="side-menu__label">Final Payment</span>
                    </a>
                </li>
                <li class="slide">
                    <a href="{{ route('admin.pricing.reviews') }}" class="side-menu__item {{ request()->routeIs('admin.pricing.reviews') ? 'active' : '' }}">
                        <span class="side-menu__label">Pricing Review</span>
                    </a>
                </li>

                <li class="slide__category"><span class="category-name">Master Data</span></li>
                <li class="slide">
                    <a href="{{ route('admin.packages') }}" class="side-menu__item {{ request()->routeIs('admin.packages') ? 'active' : '' }}">
                        <span class="side-menu__label">Packages</span>
                    </a>
                </li>
                <li class="slide">
                    <a href="{{ route('admin.location.rules') }}" class="side-menu__item {{ request()->routeIs('admin.location.rules') ? 'active' : '' }}">
                        <span class="side-menu__label">Location Rules</span>
                    </a>
                </li>
                <li class="slide">
                    <a href="{{ route('admin.customers') }}" class="side-menu__item {{ request()->routeIs('admin.customers') ? 'active' : '' }}">
                        <span class="side-menu__label">Customers</span>
                    </a>
                </li>
                <li class="slide">
                    <a href="{{ route('admin.settings') }}" class="side-menu__item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                        <span class="side-menu__label">Settings</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

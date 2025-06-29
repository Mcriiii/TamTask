<div class="sidebar">
    @php
    $prefix = Auth::user()->role === 'admin' ? 'admin.' : '';
    @endphp
    <div class="sidebar-logo">
        <img src="{{ asset('images/toplogo.png') }}" alt="" class="logo-nav">
    </div>

    {{-- Dashboard --}}
    <div class="sidebar-links">
        <a href="{{ route($prefix . 'dashboard') }}" class="tab"><i class="fas fa-chart-bar"></i> Dashboard </a>
        <div class="dropdown-tab">
            <a href="#" class="tab" onclick="toggleDropdown('reportSubmenu')">
                <i class="fas fa-chart-line"></i> Report
                <i class="fas fa-caret-down" style="margin-left:auto;"></i>
            </a>
            <div id="reportSubmenu" class="submenu" style="display: none;">
                <a href="{{ route($prefix .'incidents.index') }}" class="tab"><i class="fas fa-file-alt"></i> Incident Report</a>
                <a href="{{ route($prefix .'violations.index') }}" class="tab"><i class="fas fa-exclamation-triangle"></i> Violation Report</a>
            </div>
        </div>
        {{-- Lost & Found --}}
        <a href="{{ route($prefix . 'lost-found.index') }}" class="tab"><i class="fas fa-search"></i> Lost & Found </a>

        <a href="{{ route($prefix . 'certificates.index') }}" class="tab"><i class="fas fa-certificate"></i> Certificate</a>
        <a href="{{ route($prefix . 'complaints.index') }}" class="tab"><i class="fas fa-exclamation-circle"></i> Complaint</a>
        <a href="{{ route($prefix . 'referrals.index') }}" class="tab"><i class="fas fa-share-square"></i> Referral</a>

        {{-- Admin Only --}}
        @if(Auth::user()->role === 'admin')
        <a href="{{ route('admin.accounts') }}" class="tab">
            <i class="fas fa-user"></i> Manage Accounts
        </a>
        <a href="{{ route('admin.activity.logs') }}" class="tab">
            <i class="fas fa-clipboard-list"></i> Activity Logs
        </a>
        @endif

        {{-- Logout --}}
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
        <a href="#" class="tab logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>
<script>
    function toggleDropdown(id) {
        var submenu = document.getElementById(id);
        submenu.style.display = submenu.style.display === "none" ? "block" : "none";
    }
</script>
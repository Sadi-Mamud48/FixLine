<?php
// Expects $activePage to be set by the including view (dashboard|profile|apply_job|earnings)
$activePage = $activePage ?? '';
?>
<div class="fx-navbar">
    <a href="/FixLine/index.php?action=provider_dashboard" class="fx-brand">
        <img src="View/images/logo.png" alt="FixLine logo" onerror="this.style.display='none'">
        <span>Home</span>
    </a>

    <div class="fx-search-bar">
        <span>&#128269;</span>
        <input type="text" placeholder="Search">
    </div>

    <div class="fx-nav-icons">
        <details class="fx-settings-menu">
            <summary>
                <img src="View/images/csettings.png" alt="Settings" title="Settings">
            </summary>
            <div class="fx-settings-dropdown">
                <a href="/FixLine/index.php?action=logout">Log out</a>
            </div>
        </details>
    </div>
</div>

<div class="fx-tab-row">
    <a href="/FixLine/index.php?action=provider_dashboard" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
    <a href="/FixLine/index.php?action=provider_profile" class="<?= $activePage === 'profile' ? 'active' : '' ?>">Profile</a>
    <a href="/FixLine/index.php?action=provider_requests" class="<?= $activePage === 'requests' ? 'active' : '' ?>">Requests</a>
    <a href="/FixLine/index.php?action=provider_apply_job" class="<?= $activePage === 'apply_job' ? 'active' : '' ?>">Apply for Job</a>
    <a href="/FixLine/index.php?action=provider_earnings" class="<?= $activePage === 'earnings' ? 'active' : '' ?>">Earnings</a>
</div>

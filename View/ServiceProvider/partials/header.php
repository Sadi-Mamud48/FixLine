<?php
// Expects $activePage to be set by the including view (dashboard|profile|apply_job|earnings)
$activePage = $activePage ?? '';
?>
<div class="fx-navbar">
    <a href="service_provider.php?action=dashboard" class="fx-brand">
        <img src="View/images/logo.png" alt="FixLine logo" onerror="this.style.display='none'">
        <span>Home</span>
    </a>

    <div class="fx-search-bar">
        <span>&#128269;</span>
        <input type="text" placeholder="Search">
    </div>

    <div class="fx-nav-icons">
        <span title="Notifications">&#128276;</span>
        <span title="Messages">&#128172;</span>
        <span title="Settings">&#9881;</span>
    </div>
</div>

<div class="fx-tab-row">
    <a href="service_provider.php?action=dashboard" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
    <a href="service_provider.php?action=profile" class="<?= $activePage === 'profile' ? 'active' : '' ?>">Profile</a>
    <a href="service_provider.php?action=requests" class="<?= $activePage === 'requests' ? 'active' : '' ?>">Requests</a>
    <a href="service_provider.php?action=apply_job" class="<?= $activePage === 'apply_job' ? 'active' : '' ?>">Apply for Job</a>
    <a href="service_provider.php?action=earnings" class="<?= $activePage === 'earnings' ? 'active' : '' ?>">Earnings</a>
</div>

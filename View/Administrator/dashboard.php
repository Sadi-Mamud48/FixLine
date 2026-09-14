<?php 
$pageTitle = 'Admin Deshboard'; 
$showBackBtn = false; 
require_once __DIR__ . '/header.php'; 
?>  

<div class="main-content">     
    <div class="bg-illustration"></div>      
    <div class="actions-sidebar">         
        <h2 style="color: #ffffff; margin-bottom: 4px;">Admin Overview</h2>
        <div class="stats-grid" style="grid-template-columns: repeat(2, minmax(0, 1fr)); margin: 0 0 12px;">
            <div class="stat-box"><div>Total Users</div><div class="stat-num"><?php echo (int) ($dashboard['total_users'] ?? 0); ?></div></div>
            <div class="stat-box"><div>Providers</div><div class="stat-num"><?php echo (int) ($dashboard['providers'] ?? 0); ?></div></div>
            <div class="stat-box"><div>Bookings</div><div class="stat-num"><?php echo (int) ($dashboard['bookings'] ?? 0); ?></div></div>
            <div class="stat-box"><div>Pending Approval</div><div class="stat-num"><?php echo (int) ($dashboard['pending_providers'] ?? 0); ?></div></div>
        </div>
        <a href="index.php?action=analytics" class="nav-pill-btn">             
            <div class="avatar-circle">                 
                <img src="View/images/analytics.png" alt="Analytics">
            </div>             
            <div class="pill-textbox">                 
                Analytics             
            </div>         
        </a>          

        <a href="index.php?action=users_info" class="nav-pill-btn">             
            <div class="avatar-circle">                 
                <img src="View/images/userinfo.png" alt="Users Info">             
            </div>             
            <div class="pill-textbox">                 
                Users Information             
            </div>         
        </a>          

        <a href="index.php?action=account_management" class="nav-pill-btn">             
            <div class="avatar-circle">                 
                <img src="View/images/accountmanagement.png" alt="Account Management">             
            </div>             
            <div class="pill-textbox">                 
                Account Management             
            </div>         
        </a>     
    </div> 
</div>  

<?php require_once __DIR__ . '/footer.php'; ?>

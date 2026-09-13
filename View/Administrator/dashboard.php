<?php 
$pageTitle = 'Admin Deshboard'; 
$showBackBtn = false; 
require_once __DIR__ . '/header.php'; 
?>  

<div class="main-content">     
    <div class="bg-illustration"></div>      
    <div class="actions-sidebar">         
        <!-- Analytics Button -->         
        <a href="index.php?action=analytics" class="nav-pill-btn">             
            <div class="avatar-circle">                 
                <img src="View/images/analytics.png" alt="Analytics">
            </div>             
            <div class="pill-textbox">                 
                Analytics             
            </div>         
        </a>          

        <!-- Users Information Button -->         
        <a href="index.php?action=users_info" class="nav-pill-btn">             
            <div class="avatar-circle">                 
                <img src="View/images/userinfo.png" alt="Users Info">             
            </div>             
            <div class="pill-textbox">                 
                Users Information             
            </div>         
        </a>          

        <!-- Account Management Button -->         
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
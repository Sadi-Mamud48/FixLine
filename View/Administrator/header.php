<!DOCTYPE html> 
<html lang="en"> 
<head>     
    <meta charset="UTF-8">     
    <meta name="viewport" content="width=device-width, initial-scale=1.0">     
    <title>FixLine - Admin Dashboard</title>     
    <link rel="stylesheet" href="View/Administrator/style.css">     
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
</head> 
<body>     
    <div class="window-container">         
        <div class="nav-header">             
            <div class="header-left">                 
                <?php if (isset($showBackBtn) && $showBackBtn): ?>                     
                    <a href="<?php echo $backUrl ?? 'index.php?action=dashboard'; ?>" class="back-btn" title="Back"><i class="fa-solid fa-arrow-left"></i></a>                 
                <?php else: ?>                     
                    <span style="font-size: 24px; color:#fff;"><i class="fa-solid fa-wrench"></i></span>                 
                <?php endif; ?>                 
                <span class="brand-title"><?php echo $pageTitle ?? 'Admin Deshboard'; ?></span>             
            </div>              

            <div class="search-bar-container">                 
                <form action="index.php" method="GET" style="display: flex; width:100%;">                     
                    <input type="hidden" name="action" value="account_management">                     
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>                     
                    <input type="text" name="search" class="search-input" placeholder="Search users by name, role, email..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">                 
                </form>             
            </div>              

            <div class="header-right-icons">                 
                <button class="icon-btn" onclick="alert('No new notifications!')" title="Notifications"><i class="fa-regular fa-bell"></i></button>                 
                <a href="index.php?action=settings" class="icon-btn" title="Settings"><i class="fa-solid fa-gear"></i></a>             
            </div>         
        </div>
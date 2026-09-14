<?php 
$pageTitle = 'Home'; 
$showBackBtn = true;  

// Define back navigation depending on whether a category is currently selected 
if (isset($category)) {     
    $backUrl = 'index.php?action=account_management'; 
} else {     
    $backUrl = 'index.php?action=dashboard'; 
}  

require_once __DIR__ . '/header.php'; 
?>  

<div class="main-content">     
    <div class="bg-illustration"></div>      

    <div class="actions-sidebar" style="width: 420px;">         
        <h2 style="color: #ffffff; margin-bottom: 8px; font-size: 24px;">Account Management</h2>          

        <?php if ($search !== ''): ?>             
            <!-- SEARCH RESULT STATE -->             
            <p style="color: #ffdd59; font-size: 14px; margin-bottom: 12px; font-weight: bold;">                 
                Search results for: "<?php echo htmlspecialchars($search); ?>"             
            </p>              

            <?php              
            $foundCount = 0;             
            foreach ($allUsers as $key => $u):                 
                if (strpos(strtolower($u['name']), $search) !== false || strpos(strtolower($u['role']), $search) !== false || strpos(strtolower($u['email']), $search) !== false):                     
                    $foundCount++;             
            ?>                 
                <a href="index.php?action=profile_detail&id=<?php echo urlencode($key); ?>" class="nav-pill-btn">                     
                    <div class="avatar-circle">                         
                        <img src="View/<?php echo htmlspecialchars($u['photo']); ?>" alt="<?php echo htmlspecialchars($u['name']); ?>">                     
                    </div>                     
                    <div class="pill-textbox" style="text-align: left; padding: 10px 18px;">                         
                        <div style="font-weight: bold; font-size: 16px;"><?php echo htmlspecialchars($u['name']); ?></div>                         
                        <div style="font-size: 12px; color: #ffdd59;"><?php echo htmlspecialchars($u['role']); ?></div>                     
                    </div>                 
                </a>             
            <?php                  
                endif;             
            endforeach;               

            if ($foundCount === 0):             
            ?>                 
                <p style="color: #ffffff; background: rgba(0,0,0,0.3); padding: 15px; border-radius: 8px;">                     
                    No user found matching your search.                 
                </p>                 
                <a href="index.php?action=account_management" class="btn-purple" style="display:inline-block; text-align:center;">                     
                    Clear Search                 
                </a>             
            <?php endif; ?>          

        <?php elseif ($category === null): ?>             
            <!-- STEP 1: CATEGORY SELECTION (Service Provider, Moderator, Customer) -->             
            <p style="color: #e0d8ff; font-size: 14px; margin-bottom: 15px;">                 
                Select a user category to manage accounts:             
            </p>              

            <!-- Option 1: Service Provider -->             
            <a href="index.php?action=account_management&category=provider" class="nav-pill-btn">                 
                <div class="avatar-circle">                     
                    <img src="View/images/electrician.png" alt="Service Provider">                 
                </div>                 
                <div class="pill-textbox">                     
                    Service Provider                 
                </div>             
            </a>              

            <!-- Option 2: Moderator -->             
            <a href="index.php?action=account_management&category=moderator" class="nav-pill-btn">                 
                <div class="avatar-circle">                     
                    <img src="View/images/afnan.png" alt="Moderator">                 
                </div>                 
                <div class="pill-textbox">                     
                    Moderator                 
                </div>             
            </a>              

            <!-- Option 3: Customer / User -->             
            <a href="index.php?action=account_management&category=customer" class="nav-pill-btn">                 
                <div class="avatar-circle">                     
                    <img src="View/images/userinfo.png" alt="Customer">                 
                </div>                 
                <div class="pill-textbox">                     
                    User / Customer                 
                </div>             
            </a>          

        <?php else: ?>             
            <!-- STEP 2: USER LIST UNDER SELECTED CATEGORY -->             
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">                 
                <span style="color: #ffdd59; font-size: 16px; font-weight: bold;">                     
                    Category: <?php echo htmlspecialchars(ucfirst($category)); ?>                 
                </span>                 
                <a href="index.php?action=account_management" style="color: #ffffff; font-size: 12px; text-decoration: underline;">                     
                    Change Category                 
                </a>             
            </div>              

            <?php              
            $catUsersCount = 0;             
            foreach ($allUsers as $key => $u):                 
                if (strtolower((string) $u['role']) === $category):                     
                    $catUsersCount++;             
            ?>                 
                <a href="index.php?action=profile_detail&id=<?php echo urlencode($key); ?>" class="nav-pill-btn">                     
                    <div class="avatar-circle">                         
                        <img src="View/<?php echo htmlspecialchars($u['photo']); ?>" alt="<?php echo htmlspecialchars($u['name']); ?>">                     
                    </div>                     
                    <div class="pill-textbox" style="text-align: left; padding: 10px 18px;">                         
                        <div style="font-weight: bold; font-size: 16px;"><?php echo htmlspecialchars($u['name']); ?></div>                         
                        <div style="font-size: 12px; color: #ffdd59;">                             
                            <?php echo htmlspecialchars($u['profession'] ?: ($u['role_label'] ?? $u['role'])); ?>
                            <?php if ($u['role'] === 'provider' && !empty($u['provider_status'])): ?>
                                <span style="margin-left: 6px;">(<?php echo htmlspecialchars($u['provider_status']); ?>)</span>
                            <?php endif; ?>
                        </div>                     
                    </div>                 
                </a>             
                <?php if ($u['role'] === 'provider' && !empty($u['provider_id']) && ($u['provider_status'] ?? '') === 'pending'): ?>
                    <div style="margin: -18px 0 8px 90px; display: flex; gap: 8px;">
                        <form method="POST" action="index.php?action=update_provider_status">
                            <input type="hidden" name="provider_id" value="<?php echo (int) $u['provider_id']; ?>">
                            <button class="btn-purple" type="submit" name="status" value="approved" style="padding: 6px 10px; font-size: 12px;">Approve</button>
                        </form>
                        <form method="POST" action="index.php?action=update_provider_status">
                            <input type="hidden" name="provider_id" value="<?php echo (int) $u['provider_id']; ?>">
                            <button class="btn-purple" type="submit" name="status" value="rejected" style="padding: 6px 10px; font-size: 12px; background: #8e2d2d;">Reject</button>
                        </form>
                    </div>
                <?php endif; ?>
            <?php                  
                endif;             
            endforeach;               

            if ($catUsersCount === 0):             
            ?>                 
                <p style="color: #ffffff; background: rgba(0,0,0,0.3); padding: 15px; border-radius: 8px;">                     
                    No accounts found under this category.                 
                </p>             
            <?php endif; ?>          
        <?php endif; ?>     
    </div> 
</div>  

<?php require_once __DIR__ . '/footer.php'; ?>
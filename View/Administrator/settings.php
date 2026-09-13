<?php 
$pageTitle = 'System Settings'; 
$showBackBtn = true; 
$backUrl = 'index.php?action=dashboard'; 
require_once __DIR__ . '/header.php'; 
?>  

<div class="main-content">     
    <div class="glass-table-card" style="max-width: 550px; margin: 0 auto;">         
        <h2 style="font-size: 24px; border-bottom: 2px solid #8c70f8; padding-bottom: 10px; margin-bottom: 20px; color: #ffffff;">             
            <i class="fa-solid fa-gear"></i> Account Settings         
        </h2>          

        <?php if (isset($_GET['msg'])): ?>             
            <div style="background: #2ecc71; color: #fff; padding: 10px 15px; border-radius: 6px; font-size: 14px; margin-bottom: 20px; font-weight: bold; text-align: center;">                 
                <?php                      
                    if ($_GET['msg'] === 'email_updated') echo 'Email address updated successfully!';                     
                    elseif ($_GET['msg'] === 'pass_updated') echo 'Password changed successfully!';                 
                ?>             
            </div>         
        <?php endif; ?>          

        <!-- 1. Change Email Section -->         
        <form action="index.php?action=update_settings_email" method="POST" style="margin-bottom: 22px; background: rgba(0,0,0,0.22); padding: 20px; border-radius: 10px; border: 1px solid #785be8;">             
            <h3 style="color: #ffdd59; font-size: 16px; margin-bottom: 15px;">                 
                <i class="fa-regular fa-envelope"></i> Change Email Address             
            </h3>                          

            <div class="form-group">                 
                <label>Current Email</label>                 
                <input type="email" class="form-control" value="<?php echo htmlspecialchars($_SESSION['logged_user'] ?? 'admin@fixline.com'); ?>" disabled style="background: rgba(255,255,255,0.2); color: #fff;">             
            </div>              

            <div class="form-group">                 
                <label>New Email Address</label>                 
                <input type="email" name="new_email" class="form-control" placeholder="Enter new email address" required>             
            </div>              

            <button type="submit" class="btn-purple" style="padding: 9px 22px; font-size: 14px; margin-top: 5px;">                 
                Update Email             
            </button>         
        </form>          

        <!-- 2. Change Password Section -->         
        <form action="index.php?action=update_settings_pass" method="POST" style="margin-bottom: 25px; background: rgba(0,0,0,0.22); padding: 20px; border-radius: 10px; border: 1px solid #785be8;">             
            <h3 style="color: #ffdd59; font-size: 16px; margin-bottom: 15px;">                 
                <i class="fa-solid fa-key"></i> Change Password             
            </h3>              

            <div class="form-group">                 
                <label>Current Password</label>                 
                <input type="password" name="current_password" class="form-control" placeholder="••••••••" required>             
            </div>              

            <div class="form-group">                 
                <label>New Password</label>                 
                <input type="password" name="new_password" class="form-control" placeholder="••••••••" required>             
            </div>              

            <button type="submit" class="btn-purple" style="padding: 9px 22px; font-size: 14px; margin-top: 5px;">                 
                Change Password             
            </button>         
        </form>          

        <!-- 3. Logout Button -->         
        <div style="text-align: center; border-top: 1px solid rgba(255,255,255,0.15); padding-top: 20px;">             
            <a href="index.php?action=logout" class="btn-purple" style="background: #e74c3c; padding: 12px 35px; font-size: 15px; display: inline-block; text-decoration: none;">                 
                <i class="fa-solid fa-right-from-bracket"></i> Logout             
            </a>         
        </div>     
    </div> 
</div>  

<?php require_once __DIR__ . '/footer.php'; ?>
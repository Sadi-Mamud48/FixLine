<?php 
$pageTitle = ''; 
$showBackBtn = true; 
$backUrl = 'index.php?action=account_management'; 
require_once __DIR__ . '/header.php'; 
?>  

<div class="main-content">     
    <div class="bg-illustration"></div>      

    <div class="profile-card-overlay">         
        <div class="profile-card-header">             
            <h2 style="font-size: 24px;">Account Management</h2>             
            <?php if (isset($_GET['msg'])): ?>                 
                <p style="color: #2ecc71; font-weight: bold; margin-top: 5px; font-size: 14px;">                     
                    <?php                          
                        if ($_GET['msg'] === 'saved') echo 'Profile & details updated successfully!';                         
                        else echo 'Account status changed to: ' . htmlspecialchars($_GET['msg']);                     
                    ?>                 
                </p>             
            <?php endif; ?>         
        </div>          

        <form action="index.php?action=save_profile" method="POST">             
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">                          

            <div class="profile-body-grid">                 
                <div>                     
                    <div class="photo-placeholder">                         
                        <img src="View/<?php echo htmlspecialchars($user['photo']); ?>" alt="Photo">                     
                    </div>                     
                    <p style="text-align: center; margin-top: 8px; font-weight: bold; font-size: 13px;">Photo</p>                 
                </div>                  

                <div>                     
                    <!-- Account Name Field -->                     
                    <div class="form-group">                         
                        <label>Account Name</label>                         
                        <input type="text" name="account_name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>                     
                    </div>                      

                    <!-- Email Address Field -->                     
                    <div class="form-group">                         
                        <label>Email Address</label>                         
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>                     
                    </div>                      

                    <!-- Role Selection Field -->                     
                    <div class="form-group">                         
                        <label>Role</label>                         
                        <select name="role" class="form-control">                             
                            <option value="Customer" <?php if ($user['role'] === 'Customer') echo 'selected'; ?>>Customer</option>                             
                            <option value="Service Provider" <?php if ($user['role'] === 'Service Provider') echo 'selected'; ?>>Service Provider</option>                             
                            <option value="Moderator" <?php if ($user['role'] === 'Moderator') echo 'selected'; ?>>Moderator</option>                             
                            <option value="Admin" <?php if ($user['role'] === 'Admin') echo 'selected'; ?>>Admin</option>                         
                        </select>                     
                    </div>                      

                    <!-- Phone Number Field -->                     
                    <div class="form-group">                         
                        <label>Phone Number</label>                         
                        <input type="text" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" required>                     
                    </div>                      

                    <!-- Account Status Display -->                     
                    <p style="font-size: 13px; margin-top: 5px;">                         
                        Status:                          
                        <span class="status-badge <?php echo ($user['status'] === 'Active') ? 'status-active' : 'status-blocked'; ?>">                             
                            <?php echo htmlspecialchars($user['status']); ?>                         
                        </span>                     
                    </p>                 
                </div>             
            </div>              

            <!-- Action Buttons -->             
            <div class="button-row">                 
                <button type="submit" class="btn-purple">Save</button>                 
                <button type="button" class="btn-purple" onclick="alert('Password reset instructions sent to <?php echo htmlspecialchars($user['email']); ?>!')">Reset Password</button>                 
                <a href="index.php?action=toggle_block&id=<?php echo urlencode($user['id']); ?>" class="btn-purple">                     
                    <?php echo ($user['status'] === 'Active') ? 'Block Account' : 'Unblock Account'; ?>                 
                </a>             
            </div>         
        </form>     
    </div> 
</div>  

<?php require_once __DIR__ . '/footer.php'; ?>
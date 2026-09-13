<?php 
$pageTitle = 'Users Information'; 
$showBackBtn = true; 
$backUrl = 'index.php?action=dashboard'; 
require_once __DIR__ . '/header.php';  

$search = strtolower($_GET['search'] ?? ''); 
?>  

<div class="main-content">     
    <div class="glass-table-card">         
        <h2>Registered System Users</h2>         
        <p style="margin-bottom: 15px; color: #ccc;">View and filter registered accounts across all roles.</p>          

        <table class="custom-table">             
            <thead>                 
                <tr>                     
                    <th>User</th>                     
                    <th>Role</th>                     
                    <th>Email</th>                     
                    <th>Phone</th>                     
                    <th>Status</th>                     
                    <th>Action</th>                 
                </tr>             
            </thead>             
            <tbody>                 
                <?php                  
                foreach ($users as $key => $u):                      
                    if ($search && strpos(strtolower($u['name']), $search) === false && strpos(strtolower($u['role']), $search) === false) {                         
                        continue;                     
                    }                 
                ?>                     
                    <tr>                         
                        <td><strong><?php echo htmlspecialchars($u['name']); ?></strong></td>                         
                        <td><?php echo htmlspecialchars($u['role']); ?></td>                         
                        <td><?php echo htmlspecialchars($u['email']); ?></td>                         
                        <td><?php echo htmlspecialchars($u['phone']); ?></td>                         
                        <td>                             
                            <span class="status-badge <?php echo ($u['status'] === 'Active') ? 'status-active' : 'status-blocked'; ?>">                                 
                                <?php echo htmlspecialchars($u['status']); ?>                             
                            </span>                         
                        </td>                         
                        <td>                             
                            <a href="index.php?action=profile_detail&id=<?php echo urlencode($key); ?>" class="btn-purple" style="padding: 4px 10px; font-size: 12px;">Edit</a>                         
                        </td>                     
                    </tr>                 
                <?php endforeach; ?>             
            </tbody>         
        </table>     
    </div> 
</div>  

<?php require_once __DIR__ . '/footer.php'; ?>
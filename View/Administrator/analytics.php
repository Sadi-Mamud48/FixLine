<?php 
$pageTitle = 'Platform Analytics'; 
$showBackBtn = true; 
$backUrl = 'index.php?action=dashboard'; 
require_once __DIR__ . '/header.php'; 
?>  

<div class="main-content">     
    <div class="glass-table-card">         
        <h2>FixLine Platform Overview</h2>         
        <p style="margin-bottom: 15px; color: #ccc;">Real-time statistics based on proposal specifications.</p>          

        <div class="stats-grid">             
            <div class="stat-box">                 
                <div>Total Bookings</div>                 
                <div class="stat-num"><?php echo $analytics['total_bookings']; ?></div>             
            </div>             
            <div class="stat-box">                 
                <div>Active Users</div>                 
                <div class="stat-num"><?php echo $analytics['active_users']; ?></div>             
            </div>             
            <div class="stat-box">                 
                <div>Total Revenue</div>                 
                <div class="stat-num"><?php echo $analytics['total_revenue']; ?></div>             
            </div>         
        </div>          

        <h3 style="margin-top: 20px; color: #ffdd59;">Top Rated Service Providers</h3>         
        <table class="custom-table">             
            <thead>                 
                <tr>                     
                    <th>Provider Name</th>                     
                    <th>Service Category</th>                     
                    <th>Rating</th>                 
                </tr>             
            </thead>             
            <tbody>                 
                <?php foreach ($analytics['top_providers'] as $tp): ?>                     
                    <tr>                         
                        <td><?php echo htmlspecialchars($tp['name']); ?></td>                         
                        <td><?php echo htmlspecialchars($tp['service']); ?></td>                         
                        <td><span style="color: #ffdd59; font-weight: bold;"><?php echo htmlspecialchars($tp['rating']); ?></span></td>                     
                    </tr>                 
                <?php endforeach; ?>             
            </tbody>         
        </table>     
    </div> 
</div>  

<?php require_once __DIR__ . '/footer.php'; ?>
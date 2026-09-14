<?php
// FixLine/View/Moderator/index.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Moderators Dashboard - FixLine</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', sans-serif; }
    
    /* Background Image Implementation */
   body { 
      background-image: url('c:\Users\Acer\Downloads\Backgroundpic2 2.jpg.jpeg'); /* Adjust path or use an absolute URL */
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      background-attachment: fixed;
      width: 100vw; 
      min-height: 100vh; 
      overflow-x: hidden;
    }
    
    /* Fullscreen Wrapper */
    .dashboard-wrapper { 
      width: 100%; 
      min-height: 100vh; 
    }
    
    .dashboard-shadow { display: none; }
    
    /* Transparent Container to Reveal Body Background */
    .dashboard-card { 
      position: relative; 
      z-index: 2; 
      background-color: transparent; 
      border-radius: 0; 
      width: 100%; 
      min-height: 100vh; 
      display: flex; 
      flex-direction: column; 
    }
    
    /* Translucent Overlay for Readable Content */
    .dashboard-main { 
      background: linear-gradient(135deg, rgba(74, 57, 162, 0.88) 0%, rgba(136, 107, 234, 0.88) 100%); 
      padding: 30px 60px 50px; 
      flex-grow: 1; 
      position: relative; 
    }
    
    .header { display: flex; align-items: center; justify-content: space-between; color: white; margin-bottom: 40px; }
    .brand-title { display: flex; align-items: center; gap: 12px; font-size: 22px; font-weight: 600; }
    
    .search-bar { position: relative; width: 40%; max-width: 500px; }
    .search-bar input {
      width: 100%; 
      padding: 12px 18px 12px 45px;
      border-radius: 25px; 
      border: none; 
      outline: none; 
      font-size: 15px; 
    }
    .search-bar i { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: #4a5568; }
    
    .header-icons { display: flex; align-items: center; gap: 18px; font-size: 22px; cursor: pointer; }
    .logout-btn { color: #ffffff; background: rgba(255, 255, 255, 0.16); border: 1px solid rgba(255, 255, 255, 0.7); border-radius: 6px; padding: 9px 14px; font-size: 14px; text-decoration: none; }
    .logout-btn:hover { background: rgba(255, 255, 255, 0.3); }
    
    .sidebar-menu { display: flex; flex-direction: column; gap: 20px; z-index: 5; position: relative; max-width: 350px; }
    .menu-item { display: flex; align-items: center; text-decoration: none; gap: 15px; }
    
    .icon-circle { width: 60px; height: 60px; background-color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid #b794f4; color: #553c9a; font-size: 22px; flex-shrink: 0; overflow: hidden; }
    .icon-circle img { width: 100%; height: 100%; object-fit: cover; }
    .menu-btn { border: 1.5px solid #ffffff; background: rgba(255, 255, 255, 0.15); color: white; padding: 12px 24px; border-radius: 6px; font-size: 16px; font-weight: 500; width: 100%; text-align: left; cursor: pointer; transition: 0.3s; }
    .menu-item:hover .menu-btn { background: rgba(255, 255, 255, 0.3); }

    .illustration-bg { position: absolute; right: 40px; bottom: 0; width: 55%; height: 85%; background: url('/FixLine/View/images/Backgroundpic2%202.jpg.jpeg') no-repeat right bottom; background-size: contain; opacity: 0.85; pointer-events: none; }
    
    /* Footer Styling */
    .footer { background-color: rgba(255, 255, 255, 0.95); padding: 30px 60px; display: flex; justify-content: space-between; align-items: flex-start; color: #333; border-top: 1px solid #e2e8f0; }
    .footer-logo { display: flex; flex-direction: column; align-items: center; gap: 4px; }
    .footer-logo i { font-size: 32px; color: #333; }
    .footer-logo span { font-size: 22px; font-weight: bold; }
    
    .footer-column { display: flex; flex-direction: column; gap: 6px; font-size: 14px; }
    .footer-column h4 { font-size: 14px; color: #1a202c; margin-bottom: 2px; }
    .footer-column a { text-decoration: none; color: #4a5568; }
    .footer-column a:hover { color: #553c9a; }
    
    .social-icons { display: flex; gap: 12px; font-size: 20px; margin-top: 4px; }
    .social-icons .fa-facebook { color: #1877f2; }
    .social-icons .fa-instagram { color: #e4405f; }
    .social-icons .fa-twitter { color: #1da1f2; }
    .social-icons .fa-youtube { color: #ff0000; }
  </style>
</head>
<body>

<div class="dashboard-wrapper">
  <div class="dashboard-card">
    
    <div class="dashboard-main">
      <header class="header">
        <div class="brand-title">
          <i class="fa-solid fa-screwdriver-wrench"></i>
          <span>Moderators Dashboard</span>
        </div>

        <div class="search-bar">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" placeholder="Search">
        </div>

        <div class="header-icons">
          <i class="fa-regular fa-bell"></i>
          <i class="fa-regular fa-comment-dots"></i>
          <i class="fa-solid fa-gear"></i>
          <a href="/FixLine/index.php?action=logout" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
      </header>

      <div class="sidebar-menu">
        <a href="/FixLine/index.php?action=moderator_customers" class="menu-item">
          <div class="icon-circle"><img src="/FixLine/View/images/services/Customers.png" alt="Customers"></div>
          <button class="menu-btn">Customers</button>
        </a>

        <a href="/FixLine/index.php?action=moderator_providers" class="menu-item">
          <div class="icon-circle"><img src="/FixLine/View/images/services/Provider.png" alt="Providers"></div>
          <button class="menu-btn">Providers</button>
        </a>

        <a href="/FixLine/index.php?action=moderator_complaints" class="menu-item">
          <div class="icon-circle"><img src="/FixLine/View/images/services/complaint.png" alt="Complaints"></div>
          <button class="menu-btn">Complaints</button>
        </a>

        <a href="/FixLine/index.php?action=moderator_account_management" class="menu-item">
          <div class="icon-circle"><img src="/FixLine/View/images/services/Account%20management.png" alt="Account Management"></div>
          <button class="menu-btn">Account Management</button>
        </a>
      </div>

      <div class="illustration-bg"></div>
    </div>

    <footer class="footer">
      <div class="footer-logo">
        <i class="fa-solid fa-screwdriver-wrench"></i>
        <span>FixLine</span>
      </div>

      <div class="footer-column">
        <h4>Created by :</h4>
        <span>FixLine Team</span>
      </div>

      <div class="footer-column">
        <a href="#">Support</a>
        <a href="#">FAQs</a>
      </div>

      <div class="footer-column">
        <a href="#">About us</a>
        <a href="#">Contact us</a>
        <a href="#">Our license</a>
      </div>

      <div class="footer-column">
        <h4>Social Media</h4>
        <div class="social-icons">
          <i class="fa-brands fa-facebook"></i>
          <i class="fa-brands fa-instagram"></i>
          <i class="fa-brands fa-twitter"></i>
          <i class="fa-brands fa-youtube"></i>
        </div>
      </div>
    </footer>

  </div>
</div>

</body>
</html>
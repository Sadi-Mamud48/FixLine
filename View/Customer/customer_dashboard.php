<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FixLine - Customer Dashboard</title>
 
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #5b3dcc;
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

       
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 20px;
            background-color: transparent;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 18px;
            font-weight: 500;
        }

        .header-left i {
            font-size: 24px;
        }

        .search-container {
            flex: 0 1 450px;
            position: relative;
        }

        .search-container input {
            width: 100%;
            padding: 10px 20px 10px 40px;
            border-radius: 25px;
            border: none;
            outline: none;
            font-size: 16px;
            background: #ffffff;
            color: #333;
        }

        .search-container i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 16px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .bookings-link {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            white-space: nowrap;
        }

        .bookings-link:hover {
            text-decoration: underline;
        }

        .refund-link {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            white-space: nowrap;
        }

        .refund-link:hover {
            text-decoration: underline;
        }

        .header-right i {
            font-size: 20px;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .header-right img {
            width: 26px;
            height: 26px;
            object-fit: contain;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .header-right i:hover,
        .header-right img:hover {
            opacity: 0.8;
        }

        .settings-menu {
            position: relative;
        }

        .settings-menu summary {
            list-style: none;
            cursor: pointer;
        }

        .settings-menu summary::-webkit-details-marker {
            display: none;
        }

        .settings-dropdown {
            position: absolute;
            top: 38px;
            right: 0;
            z-index: 10;
            width: 235px;
            padding: 4px;
            background: #fff;
            border: 2px solid #3b1c93;
        }

        .settings-dropdown a {
            display: block;
            padding: 16px 12px;
            margin: 0 0 10px;
            border: 2px solid #3b1c93;
            color: #111;
            background: #fff;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
        }

        .settings-dropdown a:last-child {
            margin-bottom: 0;
        }

        .settings-dropdown a:hover {
            background: #eeeaff;
        }


        .hero-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 440px;
            padding: 10px 50px 20px;
            position: relative;
            flex-grow: 1;
        }

        .services-menu {
            display: flex;
            flex-direction: column;
            gap: 18px;
            z-index: 2;
            width: 320px;
        }

        .service-card {
            display: flex;
            align-items: center;
            background: transparent;
            text-decoration: none;
            color: #fff;
            transition: transform 0.2s;
        }

        .service-card:hover {
            transform: translateX(5px);
        }

        .icon-circle {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: #ffffff;
            border: 4px solid #2c167e;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3b1c93;
            font-size: 30px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            flex-shrink: 0;
            z-index: 1;
        }

        .icon-circle img { 
            width: 78%;
            height: 78%;
            object-fit: contain;
            border-radius: 50%;
        }

        .service-btn {
            background: rgba(91, 61, 204, 0.35);
            border: 1px solid #2c167e;
            padding: 7px 12px 7px 40px;
            border-radius: 0;
            font-size: 18px;
            font-weight: 500;
            margin-left: -1px;
            width: 100%;
        }

  
        .illustration-area {
            position: absolute;
            right: 25px;
            bottom: 0;
            width: 76%;
            height: 100%;
                background: url('/FixLine/View/images/Backgroundpic2 2.jpg') no-repeat center right;
            background-size: contain;
            mix-blend-mode: multiply;
            pointer-events: none;
            opacity: 0.85;
        }


        footer {
            background-color: #ffffff;
            color: #000000;
            padding: 10px 60px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 110px;
        }

        .footer-brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
        }

        .footer-brand i {
            font-size: 32px;
            color: #000;
        }

        .footer-brand h2 {
            font-size: 24px;
            font-weight: bold;
            color: #000;
        }

        .footer-col {
            font-size: 14px;
            line-height: 1.6;
        }

        .footer-col h4 {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .footer-col a {
            color: #000;
            text-decoration: none;
            display: block;
        }

        .footer-col a:hover {
            text-decoration: underline;
        }

        .social-icons {
            display: flex;
            gap: 12px;
            margin-top: 5px;
        }

        .social-icons a {
            display: inline-block;
            border-radius: 50%;
        }

        .social-icons img {
            width: 24px;
            height: 24px;
            object-fit: contain;
            display: block;
        }

        @media (max-width: 900px) {
            .hero-section {
                flex-direction: column;
                align-items: flex-start;
            }
            .illustration-area {
                display: none;
            }
            footer {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
        }
    </style>
</head>
<body>


    <header>
        <div class="header-left">
            <img src="/FixLine/View/images/logo.png" alt="FixLine Logo" style="width: 40px; height: 40px;">
            <span>Customers Dashboard</span>
        </div>

        <form class="search-container" action="/FixLine/index.php" method="get">
            <input type="hidden" name="action" value="search">
            <input type="search" name="search" placeholder="Search services or providers" aria-label="Search services or providers">
        </form>

        <div class="header-right">
            <a class="bookings-link" href="/FixLine/index.php?action=my_bookings">My Bookings</a>
            <a class="refund-link" href="/FixLine/index.php?action=refunds">Refunds</a>
            <details class="settings-menu">
                <summary>
                    <img src="/FixLine/View/images/csettings.png" alt="Settings" title="Settings">
                </summary>
                <div class="settings-dropdown">
                    <a href="/FixLine/View/Customer/account_settings.php?tab=profile">Account Settings</a>
                    <a href="/FixLine/index.php?action=logout">Log out</a>
                </div>
            </details>
        </div>
    </header>

   
    <main class="hero-section">
       
        <div class="services-menu">
            
            <a href="/FixLine/index.php?action=search&category=plumber" class="service-card">
                <div class="icon-circle">
                    <img src="/FixLine/View/images/services/technician.png" alt="Plumber">
                </div>
                <div class="service-btn">Plumber</div>
            </a>

            <a href="/FixLine/index.php?action=search&category=electrician" class="service-card">
                <div class="icon-circle">
                    <img src="/FixLine/View/images/services/Electrician.png" alt="Electrician">
                </div>
                <div class="service-btn">Electrician</div>
            </a>

            <a href="/FixLine/index.php?action=search&category=painter" class="service-card">
                <div class="icon-circle">
                    <img src="/FixLine/View/images/services/painter.png" alt="Painter">
                </div>
                <div class="service-btn">Painter & Decorator</div>
            </a>

            <a href="/FixLine/index.php?action=search&category=repairer" class="service-card">
                <div class="icon-circle">
                    <img src="/FixLine/View/images/services/Appliance.png" alt="Appliance Repairer">
                </div>
                <div class="service-btn">Appliance Repairer</div>
            </a>

        </div>

 
        <div class="illustration-area"></div>
    </main>


    <footer>
        <div class="footer-brand">
            <img src="/FixLine/View/images/protest.png" alt="FixLine Logo" style="width: 60px; height: 60px;">
            <h2>FixLine</h2>
        </div>

        <div class="footer-col">
            <h4>Created by :</h4>
        </div>

        <div class="footer-col">
            <h4>Support</h4>
            <a href="#">FAQs</a>
        </div>

        <div class="footer-col">
            <h4>About us</h4>
            <a href="#">Contact us</a>
            <a href="#">Our license</a>
        </div>

        <div class="footer-col">
            <h4>Social Media</h4>
            <div class="social-icons">
                <a href="#"><img src="/FixLine/View/images/communication.png" alt="Facebook"></a>
                <a href="#"><img src="/FixLine/View/images/instagram.png" alt="Instagram"></a>
                <a href="#"><img src="/FixLine/View/images/logos.png" alt="X"></a>
                <a href="#"><img src="/FixLine/View/images/youtube.png" alt="YouTube"></a>
            </div>
        </div>
    </footer>

</body>
</html>

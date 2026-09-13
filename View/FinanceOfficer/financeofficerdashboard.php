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
    <title>FixLine - Finance Officer Dashboard</title>
 
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
            width: 78%;
            height: 100%;
            background: url('../images/dashboad.jpeg') no-repeat center right;
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
            <img src="../images/protest.png" alt="FixLine Logo" style="width: 40px; height: 40px;">
            <span>Finance Officer Dashboard</span>
        </div>

        <form class="search-container" action="index.php" method="get">
            <input type="hidden" name="action" value="search">
            <input type="search" name="search" placeholder="Search" aria-label="Search">
        </form>

        <div class="header-right">
            <img src="../images/fnotification.png" alt="Notifications" title="Notifications">
            <img src="../images/chat.png" alt="Messages" title="Messages">
            <img src="../images/fsettings.png" alt="Settings" title="Settings">
        </div>
    </header>

   
    <main class="hero-section">
       
        <div class="services-menu">
            
            <a href="payments.php" class="service-card">
                <div class="icon-circle">
                    <img src="../images/services/payment-method (1).png" alt="Payments">
                </div>
                <div class="service-btn">Payments</div>
            </a>

            <a href="payout.php" class="service-card">
                <div class="icon-circle">
                    <img src="../images/services/atm.png" alt="Payout">
                </div>
                <div class="service-btn">Payout</div>
            </a>

            <a href="refunds.php" class="service-card">
                <div class="icon-circle">
                    <img src="../images/services/refund.png" alt="Refunds">
                </div>
                <div class="service-btn">Refunds</div>
            </a>

            <a href="account-management.php" class="service-card">
                <div class="icon-circle">
                    <img src="../images/services/accountant.png" alt="Account Management">
                </div>
                <div class="service-btn">Account Management</div>
            </a>

        </div>

 
        <div class="illustration-area"></div>
    </main>


    <footer>
        <div class="footer-brand">
            <img src="../images/protest.png" alt="FixLine Logo" style="width: 60px; height: 60px;">
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
                <a href="#"><img src="../images/communication.png" alt="Facebook"></a>
                <a href="#"><img src="../images/instagram.png" alt="Instagram"></a>
                <a href="#"><img src="../images/logos.png" alt="X"></a>
                <a href="#"><img src="../images/youtube.png" alt="YouTube"></a>
            </div>
        </div>
    </footer>

</body>
</html>
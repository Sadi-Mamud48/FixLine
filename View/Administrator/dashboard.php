<?php
// View/Administrator/dashboard.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard - FixLine</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            background: #7559e8;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .main-container {
            width: 95%;
            min-height: 95vh;
            border-radius: 25px;
            overflow: hidden;
            background: #ffffff;
            box-shadow: 10px 10px 0px rgba(50, 20, 100, 0.35);
        }

        /* TOP AREA */

        .dashboard {
            min-height: 78vh;
            position: relative;

            background-image:
                linear-gradient(
                    rgba(93, 54, 196, 0.55),
                    rgba(93, 54, 196, 0.55)
                ),
                url('../image/background.png');

            background-size: cover;
            background-position: center;
        }

        /* TOP BAR */

        .topbar {
            height: 70px;
            background: rgba(92, 52, 190, 0.9);

            display: flex;
            align-items: center;

            padding: 0 30px;
        }

        .logo {
            width: 45px;
            height: 45px;
            object-fit: contain;
        }

        .page-title {
            color: white;
            font-size: 18px;
            margin-left: 20px;
        }

        .search-box {
            width: 350px;
            height: 38px;

            margin-left: 80px;

            border-radius: 25px;
            border: 2px solid #222;

            padding: 0 18px;

            font-size: 15px;
            outline: none;
        }

        .top-icons {
            margin-left: auto;

            display: flex;
            gap: 25px;
        }

        .top-icons img {
            width: 25px;
            height: 25px;
            object-fit: contain;
        }

        /* LEFT MENU */

        .side-menu {
            position: absolute;

            left: 45px;
            top: 95px;

            display: flex;
            flex-direction: column;

            gap: 18px;
        }

        .menu-item {
            display: flex;
            align-items: center;

            cursor: pointer;

            text-decoration: none;
        }

        .menu-icon {
            width: 65px;
            height: 65px;

            border-radius: 50%;

            background: white;

            border: 4px solid #3d208d;

            padding: 5px;

            object-fit: contain;
        }

        .menu-text {
            margin-left: 10px;

            width: 150px;

            padding: 7px;

            border: 1px solid #302064;

            color: white;

            text-align: center;

            font-size: 15px;
        }

        /* FOOTER */

        .footer {
            min-height: 17vh;

            display: flex;

            justify-content: space-around;

            align-items: center;

            background: white;
        }

        .footer-column {
            color: #111;
            font-size: 14px;
            line-height: 1.5;
        }

        .footer-logo {
            width: 55px;
            display: block;
            margin: auto;
        }

        .footer-title {
            font-size: 22px;
            text-align: center;
        }

    </style>
</head>

<body>

<div class="main-container">

    <!-- ================= DASHBOARD ================= -->

    <div class="dashboard">

        <!-- TOP BAR -->

        <div class="topbar">

            <img
                src="../image/logo.png"
                class="logo"
                alt="FixLine Logo"
            >

            <div class="page-title">
                Admin Dashboard
            </div>

            <input
                type="text"
                class="search-box"
                placeholder="🔍 Search"
            >

            <div class="top-icons">

                <img src="../image/notification.png" alt="Notification">

                <img src="../image/message.png" alt="Message">

                <img src="../image/settings.png" alt="Settings">

            </div>

        </div>


        <!-- SIDE MENU -->

        <div class="side-menu">

            <!-- Analytics -->

            <a href="#" class="menu-item">

                <img
                    src="../image/analytics.png"
                    class="menu-icon"
                    alt="Analytics"
                >

                <span class="menu-text">
                    Analytics
                </span>

            </a>


            <!-- Users Information -->

            <a href="#" class="menu-item">

                <img
                    src="../image/users.png"
                    class="menu-icon"
                    alt="Users"
                >

                <span class="menu-text">
                    Users Information
                </span>

            </a>


            <!-- ACCOUNT MANAGEMENT -->

            <a
                href="../../index.php?controller=administrator&action=accounts"
                class="menu-item"
            >

                <img
                    src="../image/account.png"
                    class="menu-icon"
                    alt="Account Management"
                >

                <span class="menu-text">
                    Account Management
                </span>

            </a>

        </div>

    </div>


    <!-- ================= FOOTER ================= -->

    <div class="footer">

        <div class="footer-column">

            <img
                src="../image/logo.png"
                class="footer-logo"
                alt="FixLine"
            >

            <div class="footer-title">
                FixLine
            </div>

        </div>


        <div class="footer-column">

            <b>Created by :</b>

        </div>


        <div class="footer-column">

            <b>Support</b><br>
            FAQs

        </div>


        <div class="footer-column">

            <b>About us</b><br>
            Contact us<br>
            Our license

        </div>


        <div class="footer-column">

            <b>Social Media</b><br>
            Facebook &nbsp; Instagram &nbsp; Twitter &nbsp; YouTube

        </div>

    </div>

</div>

</body>
</html>
<?php
// users data comes from AdministratorController
?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Account Management - FixLine</title>


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


        .container {

            width: 95%;

            min-height: 95vh;

            background: white;

            border-radius: 25px;

            overflow: hidden;

            box-shadow:
                10px 10px 0px rgba(50, 20, 100, 0.35);

        }


        .content {

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


        /* TOP */

        .topbar {

            height: 65px;

            background: rgba(92, 52, 190, 0.9);

            display: flex;

            align-items: center;

            padding: 0 25px;

        }


        .logo {

            width: 42px;

            height: 42px;

            object-fit: contain;

        }


        .home {

            color: white;

            margin-left: 20px;

            font-size: 16px;

        }


        .search {

            margin-left: 120px;

            width: 350px;

            height: 38px;

            border-radius: 25px;

            border: 2px solid #222;

            padding: 0 15px;

            font-size: 15px;

        }


        .icons {

            margin-left: auto;

            display: flex;

            gap: 25px;

        }


        .icons img {

            width: 25px;

            height: 25px;

            object-fit: contain;

        }


        /* TITLE */

        h1 {

            color: white;

            font-size: 20px;

            margin: 10px 0 15px 75px;

        }


        /* USER LIST */

        .users {

            margin-left: 45px;

            display: flex;

            flex-direction: column;

            gap: 12px;

        }


        .user-link {

            display: flex;

            align-items: center;

            text-decoration: none;

            width: 250px;

        }


        .user-photo {

            width: 65px;

            height: 65px;

            border-radius: 50%;

            background: white;

            border: 4px solid #3d208d;

            padding: 4px;

            object-fit: cover;

        }


        .user-name {

            width: 140px;

            margin-left: 10px;

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

            font-size: 14px;

            line-height: 1.5;

        }


        .footer-logo {

            width: 50px;

            display: block;

            margin: auto;

        }


        .footer-title {

            font-size: 22px;

        }

    </style>

</head>


<body>


<div class="container">


    <!-- CONTENT -->

    <div class="content">


        <div class="topbar">


            <img
                src="../image/logo.png"
                class="logo"
                alt="FixLine"
            >


            <span class="home">
                Home
            </span>


            <input
                type="text"
                class="search"
                placeholder="🔍 Search"
            >


            <div class="icons">

                <img
                    src="../image/notification.png"
                    alt="Notification"
                >

                <img
                    src="../image/message.png"
                    alt="Message"
                >

                <img
                    src="../image/settings.png"
                    alt="Settings"
                >

            </div>


        </div>


        <h1>
            Account Management
        </h1>


        <!-- USERS -->

        <div class="users">


            <?php foreach ($users as $user): ?>


                <a
                    class="user-link"
                    href="../../index.php?controller=administrator&action=accountDetails&id=<?php echo $user['id']; ?>"
                >


                    <img
                        class="user-photo"
                        src="../image/<?php echo htmlspecialchars($user['photo']); ?>"
                        alt="<?php echo htmlspecialchars($user['name']); ?>"
                    >


                    <span class="user-name">

                        <?php echo htmlspecialchars($user['name']); ?>

                    </span>


                </a>


            <?php endforeach; ?>


        </div>


    </div>


    <!-- FOOTER -->

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
<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Account Details - FixLine</title>


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

            padding-top: 60px;

        }


        /* BACK BUTTON */

        .back {

            position: absolute;

            top: 35px;

            left: 45px;

            width: 35px;

            height: 35px;

            border-radius: 50%;

            background: #000;

            color: white;

            display: flex;

            justify-content: center;

            align-items: center;

            text-decoration: none;

            font-size: 20px;

        }


        /* TITLE */

        .title {

            width: 220px;

            margin: 0 auto 25px auto;

            padding: 8px;

            background: rgba(255,255,255,0.15);

            border: 2px solid black;

            color: white;

            text-align: center;

            font-size: 19px;

            font-weight: bold;

        }


        /* PROFILE */

        .profile {

            width: 600px;

            margin: auto;

            display: grid;

            grid-template-columns: 280px 1fr;

            column-gap: 25px;

            row-gap: 15px;

        }


        .photo-section {

            grid-row: span 3;

            text-align: center;

        }


        .photo-section img {

            width: 120px;

            height: 120px;

            border-radius: 50%;

            border: 5px solid white;

            object-fit: cover;

        }


        label {

            display: block;

            color: white;

            font-weight: bold;

            margin-bottom: 5px;

        }


        input,
        select {

            width: 100%;

            height: 38px;

            border: none;

            background: white;

            padding: 5px 10px;

            font-size: 15px;

        }


        .field {

            margin-bottom: 5px;

        }


        /* BUTTONS */

        .buttons {

            grid-column: 1 / 3;

            display: flex;

            justify-content: center;

            gap: 25px;

            margin-top: 25px;

        }


        button {

            width: 120px;

            height: 38px;

            border: none;

            background: #4d2bb0;

            color: white;

            font-size: 15px;

            cursor: pointer;

        }


        button:hover {

            background: #301477;

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


    <div class="content">


        <!-- BACK -->

        <a
            href="../../index.php?controller=administrator&action=accounts"
            class="back"
        >
            ←
        </a>


        <!-- TITLE -->

        <div class="title">

            Account Management

        </div>


        <!-- PROFILE -->

        <form
            class="profile"
            method="POST"
            action="../../index.php?controller=administrator&action=saveUser"
        >


            <!-- PHOTO -->

            <div class="photo-section">

                <img
                    src="../image/<?php echo htmlspecialchars($user['photo']); ?>"
                    alt="Profile"
                >

                <input
                    type="hidden"
                    name="id"
                    value="<?php echo $user['id']; ?>"
                >

            </div>


            <!-- ACCOUNT NAME -->

            <div class="field">

                <label>
                    Account Name
                </label>

                <input
                    type="text"
                    name="name"
                    value="<?php echo htmlspecialchars($user['name']); ?>"
                >

            </div>


            <!-- ROLE -->

            <div class="field">

                <label>
                    Role
                </label>

                <select name="role">

                    <option
                        <?php if ($user['role'] == 'Plumber') echo 'selected'; ?>
                    >
                        Plumber
                    </option>

                    <option
                        <?php if ($user['role'] == 'Electrician') echo 'selected'; ?>
                    >
                        Electrician
                    </option>

                    <option
                        <?php if ($user['role'] == 'Customer') echo 'selected'; ?>
                    >
                        Customer
                    </option>

                    <option
                        <?php if ($user['role'] == 'Moderator') echo 'selected'; ?>
                    >
                        Moderator
                    </option>

                    <option
                        <?php if ($user['role'] == 'Finance Officer') echo 'selected'; ?>
                    >
                        Finance Officer
                    </option>

                </select>

            </div>


            <!-- PHONE -->

            <div class="field">

                <label>
                    Phone Number
                </label>

                <input
                    type="text"
                    name="phone"
                    value="<?php echo htmlspecialchars($user['phone']); ?>"
                >

            </div>


            <!-- BUTTONS -->

            <div class="buttons">

                <button type="submit">
                    Save
                </button>


                <button type="button">
                    Change Settings
                </button>


                <button type="button">
                    Block
                </button>

            </div>


        </form>


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
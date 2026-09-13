<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$error = $_SESSION['auth_error'] ?? null;
unset($_SESSION['auth_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FixLine - Customer Sign in</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; font-family: Arial, sans-serif; background: #39219a; color: #39219a; }
        .shell { display: grid; grid-template-columns: minmax(360px, 38%) 1fr; min-height: 100vh; width: 100%; overflow: hidden; background: #fff; }
        .panel { padding: 28px 9%; position: relative; background: #fff; }
        .brand { width: 54px; height: 54px; object-fit: contain; }
        .top-nav { position: absolute; top: 28px; right: 10%; display: flex; gap: 18px; font-size: 11px; }
        .top-nav a, .back-link { color: #39219a; text-decoration: none; }
        .back { display: inline-block; width: 30px; height: 30px; margin-top: 12px; }
        .back img { width: 100%; height: 100%; object-fit: contain; display: block; }
        h1 { margin: 18px 0 18px; font-size: 30px; line-height: 1.05; }
        .tabs { display: flex; gap: 0; margin-bottom: 28px; }
        .tabs a { width: 90px; text-align: center; }
        .tabs a { color: #aaa0da; font-size: 18px; text-decoration: none; padding-bottom: 4px; border-bottom: 2px solid currentColor; }
        .tabs a.active { color: #39219a; }
        form { max-width: 300px; }
        label { display: block; margin: 0 0 8px; color: #222; font-size: 14px; }
        input { width: 100%; padding: 11px 0; border: 0; border-bottom: 2px solid #333; outline: 0; font-size: 15px; margin-bottom: 20px; }
        .password-row { position: relative; }
        .password-row input { padding-right: 30px; }
        .password-row button { position: absolute; right: 0; bottom: 26px; border: 0; background: none; cursor: pointer; }
        .submit { width: 95px; padding: 10px; border: 0; background: #4524a9; color: #fff; font-size: 15px; cursor: pointer; }
        .notice { max-width: 300px; margin-bottom: 14px; padding: 10px; background: #f0edff; color: #222; font-size: 13px; }
        .account-type { width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #333; font-size: 14px; }
        .provider-fields { display: none; }
        .illustration { background: #382092 url('/FixLine/View/images/Picture.jpg') center / contain no-repeat; }
        @media (max-width: 760px) { .shell { grid-template-columns: 1fr; } .illustration { min-height: 300px; } .top-nav { right: 8%; } }
    </style>
</head>
<body>
    <main class="shell">
        <section class="panel">
            <img class="brand" src="/FixLine/View/images/protest.png" alt="FixLine">
            <nav class="top-nav"><a href="#">Home</a><a href="#">About us</a><a href="#">Contact us</a><a href="#">Help</a></nav>
            <a class="back" href="login.php" aria-label="Back"><img src="../images/left-arrow.png" alt="Back"></a>
            <h1>Welcome to<br>FixLine</h1>
            <?php if ($error !== null): ?><div class="notice"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <div class="tabs"><a href="login.php">Log in</a><a class="active" href="customer_signup.php">Sign in</a></div>
            <form method="post" action="/FixLine/index.php?action=customer_signup">
                <label for="account_type">Account type</label>
                <select id="account_type" class="account-type" name="account_type">
                    <option value="customer" selected>Customer</option>
                    <option value="provider">Provider</option>
                </select>
                <label for="name">Full Name</label><input id="name" type="text" name="name" required autocomplete="name">
                <label for="email">Email</label><input id="email" type="email" name="email" required autocomplete="email">
                <label for="phone">Phone Number</label><input id="phone" type="tel" name="phone" pattern="[0-9]{11}" maxlength="11" inputmode="numeric" required autocomplete="tel">
                <div class="password-row"><label for="password">Password</label><input id="password" type="password" name="password" minlength="8" required autocomplete="new-password"><button type="button" onclick="togglePassword()">&#128065;</button></div>
                <div class="confirmpassword-row"><label for="password_confirmation">Confirm Password</label><input id="password_confirmation" type="password" name="password_confirmation" minlength="8" required autocomplete="new-password"></div>
                <div id="provider-fields" class="provider-fields">
                    <select id="work_type" class="work-type" name="work_type">
                    <option value="typeofwork" selected>Type of work</option>
                    <option value="plumber">Plumber</option>
                    <option value="electrician">Electrician</option>
                    <option value="painter">Painter & Decorator</option>
                    <option value="appliance">Appliance Repairer</option>
                    </select> <br><br>
                    <label for="experience">Experience</label><input id="experience" type="text" name="experience" placeholder="Years / months / days">
                    <label for="affiliate">Affiliate</label><input id="affiliate" type="text" name="affiliate">
                    <label for="bio">Bio (Optional)</label><textarea id="bio" name="bio"></textarea><br><br>
                </div>
                <button class="submit" type="submit">Sign in</button>
            </form>
        </section>
        <section class="illustration"></section>
    </main>
    <script>
        function togglePassword(){const field=document.getElementById('password');field.type=field.type==='password'?'text':'password';}

        const accountType = document.getElementById('account_type');
        const providerFields = document.getElementById('provider-fields');
        const profession = document.getElementById('profession');

        accountType.addEventListener('change', () => {
            const providerSelected = accountType.value === 'provider';
            providerFields.style.display = providerSelected ? 'block' : 'none';
            profession.required = providerSelected;
        });
    </script>
</body>
</html>

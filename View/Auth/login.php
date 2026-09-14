<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = $_SESSION['auth_error'] ?? null;
$message = $_SESSION['auth_message'] ?? null;
unset($_SESSION['auth_error'], $_SESSION['auth_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FixLine - Log in</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; font-family: Arial, sans-serif; background: #8264f4; color: #39219a; }
        .auth-shell { display: grid; grid-template-columns: minmax(360px, 38%) 1fr; min-height: 100vh; width: 100%; overflow: hidden; background: #fff; }
        .auth-panel { padding: 28px 9%; position: relative; background: #fff; }
        .brand { width: 54px; height: 54px; object-fit: contain; }
        .top-nav { position: absolute; top: 28px; right: 10%; display: flex; gap: 18px; font-size: 11px; }
        .top-nav a { color: #39219a; text-decoration: none; }
        .back { display: inline-block; width: 30px; height: 30px; margin-top: 12px; }
        .back img { width: 100%; height: 100%; object-fit: contain; display: block; }
        h1 { margin: 18px 0 30px; font-size: 30px; line-height: 1.05; }
        .tabs { display: flex; gap: 0; margin-bottom: 38px; }
        .tabs a { width: 90px; text-align: center; }
        .tabs a { color: #aaa0da; font-size: 18px; text-decoration: none; padding-bottom: 4px; border-bottom: 2px solid currentColor; }
        .tabs a.active { color: #39219a; }
        form { max-width: 300px; }
        label { display: block; margin: 0 0 8px; color: #222; font-size: 14px; }
        input { width: 100%; padding: 12px 0; border: 0; border-bottom: 2px solid #333; outline: 0; font-size: 15px; margin-bottom: 28px; }
        .password-row { position: relative; }
        .password-row input { padding-right: 30px; }
        .password-row button { position: absolute; right: 0; bottom: 34px; border: 0; background: none; cursor: pointer; }
        .forgot { display: block; margin: -12px 0 36px; color: #222; text-align: right; font-size: 13px; text-decoration: none; }
        .submit { width: 110px; padding: 10px; border: 0; background: #4524a9; color: #fff; font-size: 15px; cursor: pointer; }
        .notice { max-width: 300px; margin-bottom: 18px; padding: 10px; background: #f0edff; color: #222; font-size: 13px; }
        .illustration { display: grid; place-items: center; background: #382092; background-image: linear-gradient(90deg, rgba(56,32,146,.25), rgba(56,32,146,.25)), url('/FixLine/View/images/Picture.jpg'); background-repeat: no-repeat; background-position: center; background-size: contain; }
        @media (max-width: 760px) { .auth-shell { grid-template-columns: 1fr; } .illustration { min-height: 300px; } .top-nav { right: 8%; } }
    </style>
</head>
<body>
    <main class="auth-shell">
        <section class="auth-panel">
            <img class="brand" src="/FixLine/View/images/protest.png" alt="FixLine">
            <nav class="top-nav"><a href="#">Home</a><a href="#">About us</a><a href="#">Contact us</a><a href="#">Help</a></nav>
           
            <h1>Welcome to<br>FixLine</h1>
            <?php if ($error !== null): ?><div class="notice"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <?php if ($message !== null): ?><div class="notice"><?= htmlspecialchars($message) ?></div><?php endif; ?>
            <div class="tabs"><a class="active" href="/FixLine/index.php?action=login">Log in</a><a href="/FixLine/index.php?action=customer_signup">Sign in</a></div>
            <form method="post" action="/FixLine/index.php?action=login">
                <label for="role">Log in as</label>
                <select id="role" name="role" required style="width:100%;padding:12px;margin-bottom:20px;border:1px solid #aaa;font-size:15px;">
                    <option value="customer">Customer</option>
                    <option value="provider">Service Provider</option>
                    <option value="other">Others</option>
                </select>
                <label for="email">Email</label>
                <input id="email" type="text" name="email" required autocomplete="username">
                <div class="password-row"><label for="password">Password</label><input id="password" type="password" name="password" required autocomplete="current-password"><button type="button" onclick="togglePassword()">&#128065;</button></div>
                <a class="forgot" href="#">forgot password?</a>
                <button class="submit" type="submit">Log in</button>
            </form>
        </section>
        <section class="illustration" aria-label="FixLine workers"></section>
    </main>
    <script>function togglePassword(){const field=document.getElementById('password');field.type=field.type==='password'?'text':'password';}</script>
</body>
</html>

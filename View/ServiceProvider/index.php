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
    <title>FixLine - Provider Dashboard</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; font-family: Arial, sans-serif; background: #5b3dcc; color: #fff; }
        .card { width: min(560px, 90%); padding: 36px; background: #fff; color: #222; border-radius: 8px; text-align: center; }
        a { display: inline-block; margin-top: 20px; padding: 10px 18px; background: #4524a9; color: #fff; text-decoration: none; }
    </style>
</head>
<body>
    <main class="card">
        <h1>Provider Dashboard</h1>
        <p>Your provider account is registered. Booking management will appear here.</p>
        <a href="/FixLine/cindex.php?action=logout">Log out</a>
    </main>
</body>
</html>

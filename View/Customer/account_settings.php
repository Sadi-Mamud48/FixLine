<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$tab = $_GET['tab'] ?? 'profile';

if (!isset($user)) {
    require_once __DIR__ . '/../../Config/Database.php';
    require_once __DIR__ . '/../../Model/Customer.php';

    $database = new Database();
    $customerModel = new Customer($database->getConnection());
    $userId = $_SESSION['user_id'] ?? 1;
    $user = $customerModel->getUser($userId);
}

$user = $user ?? [];
$settingsMessage = $_SESSION['settings_message'] ?? null;
$settingsError = $_SESSION['settings_error'] ?? null;
unset($_SESSION['settings_message'], $_SESSION['settings_error']);

$profilePhoto = $user['profile_photo'] ?? '';

if ($profilePhoto === '') {
    $profilePhoto = '/FixLine/View/images/protest.png';
} elseif (substr($profilePhoto, 0, 1) !== '/') {
    $profilePhoto = '/FixLine/View/' . ltrim(substr($profilePhoto, 3), '/');
}

$profilePhoto .= '?v=' . time();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FixLine - Account Settings</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; font-family: Arial, sans-serif; background: #5b3dcc; color: #fff; }
        .page { min-height: 100vh; padding: 24px 7%; background: linear-gradient(rgba(91, 61, 204, .88), rgba(91, 61, 204, .88)), url('../images/Backgroundpic2 2.jpg') center / cover; }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
        }
        .page-header h1 { margin: 0; font-size: 24px; }
        .page-header a { color: #fff; text-decoration: none; }
        .back-link { font-size: 16px; font-weight: 700; }
        .back-link:hover { text-decoration: underline; }
        .settings-layout { display: grid; grid-template-columns: 250px 1fr; gap: 44px; max-width: 920px; margin: 0 auto; }
        .settings-nav { display: grid; align-content: start; gap: 4px; }
        .settings-nav a { padding: 14px 8px; border: 2px solid #160b55; color: #fff; text-align: center; text-decoration: none; font-size: 20px; font-weight: 700; }
        .settings-nav a.active, .settings-nav a:hover { background: rgba(255, 255, 255, .16); }
        .settings-card { min-height: 370px; }
        .settings-card h2 { margin: 0 0 6px; font-size: 23px; }
        .settings-card p { margin: 0 0 20px; font-weight: 600; line-height: 1.4; }
        .settings-card form { display: grid; gap: 18px; }
        .field-row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 24px; }
        .settings-card label { display: grid; gap: 6px; font-weight: 700; }
        .settings-card input, .settings-card textarea { width: 100%; padding: 11px; border: 0; font: inherit; }
        .settings-card textarea { width: 320px; min-height: 64px; resize: none; }
        .profile-photo { width: 96px; height: 96px; border-radius: 50%; background: #fff; object-fit: cover; display: block; margin-bottom: 8px; }
        .settings-card button { justify-self: center; min-width: 130px; padding: 10px 24px; border: 0; background: #4524a9; color: #fff; font-size: 17px; font-weight: 700; cursor: pointer; }
        .notice { max-width: 920px; margin: 0 auto 18px; padding: 12px; background: #fff; color: #222; }
        @media (max-width: 700px) {
            .settings-layout { grid-template-columns: 1fr; gap: 24px; }
            .field-row { grid-template-columns: 1fr; gap: 18px; }
            .settings-card textarea { width: 100%; }
        }
    </style>
</head>
<body>
    <main class="page">
        <div class="page-header">
            <h1>Settings</h1>
            <a class="back-link" href="/FixLine/View/Customer/customer_dashboard.php">Back to Dashboard</a>
        </div>

        <?php if ($settingsMessage !== null): ?>
            <div class="notice"><?= htmlspecialchars($settingsMessage) ?></div>
        <?php endif; ?>
        <?php if ($settingsError !== null): ?>
            <div class="notice">Unable to save changes: <?= htmlspecialchars($settingsError) ?></div>
        <?php endif; ?>

        <div class="settings-layout">
            <nav class="settings-nav" aria-label="Settings sections">
                <a class="<?= $tab === 'account' ? 'active' : '' ?>" href="/FixLine/View/Customer/account_settings.php?tab=account">Account Management</a>
                <a class="<?= $tab === 'profile' ? 'active' : '' ?>" href="/FixLine/View/Customer/account_settings.php?tab=profile">Profile</a>
            </nav>

            <section class="settings-card">
                <?php if ($tab === 'account'): ?>
                    <h2>Account management</h2>
                    <p>Make changes to your personal information or account type.</p>
                    <form method="post" action="/FixLine/index.php?action=account_settings&tab=account">
                        <input type="hidden" name="settings_section" value="account">
                        <label>
                            Your account
                            <input type="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly>
                        </label>
                        <label>
                            Phone Number (11 digits)
                            <input
                                id="phone-input"
                                type="tel"
                                name="phone"
                                value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                                inputmode="numeric"
                                minlength="11"
                                maxlength="11"
                                pattern="[0-9]{11}"
                                title="Please enter exactly 11 digits."
                                autocomplete="tel"
                                required
                            >
                        </label>
                        <div class="field-row">
                            <label>
                                Current Password
                                <input type="password" name="current_password" autocomplete="current-password">
                            </label>
                            <label>
                                New Password
                                <input type="password" name="new_password" autocomplete="new-password">
                            </label>
                        </div>
                        <button type="submit">Save</button>
                    </form>
                <?php else: ?>
                    <h2>Profile</h2>
                    <form method="post" action="/FixLine/index.php?action=account_settings&tab=profile" enctype="multipart/form-data">
                        <input type="hidden" name="settings_section" value="profile">
                        <label>
                            Profile Photo
                            <img id="profile-photo-preview" class="profile-photo" src="<?= htmlspecialchars($profilePhoto) ?>" alt="Profile photo">
                            <input id="profile-photo-input" type="file" name="profile_photo" accept="image/*">
                        </label>
                        <div class="field-row">
                            <label>
                                First name
                                <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" autocomplete="given-name">
                            </label>
                            <label>
                                Last name
                                <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" autocomplete="family-name">
                            </label>
                        </div>
                        <label>
                            Address
                            <textarea name="address"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                        </label>
                        <button type="submit">Save</button>
                    </form>
                <?php endif; ?>
            </section>
        </div>
    </main>
    <script>
        const phoneInput = document.getElementById('phone-input');
        const profilePhotoInput = document.getElementById('profile-photo-input');
        const profilePhotoPreview = document.getElementById('profile-photo-preview');

        if (phoneInput) {
            phoneInput.addEventListener('input', () => {
                phoneInput.value = phoneInput.value.replace(/\D/g, '').slice(0, 11);
                phoneInput.setCustomValidity(
                    phoneInput.value.length === 11 ? '' : 'Please enter exactly 11 digits.'
                );
            });
        }

        if (profilePhotoInput && profilePhotoPreview) {
            profilePhotoInput.addEventListener('change', () => {
                const selectedFile = profilePhotoInput.files[0];

                if (selectedFile) {
                    profilePhotoPreview.src = URL.createObjectURL(selectedFile);
                }
            });
        }
    </script>
</body>
</html>

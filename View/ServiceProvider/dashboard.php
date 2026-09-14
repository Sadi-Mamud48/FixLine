<?php
// Expects: $provider (array), $summary (array) — supplied by ServiceProviderController::dashboard()
$activePage = 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FixLine — Provider Dashboard</title>
    <link rel="stylesheet" href="View/css/style.css">
</head>
<body>
<div class="fx-page">
    <div class="fx-shell">

        <?php include __DIR__ . '/partials/header.php'; ?>

        <div class="fx-dashboard-body">

            <!-- Left column: avatar + name + stats -->
            <div class="fx-profile-block">
                <div class="fx-avatar-wrap">
                    <img class="fx-avatar"
                         src="<?= htmlspecialchars($provider['profile_picture'] ?? '/FixLine/View/images/plumber.png') ?>"
                         alt="Profile picture">
                </div>

                <div class="fx-name-card">
                    <h2><?= htmlspecialchars($provider['name'] ?? 'Service Provider') ?></h2>
                    <div>Affiliate: <?= htmlspecialchars($provider['affiliate'] ?? '—') ?></div>
                    <div class="fx-stars">
                        Rating
                        <?php
                        $rating = (float) ($provider['rating'] ?? 0);
                        echo str_repeat('&#9733;', (int) round($rating)) . str_repeat('&#9734;', 5 - (int) round($rating));
                        ?>
                        <?= number_format($rating, 1) ?>/5
                    </div>
                </div>

                <div class="fx-card">
                    <h3>Profession</h3>
                    <div class="fx-stat" style="font-size:1.1rem;"><?= htmlspecialchars($provider['profession'] ?? '—') ?></div>
                </div>

                <div class="fx-stat-grid">
                    <div class="fx-card">
                        <h3>Work Completed</h3>
                        <div class="fx-stat"><?= (int) ($provider['work_completed'] ?? 0) ?>/<?= (int) ($provider['work_completed'] ?? 0) + (int) ($provider['work_pending'] ?? 0) ?></div>
                    </div>
                    <div class="fx-card">
                        <h3>Work Pending</h3>
                        <div class="fx-stat"><?= (int) ($provider['work_pending'] ?? 0) ?></div>
                    </div>
                    <div class="fx-card">
                        <h3>Work Successful</h3>
                        <div class="fx-stat"><?= (int) ($provider['work_successful'] ?? 0) ?></div>
                    </div>
                    <div class="fx-card">
                        <h3>Experience</h3>
                        <div class="fx-stat" style="font-size:1.1rem;"><?= htmlspecialchars($provider['experience'] ?? '—') ?></div>
                    </div>
                </div>

                <div class="fx-card">
                    <h3>Total Earned</h3>
                    <div class="fx-stat">Tk <?= number_format((float) ($summary['total_earned'] ?? 0), 2) ?></div>
                </div>
            </div>

            <!-- Right column: bio -->
            <div class="fx-bio-box">
                <h3>Bio</h3>
                <p><?= nl2br(htmlspecialchars($provider['bio'] ?? 'No bio added yet. Update your profile to introduce yourself to customers.')) ?></p>

                <a href="/FixLine/index.php?action=provider_profile" class="fx-btn" style="margin-top:12px;display:inline-block;">Edit Profile</a>
                <a href="/FixLine/index.php?action=provider_apply_job" class="fx-btn fx-btn-outline" style="margin-top:12px;display:inline-block;background:rgba(255,255,255,0.15);color:#fff;border-color:#fff;">Find Jobs</a>
            </div>
        </div>

        <?php include __DIR__ . '/partials/footer.php'; ?>
    </div>
</div>
</body>
</html>

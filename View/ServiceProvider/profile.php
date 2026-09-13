<?php
// Expects: $provider (array), $message (string) — supplied by ServiceProviderController::profile()
$activePage = 'profile';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FixLine — My Profile</title>
    <link rel="stylesheet" href="View/css/style.css">
</head>
<body>
<div class="fx-page">
    <div class="fx-shell">

        <?php include __DIR__ . '/partials/header.php'; ?>

        <div style="padding:36px;">
            <div class="fx-form-panel" style="max-width:720px;margin:0 auto;">

                <h2 style="margin-top:0;color:var(--fx-purple-dark);">My Profile</h2>

                <?php if (!empty($message)): ?>
                    <div class="fx-alert <?= str_contains(strtolower($message), 'success') ? 'fx-alert-success' : 'fx-alert-error' ?>">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <!-- ===== Profile picture uploader (AJAX + JSON) ===== -->
                <div class="fx-picture-uploader">
                    <img id="fx-avatar-preview"
                         class="fx-avatar"
                         src="<?= htmlspecialchars($provider['profile_picture'] ?? 'View/images/default-avatar.png') ?>"
                         alt="Profile picture">

                    <div>
                        <button type="button" id="fx-choose-picture-btn" class="fx-btn fx-btn-outline">Change Picture</button>
                        <input type="file" id="fx-picture-input" class="fx-hidden-input" accept="image/png, image/jpeg, image/webp">
                        <div class="fx-upload-status" id="fx-upload-status">JPG, PNG or WEBP — max 2MB.</div>
                    </div>
                </div>

                <!-- ===== Profile details form (plain POST, no AJAX needed) ===== -->
                <form action="service_provider.php?action=profile" method="POST">
                    <div class="fx-form-group">
                        <label for="profession">Profession</label>
                        <input type="text" id="profession" name="profession" required
                               value="<?= htmlspecialchars($provider['profession'] ?? '') ?>">
                    </div>

                    <div class="fx-form-group">
                        <label for="affiliate">Affiliate</label>
                        <input type="text" id="affiliate" name="affiliate"
                               value="<?= htmlspecialchars($provider['affiliate'] ?? '') ?>">
                    </div>

                    <div class="fx-form-group">
                        <label for="experience">Experience (year/month)</label>
                        <input type="text" id="experience" name="experience" placeholder="e.g. 1y 6m"
                               value="<?= htmlspecialchars($provider['experience'] ?? '') ?>">
                    </div>

                    <div class="fx-form-group">
                        <label for="bio">Bio</label>
                        <textarea id="bio" name="bio" placeholder="Tell customers about yourself..."><?= htmlspecialchars($provider['bio'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" name="update_profile" value="1" class="fx-btn">Save Changes</button>
                </form>

                <h3 style="color:var(--fx-purple-dark);margin:32px 0 12px;">Services and Costs</h3>
                <?php foreach (($providerServices ?? []) as $service): ?>
                    <form action="service_provider.php?action=profile" method="POST" style="margin-bottom:16px;">
                        <input type="hidden" name="service_id" value="<?= (int) $service['id'] ?>">
                        <div class="fx-form-group">
                            <label>Service name <input type="text" name="service_name" value="<?= htmlspecialchars($service['service_name']) ?>" required></label>
                        </div>
                        <div class="fx-form-group">
                            <label>Category <input type="text" name="category" value="<?= htmlspecialchars($service['category']) ?>" required></label>
                        </div>
                        <div class="fx-form-group">
                            <label>Cost (Tk) <input type="number" name="price" min="0" step="0.01" value="<?= htmlspecialchars($service['price']) ?>" required></label>
                        </div>
                        <div class="fx-form-group">
                            <label>Description <textarea name="service_description"><?= htmlspecialchars($service['description'] ?? '') ?></textarea></label>
                        </div>
                        <button type="submit" name="save_service" value="1" class="fx-btn">Update Service</button>
                    </form>
                <?php endforeach; ?>

                <form action="service_provider.php?action=profile" method="POST">
                    <div class="fx-form-group"><label>Service name <input type="text" name="service_name" required></label></div>
                    <div class="fx-form-group"><label>Category <input type="text" name="category" required></label></div>
                    <div class="fx-form-group"><label>Cost (Tk) <input type="number" name="price" min="0" step="0.01" required></label></div>
                    <div class="fx-form-group"><label>Description <textarea name="service_description"></textarea></label></div>
                    <button type="submit" name="save_service" value="1" class="fx-btn">Add Service</button>
                </form>
            </div>
        </div>

        <?php include __DIR__ . '/partials/footer.php'; ?>
    </div>
</div>

<script src="View/js/profile.js"></script>
</body>
</html>

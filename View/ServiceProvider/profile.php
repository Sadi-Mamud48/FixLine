<?php
$activePage = 'profile';
$serviceCategories = [
    'plumber' => 'Plumber',
    'electrician' => 'Electrician',
    'painter' => 'Painter & Decorator',
    'repairer' => 'Appliance Repairer',
];
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

                <div class="fx-picture-uploader">
                    <img id="fx-avatar-preview"
                         class="fx-avatar"
                         src="<?= htmlspecialchars($provider['profile_picture'] ?? '/FixLine/View/images/plumber.png') ?>"
                         alt="Profile picture">

                    <div>
                        <button type="button" id="fx-choose-picture-btn" class="fx-btn fx-btn-outline">Change Picture</button>
                        <input type="file" id="fx-picture-input" class="fx-hidden-input" accept="image/png, image/jpeg, image/webp">
                        <div class="fx-upload-status" id="fx-upload-status">JPG, PNG or WEBP — max 2MB.</div>
                    </div>
                </div>

                <form action="/FixLine/index.php?action=provider_profile" method="POST">
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
                <?php $providerServices = $providerServices ?? []; ?>
                <?php if ($providerServices): ?>
                    <?php $firstService = $providerServices[0]; ?>
                    <div class="fx-form-group">
                        <label for="service-selector">Service to update</label>
                        <select id="service-selector" aria-label="Service to update">
                            <?php foreach ($providerServices as $service): ?>
                                <option value="<?= (int) $service['id'] ?>"><?= htmlspecialchars($service['service_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <form id="service-update-form" action="/FixLine/index.php?action=provider_profile" method="POST" style="margin-bottom:16px;">
                        <input id="service-id" type="hidden" name="service_id" value="<?= (int) $firstService['id'] ?>">
                        <div class="fx-form-group">
                            <label>Service name <input id="service-name" type="text" name="service_name" value="<?= htmlspecialchars($firstService['service_name']) ?>" required></label>
                        </div>
                        <div class="fx-form-group">
                            <label>Category
                                <select id="service-category" name="category" required>
                                    <?php foreach ($serviceCategories as $value => $label): ?>
                                        <option value="<?= $value ?>" <?= strtolower($firstService['category']) === $value ? 'selected' : '' ?>><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                        </div>
                        <div class="fx-form-group">
                            <label>Cost (Tk) <input id="service-price" type="number" name="price" min="0" step="0.01" value="<?= htmlspecialchars($firstService['price']) ?>" required></label>
                        </div>
                        <div class="fx-form-group">
                            <label>Description <textarea id="service-description" name="service_description"><?= htmlspecialchars($firstService['description'] ?? '') ?></textarea></label>
                        </div>
                        <button type="submit" name="save_service" value="1" class="fx-btn">Update Service</button>
                    </form>
                    <script>
                        (() => {
                            const services = <?= json_encode(array_values($providerServices), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
                            const selector = document.getElementById('service-selector');
                            const fields = {
                                id: document.getElementById('service-id'),
                                name: document.getElementById('service-name'),
                                category: document.getElementById('service-category'),
                                price: document.getElementById('service-price'),
                                description: document.getElementById('service-description'),
                            };

                            selector.addEventListener('change', () => {
                                const service = services.find((item) => String(item.id) === selector.value);
                                if (!service) return;
                                fields.id.value = service.id;
                                fields.name.value = service.service_name;
                                fields.category.value = String(service.category).toLowerCase();
                                fields.price.value = service.price;
                                fields.description.value = service.description || '';
                            });
                        })();
                    </script>
                <?php else: ?>
                    <p>You have not added any services yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <?php include __DIR__ . '/partials/footer.php'; ?>
    </div>
</div>

<script src="View/js/profile.js"></script>
</body>
</html>

<?php
$activePage = 'apply_job';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FixLine — Apply for a Job</title>
    <link rel="stylesheet" href="View/css/style.css">
</head>
<body>
<div class="fx-page">
    <div class="fx-shell">

        <?php include __DIR__ . '/partials/header.php'; ?>

        <div style="padding:36px;">

            <?php if (!empty($message)): ?>
                <div class="fx-alert <?= str_contains(strtolower($message), 'success') ? 'fx-alert-success' : 'fx-alert-error' ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <h2 style="color:#fff;margin-top:0;">Available Jobs — <?= htmlspecialchars($provider['profession'] ?? '') ?></h2>

            <?php if (empty($jobs)): ?>
                <div class="fx-card-solid">
                    <?php if (empty($provider['profession'])): ?>
                        Add your profession in your profile first. Customers' jobs are matched to that category.
                    <?php else: ?>
                        No open jobs matching <?= htmlspecialchars($provider['profession']) ?> right now. A customer can post a job from the customer dashboard.
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="fx-job-list" style="margin-bottom:36px;">
                    <?php foreach ($jobs as $job): ?>
                        <div class="fx-job-card">
                            <div>
                                <h4><?= htmlspecialchars($job['title']) ?></h4>
                                <div class="fx-job-meta">
                                    <?= htmlspecialchars($job['category']) ?> &middot;
                                    <?= htmlspecialchars($job['location']) ?> &middot;
                                    Budget: Tk <?= number_format((float) $job['budget'], 2) ?>
                                </div>
                                <p style="margin:10px 0 0;color:var(--fx-text-muted);max-width:520px;">
                                    <?= htmlspecialchars($job['description']) ?>
                                </p>
                            </div>

                            <button type="button" class="fx-btn fx-apply-toggle" data-job-id="<?= (int) $job['id'] ?>">
                                Apply
                            </button>
                        </div>

                        <form action="/FixLine/index.php?action=provider_apply_job" method="POST"
                              class="fx-apply-form fx-card-solid" id="fx-apply-form-<?= (int) $job['id'] ?>" style="display:none;">
                            <input type="hidden" name="job_id" value="<?= (int) $job['id'] ?>">

                            <div class="fx-form-group">
                                <label for="service_id_<?= (int) $job['id'] ?>">Service You Will Provide</label>
                                <select id="service_id_<?= (int) $job['id'] ?>" name="service_id" class="fx-service-select" data-job-id="<?= (int) $job['id'] ?>" required>
                                    <option value="">Choose a service from your profile</option>
                                    <?php if (!empty($providerServices)): ?>
                                        <?php foreach ($providerServices as $service): ?>
                                            <option value="<?= (int) $service['id'] ?>" data-price="<?= htmlspecialchars((string) $service['price']) ?>">
                                                <?= htmlspecialchars($service['service_name']) ?> — Tk <?= number_format((float) $service['price'], 2) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="" disabled>No services added to your profile yet</option>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="fx-form-group">
                                <label for="cover_note_<?= (int) $job['id'] ?>">Why are you a good fit?</label>
                                <textarea id="cover_note_<?= (int) $job['id'] ?>" name="cover_note" required></textarea>
                            </div>

                            <div class="fx-form-group">
                                <label for="proposed_price_<?= (int) $job['id'] ?>">Service Cost (Tk)</label>
                                <input type="number" step="0.01" min="0" id="proposed_price_<?= (int) $job['id'] ?>" name="proposed_price" readonly required>
                            </div>

                            <button type="submit" name="apply_job" value="1" class="fx-btn">Submit Application</button>
                        </form>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <h2 style="color:#fff;">My Applications</h2>
            <table class="fx-table">
                <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Category</th>
                    <th>Service</th>
                    <th>Cost</th>
                    <th>Applied On</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($appliedJobs)): ?>
                    <tr><td colspan="6">You haven't applied to any jobs yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($appliedJobs as $app): ?>
                        <tr>
                            <td><?= htmlspecialchars($app['title']) ?></td>
                            <td><?= htmlspecialchars($app['category']) ?></td>
                            <td><?= htmlspecialchars($app['service_name'] ?? $app['title']) ?></td>
                            <td>Tk <?= number_format((float) $app['proposed_price'], 2) ?></td>
                            <td><?= htmlspecialchars(date('d M Y', strtotime($app['applied_at']))) ?></td>
                            <td><span class="fx-badge fx-badge-<?= htmlspecialchars($app['status']) ?>"><?= htmlspecialchars(ucfirst($app['status'])) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php include __DIR__ . '/partials/footer.php'; ?>
    </div>
</div>

<script>
    document.querySelectorAll('.fx-apply-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const form = document.getElementById('fx-apply-form-' + btn.dataset.jobId);
            form.style.display = (form.style.display === 'none') ? 'block' : 'none';
        });
    });

    document.querySelectorAll('.fx-service-select').forEach(function (select) {
        select.addEventListener('change', function () {
            const priceInput = document.getElementById('proposed_price_' + select.dataset.jobId);
            priceInput.value = select.selectedOptions[0]?.dataset.price || '';
        });
    });
</script>
</body>
</html>

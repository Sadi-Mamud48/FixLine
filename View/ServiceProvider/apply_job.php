<?php
// Expects: $provider, $jobs, $appliedJobs, $message — supplied by ServiceProviderController::applyJob()
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
                <div class="fx-card-solid">No open jobs matching your profession right now. Check back soon.</div>
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

                        <!-- Hidden application form, revealed by the button above -->
                        <form action="service_provider.php?action=apply_job" method="POST"
                              class="fx-apply-form fx-card-solid" id="fx-apply-form-<?= (int) $job['id'] ?>" style="display:none;">
                            <input type="hidden" name="job_id" value="<?= (int) $job['id'] ?>">

                            <div class="fx-form-group">
                                <label for="cover_note_<?= (int) $job['id'] ?>">Why are you a good fit?</label>
                                <textarea id="cover_note_<?= (int) $job['id'] ?>" name="cover_note" required></textarea>
                            </div>

                            <div class="fx-form-group">
                                <label for="proposed_price_<?= (int) $job['id'] ?>">Your Proposed Price (Tk)</label>
                                <input type="number" step="0.01" min="0" id="proposed_price_<?= (int) $job['id'] ?>" name="proposed_price">
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
                    <th>Proposed Price</th>
                    <th>Applied On</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($appliedJobs)): ?>
                    <tr><td colspan="5">You haven't applied to any jobs yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($appliedJobs as $app): ?>
                        <tr>
                            <td><?= htmlspecialchars($app['title']) ?></td>
                            <td><?= htmlspecialchars($app['category']) ?></td>
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
    // Small UI-only interaction: toggle each job's application form.
    // (Not AJAX — this just shows/hides a form that still submits as a normal POST.)
    document.querySelectorAll('.fx-apply-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const form = document.getElementById('fx-apply-form-' + btn.dataset.jobId);
            form.style.display = (form.style.display === 'none') ? 'block' : 'none';
        });
    });
</script>
</body>
</html>

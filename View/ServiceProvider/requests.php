<?php
// Expects: $provider, $incomingRequests, $message — supplied by ServiceProviderController::requests()
$activePage = 'requests';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FixLine — Service Requests</title>
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

            <h2 style="color:#fff;margin-top:0;">Incoming Service Requests</h2>

            <?php if (empty($incomingRequests)): ?>
                <div class="fx-card-solid">No service requests received yet.</div>
            <?php else: ?>
                <div class="fx-message-list" style="margin-bottom:36px;">
                    <?php foreach ($incomingRequests as $request): ?>
                        <div class="fx-message-card <?= ($request['status'] ?? 'new') === 'new' ? 'fx-message-unread' : '' ?>">
                            <div class="fx-message-header">
                                <strong><?= htmlspecialchars($request['customer_name'] ?? 'Customer') ?></strong>
                                <span><?= htmlspecialchars(date('d M Y, h:i A', strtotime($request['created_at']))) ?></span>
                            </div>

                            <h4><?= htmlspecialchars($request['title']) ?></h4>
                            <div class="fx-message-meta">
                                <?= htmlspecialchars($request['category']) ?> &middot;
                                <?= htmlspecialchars($request['location'] ?: 'Location not specified') ?> &middot;
                                Budget: Tk <?= number_format((float) ($request['budget'] ?? 0), 2) ?>
                            </div>

                            <p><?= nl2br(htmlspecialchars($request['description'])) ?></p>

                            <?php if (($request['status'] ?? 'new') === 'accepted'): ?>
                                <div class="fx-message-status"><?= ucfirst(htmlspecialchars($request['status'])) ?></div>
                                <form action="/FixLine/index.php?action=provider_requests" method="POST" style="display:inline; margin-top:12px;">
                                    <input type="hidden" name="request_id" value="<?= (int) $request['id'] ?>">
                                    <button type="submit" name="complete_service_request" value="1" class="fx-btn">Complete</button>
                                </form>
                            <?php elseif (($request['status'] ?? 'new') === 'rejected'): ?>
                                <div class="fx-message-status">Rejected</div>
                            <?php else: ?>
                                <div style="display:flex; gap:12px; flex-wrap:wrap;">
                                    <form action="/FixLine/index.php?action=provider_requests" method="POST" style="display:inline;">
                                        <input type="hidden" name="request_id" value="<?= (int) $request['id'] ?>">
                                        <button type="submit" name="accept_service_request" value="1" class="fx-btn">Accept</button>
                                    </form>

                                    <form action="/FixLine/index.php?action=provider_requests" method="POST" style="display:inline;">
                                        <input type="hidden" name="request_id" value="<?= (int) $request['id'] ?>">
                                        <button type="submit" name="reject_service_request" value="1" class="fx-btn fx-btn-outline">Reject</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php include __DIR__ . '/partials/footer.php'; ?>
    </div>
</div>
</body>
</html>

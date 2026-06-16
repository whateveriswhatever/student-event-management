<?php define("ASSET_URL", BASE_URL . "/public"); $activeLink = "memberships"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="My club memberships — Club&Event Seeker">
    <title>My Clubs — Club&amp;Event Seeker</title>
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/ClubPage.css">
    <style>
        .status-badge { display:inline-block;padding:0.2rem 0.65rem;border-radius:99px;font-size:0.72rem;font-weight:700;text-transform:uppercase; }
        .status-approval { background:#dcfce7;color:#15803d; }
        .status-pending  { background:#fef9c3;color:#a16207; }
        .status-rejected, .status-banned { background:#fee2e2;color:#b91c1c; }
        .status-left     { background:#f1f5f9;color:#64748b; }
        .empty-state { text-align:center;padding:5rem 0;color:var(--text-muted); }
        .role-tag { font-size:0.8rem;color:var(--text-muted);margin-top:0.25rem; }
    </style>
</head>
<body>
<?php require_once root_dir . "/app/views/layouts/navbar.php"; ?>

<div class="container">
    <div class="header-section">
        <h1 class="page-title">🏛️ My Club Memberships</h1>
    </div>

    <?php $memberships = $memberships ?? []; ?>

    <?php if (empty($memberships)): ?>
        <div class="empty-state">
            <p style="font-size:3rem;">🔍</p>
            <h2 style="margin:0.5rem 0;">No Memberships Yet</h2>
            <p>You haven't joined any clubs. <a href="<?= BASE_URL ?>/clubs" style="color:var(--primary);">Explore clubs</a> to get started!</p>
        </div>
    <?php else: ?>
        <div class="grid">
            <?php foreach ($memberships as $m): ?>
            <?php
                $status = $m->getStatus()->value;
                $statusClass = "status-" . str_replace(" ", "-", $status);
                $joinedAt = $m->getJoinedTimeline()->format("M d, Y");
            ?>
            <div class="card">
                <div>
                    <div class="card-header">
                        <h3 class="card-title">Club #<?= $m->getClubID() ?></h3>
                        <span class="status-badge <?= $statusClass ?>"><?= $status ?></span>
                    </div>
                    <p class="role-tag">Role ID: <?= $m->getRoleID() ?></p>
                </div>
                <div class="card-meta" style="margin-top:1rem;">
                    📅 Joined: <strong><?= $joinedAt ?></strong>
                </div>
                <?php if ($status === "approval"): ?>
                    <form action="<?= BASE_URL ?>/membership/update" method="POST" style="margin-top:0.75rem;">
                        <input type="hidden" name="membershipID" value="<?= $m->getID() ?>">
                        <input type="hidden" name="action" value="quit">
                        <button type="submit" class="btn" style="background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;" onclick="return confirm('Leave this club?')">Leave Club</button>
                    </form>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>

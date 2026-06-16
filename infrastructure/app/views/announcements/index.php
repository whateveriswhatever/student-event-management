<?php define("ASSET_URL", BASE_URL . "/public"); $activeLink = ""; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements — Club&amp;Event Seeker</title>
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/ClubPage.css">
    <style>
        .announcement-card { border-left:4px solid var(--primary); }
        .announcement-meta { font-size:0.8rem;color:var(--text-muted);margin-bottom:0.5rem; }
        .empty-state { text-align:center;padding:4rem;color:var(--text-muted); }
    </style>
</head>
<body>
<?php require_once root_dir . "/app/views/layouts/navbar.php"; ?>

<div class="container">
    <div class="header-section">
        <h1 class="page-title">📢 Announcements</h1>
        <?php if (isset($_SESSION["user_ID"])): ?>
        <a href="#post-form" class="btn btn-primary" style="width:auto;padding:0.5rem 1.25rem;text-decoration:none;">+ New Announcement</a>
        <?php endif; ?>
    </div>

    <?php $announcements = $announcements ?? []; ?>

    <?php if (empty($announcements)): ?>
        <div class="card empty-state">
            <p style="font-size:2.5rem;">📢</p>
            <h2>No Announcements</h2>
            <p>Nothing posted yet for this club.</p>
        </div>
    <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:1rem;max-width:720px;">
            <?php foreach ($announcements as $a): ?>
            <div class="card announcement-card">
                <div class="announcement-meta">📌 Club #<?= $a->getClubID() ?> &nbsp;·&nbsp; Author #<?= $a->getAuthorID() ?></div>
                <h3 style="font-size:1.1rem;font-weight:600;margin-bottom:0.4rem;"><?= htmlspecialchars($a->getTitle()) ?></h3>
                <p class="text-muted" style="height:auto;"><?= nl2br(htmlspecialchars($a->getDescription())) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION["user_ID"])): ?>
    <div id="post-form" style="max-width:720px;margin-top:2rem;">
        <div class="card">
            <h2 style="font-size:1.2rem;font-weight:600;margin-bottom:1.25rem;">Post New Announcement</h2>
            <form action="<?= BASE_URL ?>/announcements/create" method="POST">
                <input type="hidden" name="club_ID" value="<?= (int)($clubID ?? 0) ?>">
                <div style="margin-bottom:1rem;">
                    <label style="display:block;font-size:0.875rem;font-weight:600;margin-bottom:0.4rem;">Title</label>
                    <input type="text" name="title" class="search-bar" style="max-width:100%;" placeholder="Announcement title..." required>
                </div>
                <div style="margin-bottom:1rem;">
                    <label style="display:block;font-size:0.875rem;font-weight:600;margin-bottom:0.4rem;">Content</label>
                    <textarea name="description" style="width:100%;padding:0.65rem;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:0.95rem;resize:vertical;min-height:100px;" placeholder="Write your announcement..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Post Announcement</button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>
</body>
</html>

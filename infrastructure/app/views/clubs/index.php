<?php define("ASSET_URL", BASE_URL . "/public"); $activeLink = "clubs"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Discover student clubs — Club&Event Seeker">
    <title>Discover Clubs — Club&amp;Event Seeker</title>
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/ClubPage.css">
</head>
<body>
<?php require_once root_dir . "/app/views/layouts/navbar.php"; ?>

<div class="container">
    <div class="header-section">
        <h1 class="page-title">Explore Available Clubs</h1>
        <input type="text" id="club-search" class="search-bar" placeholder="Search by Club ID or Name...">
    </div>

    <?php if (isset($_GET["success"])): ?>
        <div style="background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;border-radius:var(--radius-sm);padding:0.75rem 1rem;margin-bottom:1.5rem;">
            ✅ Club created successfully!
        </div>
    <?php endif; ?>
    <?php if (isset($_GET["msg"])): ?>
        <div style="background:#fef9c3;color:#a16207;border:1px solid #fde68a;border-radius:var(--radius-sm);padding:0.75rem 1rem;margin-bottom:1.5rem;">
            <?= match($_GET["msg"]) {
                "applied_successfully" => "✅ Application submitted! Wait for approval.",
                "already_member"       => "ℹ️ You are already a member of this club.",
                default                => htmlspecialchars($_GET["msg"])
            } ?>
        </div>
    <?php endif; ?>

    <?php $clubs = $clubs ?? []; ?>

    <?php if (empty($clubs)): ?>
        <div style="text-align:center;padding:5rem 0;color:var(--text-muted);">
            <p style="font-size:3rem;">🏛️</p>
            <h2 style="margin:0.5rem 0;">No Clubs Yet</h2>
            <p>Be the first to <a href="<?= BASE_URL ?>/clubs/create" style="color:var(--primary);">create a club</a>!</p>
        </div>
    <?php else: ?>
        <div class="grid" id="club-list">
            <?php foreach ($clubs as $club): ?>
            <div class="card club-card"
                 data-club-name="<?= strtolower(htmlspecialchars($club->getName())) ?>"
                 data-club-id="<?= $club->getID() ?>">
                <div>
                    <div class="card-header">
                        <h3 class="card-title"><?= htmlspecialchars($club->getName()) ?></h3>
                        <span class="badge">ID: <?= $club->getID() ?></span>
                    </div>
                    <p class="text-muted"><?= htmlspecialchars($club->getDescription()) ?></p>
                </div>

                <div>
                    <div class="card-meta">
                        📅 <strong>Founded:</strong> <?= $club->getFoundedDate()->format('M d, Y') ?>
                    </div>
                    <?php if (isset($_SESSION["user_ID"])): ?>
                        <form action="<?= BASE_URL ?>/membership/join" method="POST">
                            <input type="hidden" name="club_ID" value="<?= $club->getID() ?>">
                            <button type="submit" class="btn btn-primary">Request to Join</button>
                        </form>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/login" class="btn btn-primary" style="text-decoration:none;text-align:center;display:block;">Log in to Join</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="<?= ASSET_URL ?>/assets/js/ClubPage.js"></script>
</body>
</html>
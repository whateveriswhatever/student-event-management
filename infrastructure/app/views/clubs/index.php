<?php define("ASSET_URL", "/final-project/infrastructure/public"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/ClubPage.css">
    <title>Document</title>
</head>


<body>
    <nav class="navbar">
        <a href="/clubs" class="nav-left">
            <div class="nav-logo">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5"/></svg>
            </div>
            <span class="app-title">Club&Event Seeker</span>
        </a>

        <div class="nav-middle">
            <a href="/clubs" class="nav-link active">Discover</a>
            <a href="/events" class="nav-link">Events</a>
            <a href="/student/memberships" class="nav-link">My Clubs</a>
            <a href="/profile" class="nav-link">Profile</a>
        </div>

        <div class="nav-right">
            <?php
                if (isset($_SESSION["user_ID"])):
            ?>
                <div class="profile-info">
                    <img src="https://api.dicebear.com/7.x/initials/svg?seed=<?= $_SESSION['user_ID'] ?? 'ST' ?>" alt="Avatar" class="avatar">
                    <span class="student-name"><?= htmlspecialchars($_SESSION['student_name'] ?? 'Active Student') ?></span>
                </div>
                <a href="/logout" class="logout-btn" title="Sign Out">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </a>
            <?php else: ?>
                <a href="/final-project/infrastructure/login" class="nav-link" style="font-weight: 600; color: var(--text-main);">Log in</a>
                <a href="/final-project/infrastructure/login#register" class="btn btn-primary" style="padding: 0.5rem 1.25rem;">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container">
        <div class="header-section">
            <h2 class="page-title">Explore Available Clubs</h2>
            <input type="text" id="club-search" class="search-bar" placeholder="Search by Club ID or Name...">
        </div>

        <?php $clubs = $clubs ?? []; ?>
        <div class="grid" id="club-list">
            <?php foreach ($clubs as $club): ?>
            <div class="card club-card" data-club-name="<?= strtolower($club->getName()) ?>" data-club-id="<?= $club->getID() ?>">
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
                    <form action="/membership/join" method="POST">
                        <input type="hidden" name="club_ID" value="<?= $club->getID() ?>">
                        <input type="hidden" name="student_ID" value="<?= $_SESSION['user_ID'] ?? '' ?>">
                        <button type="submit" class="btn btn-primary">Request to Join</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="<?= ASSET_URL ?>/public/js/ClubPage.js"></script>
</body>
</html>
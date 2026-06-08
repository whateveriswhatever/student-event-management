<?php
    // require_once root_dir . "/config/env-config.php";
    // $envLoader = new EnvLoader()
    define("ASSET_URL", "/final-project/infrastructure/public"); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/ClubPage.css">
    <title>Homepage</title>
</head>


<body>
    <?php
        if (isset($error)):
    ?>
        <div class="error-alert" style="background-color: #f8d7da; color: #721c24; padding: 15px; margin: 20px auto; max-width: 1200px; border-radius: 6px; font-weight: bold; border-left: 5px solid #dc3545; display: flex; align-items: center; gap: 10px;">
            <span>Alert</span>
            <span>Error saving club: <?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <?php
        if (isset($_GET["success"]) && $_GET["success"] === "registered"):
    ?>
        <div class="success-alert" style="background-color: #d1e7dd; color: #0f5132; padding: 15px; margin: 20px auto; max-width: 1200px; border-radius: 6px; font-weight: bold; border-left: 5px solid #198754; display: flex; align-items: center; gap: 10px;">
            <span>Registered successfully!</span>
        </div>
    <?php endif; ?>

    <nav class="navbar">
        <a href="<?= base_folder_path ?>/clubs" class="nav-left">
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
                    <a href="<?= base_folder_path ?>/profile" style="display: flex; justify-content: center; align-items: center; gap: 0.75rem; padding-right: 1.25rem;">
                        <img src="https://api.dicebear.com/7.x/initials/svg?seed=<?= $_SESSION['user_ID'] ?? 'ST' ?>" alt="Avatar" class="avatar">
                        <span class="student-name"><?= htmlspecialchars($_SESSION['userLastname'] ?? 'Active Student') ?></span>
                    </a>
                </div>

                <a href="<?= base_folder_path ?>/signout" class="logout-btn" title="Sign Out">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </a>
            <?php else: ?>
                <a href="<?= base_folder_path ?>/login" class="nav-link" style="font-weight: 600; color: var(--text-main);">Log in</a>
                <a href="<?= base_folder_path ?>/login#register" class="btn btn-primary" style="padding: 0.5rem 1.25rem;">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container">
        <div class="header-section">
            <h2 class="page-title">Explore Available Clubs</h2>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <div style="flex: 1; max-width: 400px; margin-right: 2rem;">
                    <input type="text" id="club-search" class="search-bar" placeholder="Search by Club ID or Name...">
                </div>
                
                <?php if (isset($_SESSION["user_ID"])): ?>
                    <div>
                        <a href="<?= base_folder_path ?>/clubs/create" class="btn btn-primary" style="text-decoration: none; padding: 0.75rem 1.5rem; display: inline-block;">
                            + Create New Club
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php 
            $clubs = $clubs ?? [];
            $userMemberships = $userMemberships ?? []; 
        ?>
        <div class="grid" id="club-list">
            <?php foreach ($clubs as $club): ?>
                <?php
                    $clubID = $club->getID();
                    $status = $userMemberships[$clubID] ?? null;
                    
                    // If the student is banned from the club, skip this iteration entirely. The club disappers.
                    if ($status === "banned") {
                        continue;
                    }
                ?>
                <div class="card club-card" data-club-name="<?= strtolower($club->getName()) ?>" data-club-id="<?= $club->getID() ?>">
                    <div>
                        <div class="card-header" style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
            
                            <?php 
                                // Get the logo value from your club model object
                                $logo = $club->getLogoURL(); 
                            ?>

                            <?php if (!empty($logo)): ?>
                                <img src="<?= htmlspecialchars($logo) ?>" 
                                alt="<?= htmlspecialchars($club->getName()) ?> Logo" 
                                style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; flex-shrink: 0;" />
                            <?php else: ?>
                                <div style="width: 50px; height: 50px; background-color: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 24px; border: 1px solid #e2e8f0; flex-shrink: 0;">🛡️</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 4px;">
                        <a href="<?= base_folder_path ?>/clubs/show?id=<?= $clubID ?>" style="text-decoration: none; color: inherit; hover: underline;">
                            <h3 class="card-title" style="margin: 0; font-size: 1.15rem;"><?= htmlspecialchars($club->getName()) ?></h3>
                        </a>
                        <span class="badge" style="align-self: flex-start;">ID: <?= $club->getID() ?></span>
                        <span class="badge" style="align-self: flex-start">Members: <?= $club->getTotalMembers() ?></span>
                    </div>

                    <p class="text-muted"><?= htmlspecialchars($club->getDescription()) ?></p>
            
                    <div>
                        <div class="card-meta">
                            📅 <strong>Founded:</strong> <?= $club->getFoundedDate()->format('M d, Y') ?>
                        </div>

                        <?php if (isset($_SESSION["user_ID"])): ?>
                            <?php if ($status === "active"): ?>
                                <button type="button" class="btn" style="background-color: #10b981; color: #fff; opacity: 0.8; cursor: not-allowed; width: 100%;" disabled>
                                    Joined
                                </button>
                            <?php elseif ($status === "pending"): ?>
                                <button type="button" class="btn" style="background-color: #6b7280; color: #fff; opacity: 0.8; cursor: wait; width: 100%;" disabled>
                                    Requested
                                </button>
                            <?php else: ?>
                                <form action="<?= base_folder_path ?>/clubs/register" method="POST">
                                    <input type="hidden" name="club_ID" value="<?= $club->getID() ?>">
                                    <?php if (isset($_SESSION["user_ID"])): ?>
                                        <button type="submit" class="btn btn-primary">Request to join</button>
                                    <?php else: ?>
                                        <a href="<?= base_folder_path ?>/login" class="btn btn-secondary" style="text-decoration: none; text-align: center; display: block;">
                                            Log in to join
                                        </a>
                                    <?php endif; ?>
                                </form>        
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="<?= ASSET_URL ?>/assets/js/ClubPage.js"></script>
</body>
</html>
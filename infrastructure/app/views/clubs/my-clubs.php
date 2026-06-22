<?php 
    define("ASSET_URL", base_folder_path . "/public"); 
    $activePage = 'my-clubs'; // For navbar highlighting if you added this link

    if (!isset($searchQuery)) {
        $searchQuery = "";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage My Clubs</title>
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/navbar.css" />
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/ClubPage.css" />
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/global.css" />
    <style>
        body { font-family: system-ui, sans-serif; background-color: #f8fafc; margin: 0; }
        .container { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
        
        /* Search Bar */
        .search-bar { display: flex; gap: 10px; margin-bottom: 30px; max-width: 410px;}
        .search-input { flex: 1; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 1rem; }
        .btn-search { background-color: #4f46e5; color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: bold; }
        
        /* Club Cards */
        .club-list { display: flex; flex-direction: column; gap: 16px; }
        .club-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
        .club-info { display: flex; align-items: center; gap: 16px; }
        .club-logo { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; background-color: #f1f5f9; }
        .club-title { margin: 0 0 4px 0; color: #1e293b; font-size: 1.25rem; }
        .club-meta { color: #64748b; font-size: 0.9rem; margin: 0; }
        
        /* Quit Button */
        .btn-quit { background-color: #ef4444; color: white; border: none; padding: 10px 16px; border-radius: 6px; cursor: pointer; font-weight: bold; transition: background 0.2s; }
        .btn-quit:hover { background-color: #dc2626; }
        
        /* Alerts */
        .alert-success { background-color: #d1e7dd; color: #0f5132; padding: 12px; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #198754; }
    </style>
</head>
<body>

    <?php require root_dir . "/app/views/partials/navbar.php"; ?>

    <div class="container">
        <h1 style="color: #1e293b; margin-bottom: 24px;">Manage My Clubs</h1>

        <?php if (isset($_GET['success']) && $_GET['success'] === 'quit'): ?>
            <div class="alert-success">You have successfully left the club.</div>
        <?php endif; ?>

        <form method="GET" action="<?= base_folder_path ?>/clubs/my-clubs" class="search-bar">
            <input type="text" name="search" class="search-input" placeholder="Search my clubs..." value="<?= htmlspecialchars($searchQuery) ?>">
            <div class="" style="display: flex; justify-content: center; align-items: space-between;">
                <button type="submit" class="btn-search" style="margin-right: 0.5rem;">Search</button>
                <?php if(!empty($searchQuery)): ?>
                    <button type="button" class="btn">
                        <a href="<?= base_folder_path ?>/clubs/my-clubs" style="align-self: center; color: #ef4444; text-decoration: none; font-weight: 500;">Clear</a>
                    </button>
                <?php endif; ?>
            </div>
        </form>

        <div class="club-list">
            <?php if (empty($joinedClubs)): ?>
                <div style="text-align: center; padding: 40px; color: #64748b; background: white; border-radius: 12px; border: 1px dashed #cbd5e1;">
                    <?php if(!empty($searchQuery)): ?>
                        No clubs found matching "<?= htmlspecialchars($searchQuery) ?>".
                    <?php else: ?>
                        You haven't joined any clubs yet. Go to Discover to find some!
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php foreach ($joinedClubs as $club): ?>
                    <div class="club-card">
                        <div class="club-info">
                            <img src="<?= $club->getLogoURL() ?: 'https://api.dicebear.com/7.x/initials/svg?seed=' . urlencode($club->getName()) ?>" alt="Logo" class="club-logo">
                            <div>
                                <a href="<?= base_folder_path ?>/clubs/show?id=<?= $club->getID() ?>" style="text-decoration: none;">
                                    <h3 class="club-title"><?= htmlspecialchars($club->getName()) ?></h3>
                                </a>
                                <p class="club-meta">👥 <?= $club->getTotalMembers() ?> Members</p>
                            </div>
                        </div>
                        
                        <form action="<?= base_folder_path ?>/my-clubs/quit" method="POST" onsubmit="return confirm('Are you sure you want to leave <?= htmlspecialchars(addslashes($club->getName())) ?>? You will have to request to join again later.');">
                            <input type="hidden" name="club_ID" value="<?= $club->getID() ?>">
                            <button type="submit" class="btn-quit">Quit Club</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
<?php
    define("ASSET_URL", base_folder_path . "/public"); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">   
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/ClubPage.css">
</head>


<body>
    <!-- <?php
        if (isset($error)):
    ?>
        <div class="error-alert" style="background-color: #f8d7da; color: #721c24; padding: 15px; margin: 20px auto; max-width: 1200px; border-radius: 6px; font-weight: bold; border-left: 5px solid #dc3545; display: flex; align-items: center; gap: 10px;">
            <span>Alert</span>
            <span>Error saving club: <?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?> -->

    <?php
        if (isset($_GET["success"]) && $_GET["success"] === "registered"):
    ?>
        <div class="success-alert" style="background-color: #d1e7dd; color: #0f5132; padding: 15px; margin: 20px auto; max-width: 1200px; border-radius: 6px; font-weight: bold; border-left: 5px solid #198754; display: flex; align-items: center; gap: 10px;">
            <span>Registered successfully!</span>
        </div>
    <?php endif; ?>

    <?php
        $activePage = "homepage";
        require_once root_dir . "/app/views/partials/navbar.php";
    ?>

    <?php 
        $clubs = $clubs ?? [];
        $userMemberships = $userMemberships ?? []; 
    ?>

    <div class="container">
        <!-- Header section -->
        <div class="clubs-header">
            <div class="header-row">
                <div class="header-info">
                    <h2 class="page-title">Explore</h2>
                    <p class="page-subtitle">
                        Discover communities, activities, and events on campus
                    </p>
                </div>

                <?php if (isset($_SESSION["user_ID"])): ?>
                    <button class="btn-primary create-btn">
                        <a href="<?= base_folder_path ?>/clubs/create"
                        style="text-decoration: none; color: #fff;">
                            + Create
                        </a>
                    </button>
                    
                <?php endif ?>
            </div>

            <form action="<?= base_folder_path ?>/clubs"
                method="GET" class="search-form">

                <input type="text" name="search_name" placeholder="Search by name"
                    value="<?= htmlspecialchars($_GET["search_name"] ?? "") ?>"
                    class="search-input" />

                <input type="number" name="search_ID" placeholder="Search by ID"
                    min="1" value="<?= htmlspecialchars($_GET["search_ID"] ?? '') ?>"
                    class="search-id" />

                <button type="submit" class="btn-primary search-btn">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                    
                <?php if (!empty($_GET["search_name"]) || !empty($_GET["search_ID"])): ?>
                    <a href="<?= base_folder_path ?>/clubs"
                        class="btn btn-secondary">
                        Clear
                    </a>
                <?php endif; ?>
            </form>

            <div class="club-count">
                Showing <?= count($clubs) ?> clubs
            </div>
        </div>

        
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
    <script src="https://kit.fontawesome.com/YOUR_KIT_CODE.js" crossorigin="anonymous"></script>

</body>
</html>
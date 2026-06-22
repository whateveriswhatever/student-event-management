<?php 
    define("ASSET_URL", base_folder_path . "/public"); 

    $student = $student ?? null;
    $friendshipStatus = $friendshipStatus ?? null;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($student->getFirstname()) ?>'s Profile</title>
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/Profile.css?v=<?= time() ?>" />
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/global.css" />
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/ClubPage.css" />
    <style>
        #friends-modal-overlay { display: none !important; opacity: 1 !important; }
        #friends-modal-overlay.active { display: flex !important; }
    </style>
</head>
<body>
    <?php $activePage = "friends"; require root_dir . "/app/views/partials/navbar.php"; ?>

    <div class="profile-container">
        <?php if ($student): ?>
        <div class="profile-card">
            <img class="profile-avatar-large"
                 src="https://api.dicebear.com/7.x/initials/svg?seed=<?= urlencode($student->getLastname()) ?>"
                 alt="Avatar">

            <div class="profile-details">
                <h2><?= htmlspecialchars($student->getFirstname() . ' ' . $student->getLastname()) ?></h2>
                <span class="student-id-tag">Student ID: <?= htmlspecialchars($student->getID()) ?></span>

                <div class="info-grid">
                    <div class="info-item"><strong>Email:</strong> <?= htmlspecialchars($student->getEmail()) ?></div>
                    <div class="info-item"><strong>Major:</strong> <?= $profile ? htmlspecialchars(ucwords($profile->getMajor())) : 'Not Specified' ?></div>
                </div>

                <!-- Friend action button -->
                <div style="margin-top: 1rem;">
                    <?php if ($friendshipStatus === "accepted"): ?>
                        <form action="<?= base_folder_path ?>/friends/unfriend" method="POST"
                              onsubmit="return confirm('Unfriend this person?')">
                            <input type="hidden" name="friend_ID" value="<?= htmlspecialchars($student->getID()) ?>">
                            <button style="background:#ef4444;color:#fff;border:none;padding:8px 20px;border-radius:8px;font-weight:600;cursor:pointer;">
                                Unfriend
                            </button>
                        </form>
                    <?php elseif ($friendshipStatus === "pending"): ?>
                        <button disabled style="background:#9ca3af;color:#fff;border:none;padding:8px 20px;border-radius:8px;font-weight:600;">
                            Request Pending
                        </button>
                    <?php else: ?>
                        <form action="<?= base_folder_path ?>/friends/add" method="POST">
                            <input type="hidden" name="receiver_ID" value="<?= htmlspecialchars($student->getID()) ?>">
                            <button style="background:#3b82f6;color:#fff;border:none;padding:8px 20px;border-radius:8px;font-weight:600;cursor:pointer;">
                                + Add Friend
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2.5rem;">
            <div>
                <h3 class="section-title">👥 Clubs</h3>
                <?php if (!empty($joinedClubs)): ?>
                    <?php foreach ($joinedClubs as $club): ?>
                        <div class="card" style="padding: 1rem; margin-bottom: 1rem;">
                            <strong><?= htmlspecialchars($club->getName()) ?></strong>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">Not in any clubs yet.</div>
                <?php endif; ?>
            </div>

            <div>
                <h3 class="section-title">📅 Events</h3>
                <?php if (!empty($joinedEvents)): ?>
                    <?php foreach ($joinedEvents as $event): ?>
                        <div class="card" style="padding: 1rem; margin-bottom: 1rem;">
                            <strong><?= htmlspecialchars($event->getTitle()) ?></strong>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">No events registered yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</body>
</html>
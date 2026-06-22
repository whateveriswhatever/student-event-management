<?php 
    define("ASSET_URL", base_folder_path . "/public");

    // Ensure $student is defined to avoid undefined variable errors in views.
    if (!isset($student)) {
        $student = new class {
            public function getFirstname() { return 'Guest'; }
            public function getLastname() { return 'User'; }
            public function getID() { return ''; }
            public function getEmail() { return ''; }
            public function getPhoneNumber() { return ''; }
            public function getAge() { return ''; }
        };
    }

    // Ensure $profile is defined to avoid undefined variable errors in views.
    if (!isset($profile)) {
        $profile = null;
    }

    // if (!isset($calendarJSON)) {
    //     echo "<div>Registered events weren't passed into the calendar!</div>";
    // } else {
    //     echo "<div>Registered events were passed into the calendar!</div>";
    // }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/Profile.css?v=<?= time() ?>" />
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/global.css" />
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/ClubPage.css">
    <style>
                /* Clickable Grid Card Action Styling */
        .clickable-card {
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.2s ease;
        }
        .clickable-card:hover {
            background-color: #f1f5f9 !important;
            transform: translateY(-2px);
        }

        /* Modal Pop-up Window Blurry Backdrop Wrapper */
        .modal-overlay {
            display: none; /* Hidden state by default */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(15, 23, 42, 0.4); /* Smooth charcoal fade backdrop overlay */
            backdrop-filter: blur(8px); /* Real-time Gaussian blur effect onto page background */
            -webkit-backdrop-filter: blur(8px); /* Safari support fallback */
            z-index: 9999; /* Ensure stack alignment is above the calendar */
            justify-content: center;
            align-items: center;
        }

        /* Trigger state toggled via JS script */
        .modal-overlay.active {
            display: flex;
        }

        /* Centered Pop-up Modal Window Content layout */
        .friends-modal {
            background: #ffffff;
            width: 90%;
            max-width: 480px;
            max-height: 75vh;
            border-radius: 14px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            animation: zoomInModal 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes zoomInModal {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .modal-header {
            padding: 16px 20px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            margin: 0;
            color: #1e293b;
            font-size: 1.15rem;
            font-weight: 600;
        }

        .close-modal-btn {
            background: none;
            border: none;
            font-size: 1.6rem;
            color: #94a3b8;
            cursor: pointer;
            transition: color 0.2s;
        }
        .close-modal-btn:hover {
            color: #334155;
        }

        .modal-body {
            padding: 10px 20px 20px 20px;
            overflow-y: auto;
        }

        /* Individual Friendship Row Entries */
        .friend-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .friend-row:last-child {
            border-bottom: none;
        }

        .modal-friend-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 14px;
            background-color: #cbd5e1;
        }

        .modal-friend-info {
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .friend-fullname {
            font-weight: 600;
            color: #0f172a;
            font-size: 0.95rem;
        }

        .friend-username {
            font-size: 0.8rem;
            color: #64748b;
        }

        /* Unfriend Red Button Class */
        .btn-unfriend {
            background-color: #ef4444;
            color: #ffffff;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-unfriend:hover {
            background-color: #dc2626;
        }

        .no-friends-text {
            text-align: center;
            color: #64748b;
            padding: 24px 0;
            font-size: 0.9rem;
        }

        #friends-modal-overlay {
            display: none !important;
            opacity: 1 !important;
            transition: none !important;
        }

        #friends-modal-overlay.active {
            display: flex !important;
        }

        
    </style>
    <title>Profile</title>
</head>
<body>
    <?php
        $activePage = "profile";
        require root_dir . "/app/views/partials/navbar.php"; 
    ?>

    <div class="profile-container">
        
        <div class="profile-card">
            <div>
                <img class="profile-avatar-large" src="https://api.dicebear.com/7.x/initials/svg?seed=<?= urlencode($student->getLastname()) ?>" alt="Profile Avatar">
            </div>
            <div class="profile-details">
                <h2><?= htmlspecialchars($student->getFirstname() . ' ' . $student->getLastname()) ?></h2>
                <span class="student-id-tag">Student ID: <?= htmlspecialchars($student->getID()) ?></span>
                
                <div class="info-grid">
                    <div class="info-item"><strong>Email:</strong> <?= htmlspecialchars($student->getEmail()) ?></div>
                    <div class="info-item"><strong>Phone:</strong> <?= htmlspecialchars($student->getPhoneNumber()) ?></div>
                    <div class="info-item"><strong>Age:</strong> <?= htmlspecialchars($student->getAge()) ?> years old</div>
                    <div class="info-item"><strong>Major:</strong> <?= $profile ? htmlspecialchars(ucwords($profile->getMajor())) : 'Not Specified' ?></div>
                    <div class="info-item info-card clickable-card" id="open-friends-modal">
                        <strong>Friends</strong>
                        <p><?= count($friends ?? []) ?> connection<?= count($friends ?? []) > 1 ? 's' : '' ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2.5rem;">
            
            <div>
                <h3 class="section-title">👥 My Joined Clubs</h3>
                <?php if (!empty($joinedClubs)): ?>
                    <div class="grid" style="grid-template-columns: 1fr; gap: 1rem;">
                        <?php foreach ($joinedClubs as $club): ?>
                            <div class="card" style="padding: 1rem;">
                                <strong><?= htmlspecialchars($club->getName()) ?></strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">You haven't joined any clubs yet.</div>
                <?php endif; ?>
            </div>

            <div>
                <h3 class="section-title">📅 Registered Events</h3>
                <?php if (!empty($joinedEvents)): ?>
                    <div class="grid" style="grid-template-columns: 1fr; gap: 1rem;">
                        <?php foreach ($joinedEvents as $event): ?>
                            <div class="card" style="padding: 1rem;">
                                <strong><?= htmlspecialchars($event->getTitle()) ?></strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">You haven't signed up for any events yet.</div>
                <?php endif; ?>
            </div>

        </div>

        <div style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); margin-top: 20px;">
            <h2 style="font-size: 1.5rem; color: #1e293b; margin-bottom: 16px;">📅 My Schedule</h2>
    
            <div id="schedule-calendar"></div>
        </div>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <!-- <script src="<?= ASSET_URL ?>/js/Profile.js"></script> -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            var calendarEl = document.getElementById("schedule-calendar");
            var eventData = <?= json_encode($calendarJSON ?? [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: "dayGridMonth",                    // Default view is monthly grid
                headerToolbar: {
                    left: "prev,next today",                    // Navigation button
                    center: "title",                            // Display "Month Year"
                    right: "dayGridMonth,timeGridWeek,listWeek" // View toggle buttons
                },
                height: 600,
                events: eventData,

                // Making it look nice when hovering over events
                eventMouseEnter: (info) => {
                    info.el.style.cursor = "pointer";
                    info.el.style.opacity = "0.8";
                },
                eventMouseLeave: (info) => {
                    info.el.style.opacity = "1";
                }
            });

            // Rendering it to the screen
            calendar.render();
        });
    </script>


    <div class="modal-overlay" id="friends-modal-overlay">
        <div class="friends-modal">
            <div class="modal-header">
                <h3>My Friends</h3>
                <button class="close-modal-btn" id="close-friends-modal">&times;</button>
            </div>
            <div class="modal-body">
                <?php if (!empty($friends)): ?>
                    <?php foreach ($friends as $friend): ?>
                        <div class="friend-row">
                            <a href="<?= base_folder_path ?>/profile/view?id=<?= htmlspecialchars($friend->getID()); ?>" style="display: flex; text-decoration: none; cursor: pointer;">
                                <img src="https://api.dicebear.com/7.x/initials/svg?seed=<?= $friend->getLastname(); ?>" alt="Avatar" class="modal-friend-avatar">
                                <div class="modal-friend-info">
                                    <span class="friend-fullname"><?= htmlspecialchars($friend->getFirstname() . ' ' . $friend->getLastname()) ?></span>
                                    <span class="friend-username">@<?= htmlspecialchars($friend->getID()) ?></span>
                                </div>
                            </a>
                            
                        
                            
                        
                            <form action="<?= base_folder_path ?>/friends/unfriend" method="POST" onsubmit="return confirm('Are you sure you want to unfriend this classmate?');">
                                <input type="hidden" name="friend_ID" value="<?= htmlspecialchars($friend->getID()) ?>">
                                <button type="submit" class="btn-unfriend">Unfriend</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-friends-text">You haven't added any connections yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="<?= ASSET_URL ?>/assets/js/FriendListPopUpWindow.js?v=<?= time() ?>"></script>
     
</body>
</html>
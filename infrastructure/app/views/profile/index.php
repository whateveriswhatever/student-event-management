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
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/Profile.css" />
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/global.css" />
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/ClubPage.css">
    <title>Profile</title>
</head>
<body>
    
    <!-- <nav class="navbar">
        <a href="<?= base_folder_path ?>/clubs" class="nav-left" style="text-decoration: none;">
            <div class="nav-logo">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5"/></svg>
            </div>
            <span class="app-title">C&B Hub</span>
        </a>
        <div class="nav-middle">
            <a href="<?= base_folder_path ?>/clubs" class="nav-link">Discover</a>
            <a href="#" class="nav-link">Events</a>
        </div>
        <div class="nav-right">
            <a href="<?= base_folder_path ?>/logout" class="btn" style="color: #ef4444;">Logout</a>
        </div>
    </nav> -->
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
                    <div class="info-item">📧 <strong>Email:</strong> <?= htmlspecialchars($student->getEmail()) ?></div>
                    <div class="info-item">📱 <strong>Phone:</strong> <?= htmlspecialchars($student->getPhoneNumber()) ?></div>
                    <div class="info-item">🎂 <strong>Age:</strong> <?= htmlspecialchars($student->getAge()) ?> years old</div>
                    <div class="info-item">🎓 <strong>Major:</strong> <?= $profile ? htmlspecialchars(ucwords($profile->getMajor())) : 'Not Specified' ?></div>
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
</body>
</html>
<?php 
    define("ASSET_URL", base_folder_path . "/public"); 
    $activePage = 'events'; 

    if (!isset($userJoinedClubIDs)) {
        $userJoinedClubIDs = [];
    }

    if (!isset($searchQuery)) {
        $searchQuery = "";
    }

    if (!isset($studentID)) {
        $studentID = null;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Exploerer</title>
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/ClubPage.css" />
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/global.css" />
    <style>
        body { font-family: system-ui, sans-serif; background-color: #f8fafc; margin: 0; }
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .search-bar { display: flex; gap: 10px; margin-bottom: 30px; max-width: 410px;}
        .search-input { flex: 1; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 1rem; }
        .btn-search { background-color: #4f46e5; color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: bold; }
        
        .events-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .event-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; display: flex; flex-direction: column; }
        .event-title { margin: 0 0 10px 0; color: #1e293b; font-size: 1.25rem; }
        .event-meta { color: #64748b; font-size: 0.9rem; margin-bottom: 15px; }
        .badge-private { background-color: #fef08a; color: #854d0e; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; display: inline-block; margin-bottom: 10px; }
        .btn-join { margin-top: auto; text-align: center; background-color: #10b981; color: white; padding: 10px; border-radius: 6px; text-decoration: none; font-weight: bold; display: block; }
        .btn-join.disabled { background-color: #cbd5e1; cursor: not-allowed; }
    </style>
</head>
<body>

    <?php require root_dir . "/app/views/partials/navbar.php"; ?>

    <div class="container">
        <h1 style="color: #1e293b; margin-bottom: 20px;">Upcoming Events</h1>

        <form method="GET" action="<?= base_folder_path ?>/events" class="search-bar">
            <input type="text" name="search" class="search-input" placeholder="Search events by name..." value="<?= htmlspecialchars($searchQuery) ?>">
            <div class="" style="display: flex; justify-content: center; align-items: space-between;">
                <button type="submit" class="btn-search" style="margin-right: 0.5rem;">Search</button>
                <?php if(!empty($searchQuery)): ?>
                    <button type="button" class="btn">
                        <a href="<?= base_folder_path ?>/events" style="align-self: center; color: #ef4444; text-decoration: none; font-weight: 500;">Clear</a>
                    </button>
                <?php endif; ?>
            </div> 
        </form>

        <div class="events-grid">
            <?php if (empty($events)): ?>
                <p>No events found.</p>
            <?php else: ?>
                <?php foreach ($events as $component): ?>
                    <?php 
                        // --- CORE PRIVACY LOGIC ---
                        $event = $component[0];
                        $wasRegistered = $component[1];
                        $isPrivate = $event->getPrivacyMode() ?? false;
                        $clubID = $event->getClubID();
                        $isMember = in_array($clubID, $userJoinedClubIDs);
                        $isFull = $event->getCurrParticipants() >= $event->getMaxParticipants();

                        // If the event is private and the logged-in user is NOT a member, skip rendering it entirely
                        if ($isPrivate && !$isMember) {
                            continue; 
                        }
                    ?>
                    
                    <div class="event-card">
                        <?php if ($isPrivate): ?>
                            <div><span class="badge-private">🔒 Private (Members Only)</span></div>
                        <?php endif; ?>
                        
                        <h3 class="event-title"><?= htmlspecialchars($event->getTitle()) ?></h3>
                        
                        <div class="event-meta">
                            <div>📅 <?= $event->getEventDate()->format('F j, Y') ?></div>
                            <div>⏰ <?= $event->getStartTime()->format('g:i A') ?> - <?= $event->getEndTime()->format('g:i A') ?></div>
                            <div>👥 <?= $event->getCurrParticipants() ?> / <?= $event->getMaxParticipants() ?> Joined</div>
                        </div>
                        
                        <p style="color: #475569; font-size: 0.95rem; margin-bottom: 20px; line-height: 1.5;">
                            <?= htmlspecialchars(substr($event->getDescription(), 0, 100)) ?>...
                        </p>

                        <?php if ($studentID): ?>
                            <?php if ($event->getCurrParticipants() >= $event->getMaxParticipants()): ?>
                                <button class="btn-join disabled" disabled>Event Full</button>
                            <?php else: ?>
                                <form action="<?= base_folder_path ?>/events/register" method=\"POST\" style="margin-top: auto;">
                                    <input type="hidden" name="event_ID" value="<?= $event->getID() ?>">
                                    <input type="hidden" name="student_ID" value="<?= $studentID ?>">
                                    
                                    <?php if ($wasRegistered): ?>
                                        <button type="button" class="btn-join"
                                        style="width: 100%; border: none; cursor: pointer;">Registered</button>
                                    <?php elseif ($isFull): ?>
                                        <button type="button" style="width: 100%; background-color: #ef4444; color: white; padding: 10px; border: none; border-radius: 6px; cursor: not-allowed; opacity: 0.7;" disabled>
                                            🚫 Filled (Max Exceeded)
                                        </button>
                                    <?php elseif (($event->getStatus())->value !== "open"): ?>
                                        <button type="button" style="width: 100%; background-color: #6b7280; color: white; padding: 10px; border: none; border-radius: 6px; cursor: not-allowed;" disabled>
                                            Registration Closed
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" class="btn-join" style="width: 100%; border: none; cursor: pointer;">Register Now</button>
                                    <?php endif ?>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="<?= base_folder_path ?>/login" class="btn-join" style="background-color: #64748b;">Log in to Join</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
>>>>>>> master

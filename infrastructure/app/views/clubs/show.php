<?php 
    define("ASSET_URL", base_folder_path . "/public"); 

    if (!isset($club)) {
        die('Error: Club data not available.');
    }
    
    if (!isset($isMember)) {
        $isMember = false;
    }
    
    if (!isset($events)) {
        $events = [];
    }
    if (!isset($eventAddressMapper)) {
        $eventAddressMapper = [];
    }

    if (!isset($members)) {
        die("Error: Data of club members are not available!");
    }

    function accquireExecutives(array $members): array {
        $executives = [];
        if (empty($members)) return [];
        foreach ($members as $memberData) {
            $role = $memberData["role"];
            $title = ($role->getTitle())->value;
            // echo "<div>{$title}</div>";
            if ($title === "president") {
                $executives[] = $memberData;
            }
        }
        return $executives;
    }

    $executives = accquireExecutives($members);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($club->getName()) ?> - Club Details</title>
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/ClubPage.css">
</head>
<body>
    <div style="max-width: 1200px; margin: 40px auto; padding: 0 20px;">
        <a href="/final-project/infrastructure/clubs" style="text-decoration: none; color: #4f46e5; font-weight: 500;">← Back to Clubs</a>
        
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); margin-top: 20px; display: flex; gap: 24px; align-items: center;">
            <div style="font-size: 48px;">
                <img class="profile-avatar-large" src="<?= htmlspecialchars($club->getLogoURL()) ?>"
                alt="<?= htmlspecialchars($club->getName()) ?>"
                style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; flex-shrink: 0;" />
            </div>
            <div>
                <h1 style="margin: 0 0 8px 0; font-size: 2rem; color: #1e293b;"><?= htmlspecialchars($club->getName()) ?></h1>
                <p style="color: #64748b; margin: 0 0 12px 0; max-width: 700px;"><?= htmlspecialchars($club->getDescription()) ?></p>
                <span class="badge">📅 Founded: <?= $club->getFoundedDate()->format('M d, Y') ?></span>
                <span class="badge member-count-badge"
                        style="align-self: flex-start; cursor: pointer;"
                        data-club-id="<?= $club->getID() ?>"
                        data-club-name="<?= htmlspecialchars($club->getName()) ?>">
                    👥 Members: <?= $club->getTotalMembers() ?>
                </span>
            </div>
        </div>

        <div style="margin-top: 40px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="font-size: 1.5rem; color: #1e293b; margin-bottom: 20px;">📅 Club Events</h2>

                <?php if (isset($currentUserRole) && in_array($currentUserRole, ["president", "vice president", "vp"])): ?>
                    <button id="open-create-event-btn" style="background-color: #10b981; color: #fff; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
                        + Create Event
                    </button>
                <?php endif; ?>
            </div>
            

            <?php if (!$isMember): ?>
                <div style="background-color: #f8fafc; border: 2px dashed #cbd5e1; padding: 40px; text-align: center; border-radius: 12px;">
                    <p style="color: #64748b; font-size: 1.1rem; margin: 0 0 16px 0;">🔒 Events are exclusive to active club members.</p>
                    <p style="color: #94a3b8; font-size: 0.95rem; margin: 0;">Please request to join this club from the discovery dashboard to view or register into events.</p>
                </div>
            <?php else: ?>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 16px; border-radius: 8px; margin-bottom: 24px;">
                    <form action="/final-project/infrastructure/clubs/show" method="GET" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
                        <input type="hidden" name="id" value="<?= $club->getID() ?>">
                
                        <div style="flex: 1; min-width: 200px;">
                            <label style="display: block; font-size: 0.85rem; color: #475569; margin-bottom: 4px; font-weight: 600;">From Date</label>
                            <input type="date" name="start_date" value="<?= htmlspecialchars($filterStart ?? '') ?>" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>
                
                        <div style="flex: 1; min-width: 200px;">
                            <label style="display: block; font-size: 0.85rem; color: #475569; margin-bottom: 4px; font-weight: 600;">To Date</label>
                            <input type="date" name="end_date" value="<?= htmlspecialchars($filterEnd ?? '') ?>" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>
                
                        <div style="display: flex; gap: 8px;">
                            <button type="submit" style="background-color: #3b82f6; color: white; padding: 9px 16px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">Filter</button>
                            <a href="/final-project/infrastructure/clubs/show?id=<?= $club->getID() ?>" style="background-color: #e2e8f0; color: #475569; padding: 9px 16px; border-radius: 6px; text-decoration: none; font-weight: 600; display: inline-block;">Clear</a>
                        </div>
                    </form>
                </div>
                <?php if (empty($events)): ?>
                    <div style="color: #64748b; padding: 20px;">No events are currently scheduled for this club.</div>
                <?php else: ?>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 20px;">
                        <?php foreach ($events as $pair): ?>
                            <?php
                                $event = $pair[0];
                                $wasRegistered = $pair[1];
                                $isFull = $event->getCurrParticipants() >= $event->getMaxParticipants(); 
                            ?>
                            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                                <div>
                                    <h3 style="margin: 0 0 12px 0; color: #0f172a; font-size: 1.2rem;"><?= htmlspecialchars($event->getTitle()) ?></h3>
                                    
                                    <div style="display: flex; flex-direction: column; gap: 8px; font-size: 0.9rem; color: #475569; margin-bottom: 16px;">
                                        <div>📆 <strong>Date:</strong> <?= $event->getEventDate()->format('M d, Y') ?></div>
                                        <div>⏰ <strong>Time:</strong> <?= $event->getStartTime()->format('H:i') ?> - <?= $event->getEndTime()->format('H:i') ?></div>
                                        <?php
                                            $locationID = $event->getLocationID();
                                            $addressString = $eventAddressMapper[$locationID] ?? "Unknown Location";
                                        ?>
                                        <div>📍 <strong>Location:</strong> <?= htmlspecialchars($addressString) ?> </div>
                                        <div>👥 <strong>Capacity:</strong> <?= $event->getCurrParticipants() ?> / <?= $event->getMaxParticipants() ?> Joined</div>
                                        <div>Status: <span style="font-weight: 600; text-transform: uppercase; font-size: 0.8rem; color: <?= $event->getStatus()->value === 'open' ? '#10b981' : '#ef4444' ?>"><?= htmlspecialchars($event->getStatus()->value) ?></span></div>
                                    </div>
                                </div>

                                <form action="/final-project/infrastructure/events/register" method="POST">
                                    <input type="hidden" name="event_ID" value="<?= $event->getID() ?>">
                                    <input type="hidden" name="student_ID" value="<?= $_SESSION['user_ID'] ?>">
                                    
                                    <?php if ($isFull): ?>
                                        <button type="button" style="width: 100%; background-color: #ef4444; color: white; padding: 10px; border: none; border-radius: 6px; cursor: not-allowed; opacity: 0.7;" disabled>
                                            🚫 Filled (Max Exceeded)
                                        </button>
                                    <?php elseif ($event->getStatus()->value !== 'open'): ?>
                                        <button type="button" style="width: 100%; background-color: #6b7280; color: white; padding: 10px; border: none; border-radius: 6px; cursor: not-allowed;" disabled>
                                            Registration Closed
                                        </button>
                                    <?php elseif ($wasRegistered === true): ?>
                                        <button type="button" style="width: 100%; background-color: #008000; color: #fff; padding: 10px; border: none; border-radius: 6px; cursor: not-allowed; opacity: 0.7" disabled>
                                            Registered
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" style="width: 100%; background-color: #4f46e5; color: white; padding: 10px; border: none; border-radius: 6px; cursor: pointer; font-weight: 500; transition: background 0.2s;" onmouseover="this.style.backgroundColor='#4338ca'" onmouseout="this.style.backgroundColor='#4f46e5'">
                                            Register for Event
                                        </button>
                                    <?php endif; ?>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            <?php endif; ?>
        </div>

        <div style="margin-top: 50px; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <h2 style="font-size: 1.4rem; color: #1e293b; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                👥 Club Members (<?= count($members ?? []) ?>)
            </h2>
            
            <?php if (empty($executives)): ?>
                <p style="color: #64748b; font-style: italic; margin: 0;">No active members found in this club.</p>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px;">
                    <?php foreach ($executives as $memberData): ?>
                        <?php 
                            $student = $memberData['student'];
                            $role = $memberData['role'];
                            
                            // Safe parsing for Student Details (Handles both Object and Raw Array types gracefully)
                            $fullName = 'Unknown Student';
                            $dispID = 'N/A';
                            
                            if (is_object($student)) {
                                $firstName = method_exists($student, 'getFirstname') ? $student->getFirstname() : '';
                                $lastName = method_exists($student, 'getLastname') ? $student->getLastname() : '';
                                $fullName = trim($firstName . ' ' . $lastName);
                                if (empty($fullName) && method_exists($student, 'getName')) {
                                    $fullName = $student->getName();
                                }
                                $dispID = method_exists($student, 'getID') ? $student->getID() : 'N/A';
                            } elseif (is_array($student)) {
                                $fullName = trim(($student['firstname'] ?? '') . ' ' . ($student['lastname'] ?? ''));
                                $dispID = $student['ID'] ?? 'N/A';
                            }
                            if (empty($fullName) && $dispID !== 'N/A') $fullName = "Student #" . $dispID;
                            
                            // Safe parsing for Role Details
                            $roleName = 'member';
                            if ($role && is_object($role) && method_exists($role, 'getTitle')) {
                                $roleName = $role->getTitle()->value; // Retrieves 'president', 'member', etc.
                            }
                            
                            // Style badges matching distinct club roles
                            $badgeColor = '#4b5563';
                            $badgeBg = '#f3f4f6';
                            if (strtolower($roleName) === 'president') {
                                $badgeColor = '#7c3aed';
                                $badgeBg = '#f3e8ff';
                            } elseif (in_array(strtolower($roleName), ['vice president', 'vp'])) {
                                $badgeColor = '#2563eb';
                                $badgeBg = '#dbeafe';
                            } elseif (strtolower($roleName) === 'secretary') {
                                $badgeColor = '#059669';
                                $badgeBg = '#d1fae5';
                            }
                        ?>
                        
                        <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; display: flex; align-items: center; gap: 12px; background: #f8fafc;">
                            <div style="width: 42px; height: 42px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; font-weight: bold; color: #475569; flex-shrink: 0;">
                                <?= htmlspecialchars(mb_substr($fullName, 0, 1)) ?>
                            </div>
                            
                            <div style="display: flex; flex-direction: column; gap: 2px; overflow: hidden;">
                                <span style="font-weight: 600; color: #1e293b; font-size: 0.95rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;" title="<?= htmlspecialchars($fullName) ?>">
                                    <?= htmlspecialchars($fullName) ?>
                                </span>
                                <span style="font-size: 0.8rem; color: #64748b;">
                                    ID: <?= htmlspecialchars($dispID) ?>
                                </span>
                                <span style="align-self: flex-start; margin-top: 4px; font-size: 0.72rem; font-weight: 600; padding: 2px 8px; border-radius: 9999px; text-transform: capitalize; color: <?= $badgeColor ?>; background-color: <?= $badgeBg ?>;">
                                    <?= htmlspecialchars($roleName) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div id="members-modal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-club-name">Club Members</h3>
                <button id="close-modal-btn" class="close-btn">&times;</button>
            </div>
            <div id="modal-members-list" class="modal-body">
                <p>Loading members...</p>
            </div>
        </div>
    </div>

    <div id="create-event-modal" class="modal-overlay" style="display: none;">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h3 style="margin: 0;">Create New Event</h3>
                <button id="close-event-modal-btn" class="close-btn">&times;</button>
            </div>
            
            <div class="modal-body">
                <form action="/final-project/infrastructure/events/create" method="POST">
                    <input type="hidden" name="club_ID" value="<?= $club->getID() ?>">
                    
                    <div style="margin-bottom: 12px;">
                        <label style="display: block; margin-bottom: 4px; font-size: 0.9rem; font-weight: 500;">Event Title</label>
                        <input type="text" name="title" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    </div>

                    <div style="margin-bottom: 12px;">
                        <label style="display: block; margin-bottom: 4px; font-size: 0.9rem; font-weight: 500;">Description</label>
                        <textarea name="description" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px; min-height: 80px; resize: vertical;"></textarea>
                    </div>

                    <div style="margin-bottom: 16px; padding: 12px; border: 1px dashed #cbd5e1; border-radius: 8px; background-color: #f8fafc;">
                        <h4 style="margin: 0 0 10px 0; font-size: 0.95rem; color: #334155;">Location Details</h4>
                        <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                            <div style="flex: 1;">
                                <label style="display: block; margin-bottom: 4px; font-size: 0.85rem; font-weight: 500;">Building</label>
                                <input type="text" name="location_building" required placeholder="e.g. Science Building" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px;">
                            </div>
                            <div style="flex: 1;">
                                <label style="display: block; margin-bottom: 4px; font-size: 0.85rem; font-weight: 500;">Room</label>
                                <input type="text" name="location_room" required placeholder="e.g. 104A" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px;">
                            </div>
                            <div style="flex: 1;">
                                <label style="display: block; margin-bottom: 4px; font-size: 0.85rem; font-weight: 500;">Room Capacity</label>
                                <input type="number" name="location_capacity" min="10" required placeholder="e.g. 50" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px;">
                            </div>
                        </div>
                    </div>

                    <div style="margin-bottom: 12px;">
                        <label style="display: block; margin-bottom: 4px; font-size: 0.9rem; font-weight: 500;">Event Date</label>
                        <input type="date" name="event_date" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    </div>

                    <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                        <div style="flex: 1;">
                            <label style="display: block; margin-bottom: 4px; font-size: 0.9rem; font-weight: 500;">Start Time</label>
                            <input type="time" name="start_time" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>
                        <div style="flex: 1;">
                            <label style="display: block; margin-bottom: 4px; font-size: 0.9rem; font-weight: 500;">End Time</label>
                            <input type="time" name="end_time" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 4px; font-size: 0.9rem; font-weight: 500;">Max Participants</label>
                        <input type="number" name="max_participants" min="1" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    </div>

                    <button type="submit" style="width: 100%; background-color: #4f46e5; color: white; padding: 10px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">Save Event</button>
                </form>
            </div>
        </div>
    </div>
    
    <script src="<?= ASSET_URL ?>/assets/js/ClubDetailPage.js?v=<?= time() ?>"></script>
</body>
</html>
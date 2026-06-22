<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <div class="container">
        <h2>Admin Dashboard (Club President)</h2>
    
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 1.5rem;">
            <div class="card">
                <h3>Pending Join Requests</h3>
                <?php $pendingRequests = $pendingRequests ?? []; ?>
                <ul id="request-list" style="list-style: none; margin-top: 1rem;">
                    <?php foreach ($pendingRequests as $req): ?>
                    <li style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; border-bottom: 1px solid var(--border);">
                        <div>
                            <strong><?= $req->getStudentName() ?></strong> (ID: <?= $req->getStudentID() ?>)
                            <br><small class="text-muted">Applied: <?= $req->getJoinedTimeline()->format('Y-m-d') ?></small>
                        </div>
                        <form action="/membership/update" method="POST" style="display: flex; gap: 0.5rem;">
                            <input type="hidden" name="membership_ID" value="<?= $req->getID() ?>">
                            <button type="submit" name="action" value="approve" class="btn btn-success" style="padding: 0.25rem 0.5rem;">Accept</button>
                            <button type="submit" name="action" value="reject" class="btn btn-danger" style="padding: 0.25rem 0.5rem;">Reject</button>
                        </form>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="card">
                <h3>Create New Event</h3>
                <form action="/events/create" method="POST" style="margin-top: 1rem;">
                    <div class="form-group"><label>Event Title</label><input type="text" name="title" required></div>
                    <div class="form-group"><label>Description</label><textarea name="description" rows="3" required></textarea></div>
                    <div class="form-group"><label>Date</label><input type="date" name="event_date" required></div>
                
                    <div style="display: flex; gap: 1rem;">
                        <div class="form-group"><label>Start Time</label><input type="time" name="start_time" required></div>
                        <div class="form-group"><label>End Time</label><input type="time" name="end_time" required></div>
                    </div>

                    <div style="display: flex; gap: 1rem;">
                        <div class="form-group"><label>Location ID</label><input type="number" name="location_ID" required></div>
                        <div class="form-group"><label>Max Participants</label><input type="number" name="max_participants" required></div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Create Event</button>
                </form>
            </div>
        </div>
    </div>
    <script src="../../../public/assets/js/script.js"></script>
</body>
</html>
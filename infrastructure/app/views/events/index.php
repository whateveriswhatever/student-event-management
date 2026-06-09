<?php define("ASSET_URL", "/final-project/infrastructure/public"); $activeLink = "events"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Browse upcoming student events — Club&Event Seeker">
    <title>Events — Club&amp;Event Seeker</title>
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/ClubPage.css">
    <style>
        .status-open    { background:#dcfce7;color:#15803d; }
        .status-pending { background:#fef9c3;color:#a16207; }
        .status-closed  { background:#fee2e2;color:#b91c1c; }
        .status-void    { background:#f1f5f9;color:#64748b; }
        .status-badge { display:inline-block;padding:0.2rem 0.65rem;border-radius:99px;font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.04em; }
        .event-date { font-size:0.85rem;color:var(--text-muted);margin-bottom:0.4rem; }
        .empty-state { text-align:center;padding:5rem 0;color:var(--text-muted); }
    </style>
</head>
<body>
<?php require_once root_dir . "/app/views/layouts/navbar.php"; ?>

<div class="container">
    <div class="header-section">
        <h1 class="page-title">📅 Upcoming Events</h1>
        <input type="text" id="event-search" class="search-bar" placeholder="Search events by name...">
    </div>

    <?php if (isset($error)): ?>
        <div style="background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;border-radius:var(--radius-sm);padding:0.75rem 1rem;margin-bottom:1.5rem;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php $events = $events ?? []; ?>

    <?php if (empty($events)): ?>
        <div class="empty-state">
            <p style="font-size:3rem;">🗓️</p>
            <h2 style="margin:0.5rem 0;">No Events Yet</h2>
            <p>Check back later for upcoming club events.</p>
        </div>
    <?php else: ?>
        <div class="grid" id="event-list">
            <?php foreach ($events as $row): ?>
            <?php
                // $row là array thô từ DB vì EventRepository::all() chưa hydrate
                $title       = htmlspecialchars($row["title"] ?? "Untitled Event");
                $description = htmlspecialchars($row["description"] ?? "");
                $status      = $row["status"] ?? "pending";
                $eventDate   = $row["event_date"] ? date("M d, Y", strtotime($row["event_date"])) : "TBD";
                $eventID     = (int)($row["ID"] ?? 0);
                $maxP        = (int)($row["max_participants"] ?? 0);
                $statusClass = "status-" . $status;
            ?>
            <div class="card" data-event-title="<?= strtolower($title) ?>">
                <div>
                    <div class="card-header">
                        <h3 class="card-title"><?= $title ?></h3>
                        <span class="status-badge <?= $statusClass ?>"><?= $status ?></span>
                    </div>
                    <p class="event-date">📅 <?= $eventDate ?></p>
                    <p class="text-muted"><?= $description ?></p>
                </div>
                <div>
                    <div class="card-meta">👥 Max Participants: <strong><?= $maxP ?: "Unlimited" ?></strong></div>
                    <?php if (isset($_SESSION["user_ID"]) && $status === "open"): ?>
                        <form action="/final-project/infrastructure/events/register" method="POST">
                            <input type="hidden" name="event_ID" value="<?= $eventID ?>">
                            <button type="submit" class="btn btn-primary">Register</button>
                        </form>
                    <?php elseif ($status !== "open"): ?>
                        <button class="btn" style="background:#f1f5f9;color:var(--text-muted);cursor:not-allowed;" disabled>
                            <?= $status === "closed" ? "Registration Closed" : ucfirst($status) ?>
                        </button>
                    <?php else: ?>
                        <a href="/final-project/infrastructure/login" class="btn btn-primary" style="text-decoration:none;text-align:center;display:block;">Log in to Register</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
document.getElementById("event-search")?.addEventListener("input", function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll(".card[data-event-title]").forEach(card => {
        card.style.display = card.dataset.eventTitle.includes(q) ? "" : "none";
    });
});
</script>
</body>
</html>

<?php define("ASSET_URL", BASE_URL . "/public"); $activeLink = "clubs"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Club detail page — Club&Event Seeker">
    <title><?= htmlspecialchars($club?->getName() ?? 'Club Detail') ?> — Club&amp;Event Seeker</title>
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/ClubPage.css">
    <style>
        .detail-container { max-width: 860px; margin: 2.5rem auto; padding: 0 1.5rem; }
        .detail-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-md); padding: 2rem; box-shadow: var(--shadow); }
        .detail-header { display: flex; gap: 1.5rem; align-items: flex-start; margin-bottom: 1.75rem; }
        .club-logo { width: 80px; height: 80px; border-radius: var(--radius-md); object-fit: cover; border: 2px solid var(--border); background: #f1f5f9; }
        .club-logo-placeholder { width: 80px; height: 80px; border-radius: var(--radius-md); background: linear-gradient(135deg,#2563eb,#7c3aed); display:flex;align-items:center;justify-content:center;color:#fff;font-size:2rem;font-weight:700; }
        .detail-title { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.4rem; }
        .status-badge { display:inline-block;padding:0.25rem 0.75rem;border-radius:99px;font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em; }
        .status-active { background:#dcfce7;color:#15803d; }
        .status-low { background:#fef9c3;color:#a16207; }
        .status-closed { background:#fee2e2;color:#b91c1c; }
        .meta-row { display:flex;gap:2rem;margin:1rem 0;flex-wrap:wrap; }
        .meta-item { font-size:0.9rem;color:var(--text-muted); }
        .meta-item strong { color:var(--text-main); }
        .description-text { color:var(--text-muted);line-height:1.7;margin:1.25rem 0; }
        .action-row { display:flex;gap:0.75rem;margin-top:1.75rem;padding-top:1.25rem;border-top:1px solid var(--border); }
        .btn-outline { background:transparent;border:1.5px solid var(--primary);color:var(--primary); }
        .btn-outline:hover { background:#eff6ff; }
        .not-found { text-align:center;padding:4rem 0;color:var(--text-muted); }
    </style>
</head>
<body>
<?php require_once root_dir . "/app/views/layouts/navbar.php"; ?>

<div class="detail-container">
    <?php if (!isset($club) || $club === null): ?>
        <div class="detail-card not-found">
            <p style="font-size:3rem;">🔍</p>
            <h2>Club Not Found</h2>
            <p>The club you're looking for doesn't exist or has been removed.</p>
            <a href="<?= BASE_URL ?>/clubs" class="btn btn-primary" style="width:auto;padding:0.6rem 1.5rem;margin-top:1.5rem;display:inline-block;text-decoration:none;">← Back to Clubs</a>
        </div>
    <?php else: ?>
        <div class="detail-card">
            <div class="detail-header">
                <?php if ($club->getLogoURL()): ?>
                    <img src="<?= htmlspecialchars($club->getLogoURL()) ?>" alt="Club Logo" class="club-logo">
                <?php else: ?>
                    <div class="club-logo-placeholder"><?= strtoupper(substr($club->getName(), 0, 1)) ?></div>
                <?php endif; ?>
                <div>
                    <h1 class="detail-title"><?= htmlspecialchars($club->getName()) ?></h1>
                    <?php
                        $statusClass = match($club->getStatus()->value) {
                            'active' => 'status-active',
                            'low'    => 'status-low',
                            default  => 'status-closed'
                        };
                    ?>
                    <span class="status-badge <?= $statusClass ?>"><?= $club->getStatus()->value ?></span>
                </div>
            </div>

            <div class="meta-row">
                <div class="meta-item">📅 <strong>Founded:</strong> <?= $club->getFoundedDate()->format('F d, Y') ?></div>
                <div class="meta-item">🆔 <strong>Club ID:</strong> <?= $club->getID() ?></div>
            </div>

            <p class="description-text"><?= nl2br(htmlspecialchars($club->getDescription())) ?></p>

            <div class="action-row">
                <a href="<?= BASE_URL ?>/clubs" class="btn btn-outline" style="width:auto;padding:0.6rem 1.5rem;text-decoration:none;text-align:center;">← Back</a>
                <?php if (isset($_SESSION["user_ID"])): ?>
                    <form action="<?= BASE_URL ?>/membership/join" method="POST" style="flex:1;">
                        <input type="hidden" name="club_ID" value="<?= $club->getID() ?>">
                        <button type="submit" class="btn btn-primary">Request to Join</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>

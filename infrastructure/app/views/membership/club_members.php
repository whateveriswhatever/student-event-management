<?php define("ASSET_URL", BASE_URL . "/public"); $activeLink = ""; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club Members — Club&amp;Event Seeker</title>
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/ClubPage.css">
    <style>
        .table-card { background:#fff;border:1px solid var(--border);border-radius:var(--radius-md);overflow:hidden;box-shadow:var(--shadow); }
        table { width:100%;border-collapse:collapse; }
        th,td { padding:0.85rem 1.25rem;text-align:left;font-size:0.9rem; }
        th { background:#f8fafc;font-weight:600;color:var(--text-main);border-bottom:1px solid var(--border); }
        tr:not(:last-child) td { border-bottom:1px solid var(--border); }
        tr:hover td { background:#f8fafc; }
        .status-badge { display:inline-block;padding:0.2rem 0.65rem;border-radius:99px;font-size:0.72rem;font-weight:700;text-transform:uppercase; }
        .status-approval { background:#dcfce7;color:#15803d; }
        .status-pending  { background:#fef9c3;color:#a16207; }
        .status-rejected,.status-banned { background:#fee2e2;color:#b91c1c; }
        .status-left { background:#f1f5f9;color:#64748b; }
        .empty-state { text-align:center;padding:4rem;color:var(--text-muted); }
    </style>
</head>
<body>
<?php require_once root_dir . "/app/views/layouts/navbar.php"; ?>

<div class="container">
    <div class="header-section">
        <h1 class="page-title">👥 Club Members</h1>
        <a href="<?= BASE_URL ?>/clubs" class="btn btn-primary" style="width:auto;padding:0.5rem 1.25rem;text-decoration:none;">← Back to Clubs</a>
    </div>

    <?php $memberships = $memberships ?? []; ?>

    <?php if (empty($memberships)): ?>
        <div class="table-card empty-state">
            <p style="font-size:2.5rem;">👥</p>
            <h2>No Members Yet</h2>
            <p>This club has no members yet.</p>
        </div>
    <?php else: ?>
        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student ID</th>
                        <th>Role ID</th>
                        <th>Status</th>
                        <th>Joined At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($memberships as $i => $m): ?>
                    <?php
                        $status = $m->getStatus()->value;
                        $statusClass = "status-" . $status;
                    ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><strong><?= htmlspecialchars((string)$m->getStudentID()) ?></strong></td>
                        <td><?= $m->getRoleID() ?></td>
                        <td><span class="status-badge <?= $statusClass ?>"><?= $status ?></span></td>
                        <td><?= $m->getJoinedTimeline()->format("M d, Y") ?></td>
                        <td>
                            <?php if ($status === "pending"): ?>
                            <form action="<?= BASE_URL ?>/membership/update" method="POST" style="display:inline;">
                                <input type="hidden" name="membershipID" value="<?= $m->getID() ?>">
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="btn btn-primary" style="width:auto;padding:0.3rem 0.75rem;font-size:0.8rem;">Approve</button>
                            </form>
                            <form action="<?= BASE_URL ?>/membership/update" method="POST" style="display:inline;margin-left:0.4rem;">
                                <input type="hidden" name="membershipID" value="<?= $m->getID() ?>">
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="btn" style="width:auto;padding:0.3rem 0.75rem;font-size:0.8rem;background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;">Reject</button>
                            </form>
                            <?php else: ?>
                                <span style="color:var(--text-muted);font-size:0.85rem;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
</body>
</html>

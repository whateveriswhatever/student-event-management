<?php define("ASSET_URL", "/final-project/infrastructure/public"); $activeLink = ""; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locations — Club&amp;Event Seeker</title>
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/ClubPage.css">
    <style>
        .table-card { background:#fff;border:1px solid var(--border);border-radius:var(--radius-md);overflow:hidden;box-shadow:var(--shadow); }
        table { width:100%;border-collapse:collapse; }
        th,td { padding:0.85rem 1.25rem;text-align:left;font-size:0.9rem; }
        th { background:#f8fafc;font-weight:600;color:var(--text-main);border-bottom:1px solid var(--border); }
        tr:not(:last-child) td { border-bottom:1px solid var(--border); }
        tr:hover td { background:#f8fafc; }
        .add-form { max-width:480px;background:#fff;border:1px solid var(--border);border-radius:var(--radius-md);padding:1.5rem;box-shadow:var(--shadow);margin-top:2rem; }
        .form-group { margin-bottom:1rem; }
        .form-label { display:block;font-size:0.875rem;font-weight:600;margin-bottom:0.4rem; }
        .form-input { width:100%;padding:0.6rem 0.9rem;font-size:0.95rem;border:1px solid var(--border);border-radius:var(--radius-sm);outline:none;transition:var(--transition); }
        .form-input:focus { border-color:var(--primary);box-shadow:0 0 0 3px rgba(37,99,235,0.12); }
        .alert-error { background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;border-radius:var(--radius-sm);padding:0.75rem 1rem;margin-bottom:1rem;font-size:0.9rem; }
    </style>
</head>
<body>
<?php require_once root_dir . "/app/views/layouts/navbar.php"; ?>

<div class="container">
    <div class="header-section">
        <h1 class="page-title">📍 Locations</h1>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php $locations = $locations ?? []; ?>

    <div class="table-card">
        <?php if (empty($locations)): ?>
            <p style="padding:2rem;color:var(--text-muted);text-align:center;">No locations found. Add one below.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr><th>#</th><th>Building</th><th>Room</th><th>Capacity</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($locations as $i => $loc): ?>
                    <?php
                        // $loc có thể là Location object hoặc array raw
                        $building = htmlspecialchars(is_array($loc) ? $loc["building"] : $loc->getBuilding());
                        $room     = htmlspecialchars(is_array($loc) ? $loc["room"] : $loc->getRoom());
                        $capacity = is_array($loc) ? $loc["attendance_capacity"] : $loc->getCapacity();
                    ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><strong><?= $building ?></strong></td>
                        <td><?= $room ?></td>
                        <td><?= number_format($capacity) ?> people</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="add-form">
        <h2 style="font-size:1.15rem;font-weight:600;margin-bottom:1.25rem;">Add New Location</h2>
        <form action="/final-project/infrastructure/locations/create" method="POST">
            <div class="form-group">
                <label class="form-label">Building</label>
                <input type="text" name="building" class="form-input" placeholder="e.g. Block A" required>
            </div>
            <div class="form-group">
                <label class="form-label">Room</label>
                <input type="text" name="room" class="form-input" placeholder="e.g. 101" maxlength="4" required>
            </div>
            <div class="form-group">
                <label class="form-label">Capacity</label>
                <input type="number" name="capacity" class="form-input" placeholder="Minimum 10" min="10" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Location</button>
        </form>
    </div>
</div>
</body>
</html>

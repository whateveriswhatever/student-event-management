<?php define("ASSET_URL", BASE_URL . "/public"); $activeLink = ""; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>400 Bad Request — Club&amp;Event Seeker</title>
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/ClubPage.css">
</head>
<body>
<?php require_once root_dir . "/app/views/layouts/navbar.php"; ?>
<div style="text-align:center;padding:6rem 1.5rem;color:var(--text-muted);">
    <p style="font-size:4rem;line-height:1;">⚠️</p>
    <h1 style="font-size:2rem;font-weight:700;color:var(--text-main);margin:0.75rem 0;">400 — Bad Request</h1>
    <p style="font-size:1rem;max-width:420px;margin:0 auto 2rem;">
        <?= htmlspecialchars($message ?? "The request was invalid or missing required information.") ?>
    </p>
    <a href="<?= BASE_URL ?>/clubs" style="text-decoration:none;display:inline-block;" class="btn btn-primary" style="width:auto;padding:0.65rem 1.5rem;">← Go Home</a>
</div>
</body>
</html>

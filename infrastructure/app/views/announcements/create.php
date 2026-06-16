<?php define("ASSET_URL", BASE_URL . "/public"); $activeLink = ""; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Post a new announcement — Club&Event Seeker">
    <title>New Announcement — Club&amp;Event Seeker</title>
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/ClubPage.css">
    <style>
        .form-container { max-width: 640px; margin: 2.5rem auto; padding: 0 1.5rem; }
        .form-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-md); padding: 2rem; box-shadow: var(--shadow); }
        .form-title { font-size: 1.4rem; font-weight: 700; margin-bottom: 1.5rem; }
        .form-group { margin-bottom: 1.1rem; }
        .form-label { display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.4rem; }
        .form-input, .form-textarea {
            width: 100%; padding: 0.65rem 0.9rem; font-size: 0.95rem;
            border: 1px solid var(--border); border-radius: var(--radius-sm);
            outline: none; transition: var(--transition);
        }
        .form-textarea { resize: vertical; min-height: 120px; }
        .form-input:focus, .form-textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
        }
        .alert-error { background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;border-radius:var(--radius-sm);padding:0.75rem 1rem;margin-bottom:1.25rem;font-size:0.9rem; }
    </style>
</head>
<body>
<?php require_once root_dir . "/app/views/layouts/navbar.php"; ?>

<div class="form-container">
    <div class="form-card">
        <h1 class="form-title">📢 New Announcement</h1>

        <?php if (isset($error)): ?>
            <div class="alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/announcements/create" method="POST">
            <input type="hidden" name="club_ID" value="<?= (int)($clubID ?? 0) ?>">

            <div class="form-group">
                <label class="form-label" for="ann-title">Title <span style="color:#dc2626">*</span></label>
                <input type="text" id="ann-title" name="title" class="form-input"
                       placeholder="Announcement title..." maxlength="55" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="ann-desc">Content <span style="color:#dc2626">*</span></label>
                <textarea id="ann-desc" name="description" class="form-textarea"
                          placeholder="Write your announcement content..." maxlength="1000" required></textarea>
            </div>

            <div style="display:flex;gap:0.75rem;margin-top:1.5rem;">
                <a href="<?= BASE_URL ?>/announcements?club_ID=<?= (int)($clubID ?? 0) ?>"
                   class="btn" style="background:#f1f5f9;color:var(--text-main);width:auto;padding:0.6rem 1.25rem;text-decoration:none;text-align:center;">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary" style="flex:1;">Post Announcement</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>

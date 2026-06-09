<?php define("ASSET_URL", "/final-project/infrastructure/public"); $activeLink = "clubs"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Create a new student club on Club&Event Seeker">
    <title>Create Club — Club&amp;Event Seeker</title>
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/ClubPage.css">
    <style>
        .form-container { max-width: 640px; margin: 2.5rem auto; padding: 0 1.5rem; }
        .form-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-md); padding: 2rem; box-shadow: var(--shadow); }
        .form-title { font-size: 1.5rem; font-weight: 700; margin-bottom: 1.75rem; color: var(--text-main); }
        .form-group { margin-bottom: 1.25rem; }
        .form-label { display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-main); margin-bottom: 0.4rem; }
        .form-input, .form-select, .form-textarea {
            width: 100%; padding: 0.65rem 0.9rem; font-size: 0.95rem;
            border: 1px solid var(--border); border-radius: var(--radius-sm);
            background: #fff; outline: none; transition: var(--transition);
        }
        .form-textarea { resize: vertical; min-height: 100px; }
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
        }
        .form-actions { display: flex; gap: 0.75rem; margin-top: 1.75rem; }
        .btn-secondary { background: #f1f5f9; color: var(--text-main); }
        .btn-secondary:hover { background: #e2e8f0; }
        .alert-error { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; border-radius: var(--radius-sm); padding: 0.75rem 1rem; margin-bottom: 1.25rem; font-size: 0.9rem; }
    </style>
</head>
<body>
<?php require_once root_dir . "/app/views/layouts/navbar.php"; ?>

<div class="form-container">
    <div class="form-card">
        <h1 class="form-title">🏛️ Create New Club</h1>

        <?php if (isset($error)): ?>
            <div class="alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="/final-project/infrastructure/clubs/create" method="POST" id="create-club-form">
            <div class="form-group">
                <label class="form-label" for="name">Club Name <span style="color:#dc2626">*</span></label>
                <input type="text" id="name" name="name" class="form-input"
                       placeholder="e.g. Photography Club" maxlength="55" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description <span style="color:#dc2626">*</span></label>
                <textarea id="description" name="description" class="form-textarea"
                          placeholder="Briefly describe your club's purpose and activities..." maxlength="555" required></textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="logo_url">Logo URL</label>
                <input type="url" id="logo_url" name="logo_url" class="form-input"
                       placeholder="https://example.com/logo.png">
            </div>

            <div style="display:flex;gap:1rem;">
                <div class="form-group" style="flex:1;">
                    <label class="form-label" for="founded_date">Founded Date</label>
                    <input type="date" id="founded_date" name="founded_date" class="form-input"
                           value="<?= date('Y-m-d') ?>">
                </div>
                <div class="form-group" style="flex:1;">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="active" selected>Active</option>
                        <option value="low">Low Activity</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <a href="/final-project/infrastructure/clubs" class="btn btn-secondary" style="width:auto;padding:0.6rem 1.5rem;text-decoration:none;text-align:center;">Cancel</a>
                <button type="submit" class="btn btn-primary" style="flex:1;">Create Club</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>

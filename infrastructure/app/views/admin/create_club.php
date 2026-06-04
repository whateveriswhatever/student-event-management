<?php define("ASSET_URL", "/final-project/infrastructure/public"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Create Club</title>
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/LoginRegister.css" />
    <style>
        .admin-container { max-width: 600px; margin: 40px auto; padding: 20px; }
        .form-textarea { width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px; resize: vertical; min-height: 100px; }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="auth-header">
            <h1 class="auth-title">Create a New Club</h1>
            <p>Admin Control Panel</p>
        </div>

        <div class="card">
            <?php if (isset($error)): ?>
                <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center; font-weight: bold;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="/final-project/infrastructure/admin/create/club" method="POST" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label class="form-label">Club Name</label>
                    <input type="text" name="name" class="form-input" required placeholder="e.g., Chess Club">
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-textarea" required placeholder="What is this club about?"></textarea>
                </div>

                <div class="form-group" style="display: flex; gap: 1rem;">
                    <div style="flex: 1;">
                        <label class="form-label">Founded Date</label>
                        <input type="date" name="founded_date" class="form-input" required>
                    </div>
                    <div style="flex: 1;">
                        <label class="form-label">Initial Status</label>
                        <select name="status" class="form-select" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Club Logo (Image)</label>
                    <input type="file" name="logo_image" accept="image/png, image/jpeg, image/jpg" class="form-input">
                    <small style="color: #666; display: block; margin-top: 5px;">Upload a .png or .jpg file.</small>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top: 1rem; width: 100%; padding: 0.75rem;">
                    Create Club
                </button>
            </form>
        </div>
    </div>
</body>
</html>
<?php define("ASSET_URL", BASE_URL . "/public"); $activeLink = ""; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="All students — Club&Event Seeker Admin">
    <title>Students — Club&amp;Event Seeker</title>
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/ClubPage.css">
    <style>
        .table-card { background:#fff;border:1px solid var(--border);border-radius:var(--radius-md);overflow:hidden;box-shadow:var(--shadow); }
        table { width:100%;border-collapse:collapse; }
        th,td { padding:0.85rem 1.25rem;text-align:left;font-size:0.9rem; }
        th { background:#f8fafc;font-weight:600;color:var(--text-main);border-bottom:1px solid var(--border); }
        tr:not(:last-child) td { border-bottom:1px solid var(--border); }
        tr:hover td { background:#f8fafc; }
        .empty-state { text-align:center;padding:4rem;color:var(--text-muted); }
    </style>
</head>
<body>
<?php require_once root_dir . "/app/views/layouts/navbar.php"; ?>

<div class="container">
    <div class="header-section">
        <h1 class="page-title">🎓 All Students</h1>
        <input type="text" id="student-search" class="search-bar" placeholder="Search by ID or name...">
    </div>

    <?php $students = $students ?? []; ?>

    <?php if (empty($students)): ?>
        <div class="table-card empty-state">
            <p style="font-size:2.5rem;">🎓</p>
            <h2>No Students Found</h2>
        </div>
    <?php else: ?>
        <div class="table-card">
            <table id="student-table">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Age</th>
                        <th>Email</th>
                        <th>Phone</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $s): ?>
                    <?php
                        // $s là array thô từ DB vì StudentRepository::all() trả raw rows
                        $sid   = htmlspecialchars($s["ID"] ?? "");
                        $fname = htmlspecialchars($s["firstname"] ?? "");
                        $lname = htmlspecialchars($s["lastname"] ?? "");
                        $age   = $s["age"] ?? "—";
                        $email = htmlspecialchars($s["email"] ?? "");
                        $phone = htmlspecialchars($s["phone_number"] ?? "");
                    ?>
                    <tr data-search="<?= strtolower("$sid $fname $lname") ?>">
                        <td><strong><?= $sid ?></strong></td>
                        <td><?= $fname ?></td>
                        <td><?= $lname ?></td>
                        <td><?= $age ?></td>
                        <td><?= $email ?></td>
                        <td><?= $phone ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
document.getElementById("student-search")?.addEventListener("input", function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll("#student-table tbody tr").forEach(row => {
        row.style.display = row.dataset.search.includes(q) ? "" : "none";
    });
});
</script>
</body>
</html>

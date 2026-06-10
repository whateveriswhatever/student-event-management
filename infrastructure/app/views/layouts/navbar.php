<?php
// Shared navbar component - include vào mọi view
// Dùng: $activeLink = "clubs" | "events" | "memberships" | ...
$activeLink = $activeLink ?? "";
?>
<nav class="navbar">
    <a href="<?= BASE_URL ?>/clubs" class="nav-left">
        <div class="nav-logo">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                <path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5"/>
            </svg>
        </div>
        <span class="app-title">Club&amp;Event Seeker</span>
    </a>

    <div class="nav-middle">
        <a href="<?= BASE_URL ?>/clubs"              class="nav-link <?= $activeLink === 'clubs'       ? 'active' : '' ?>">Discover</a>
        <a href="<?= BASE_URL ?>/events"             class="nav-link <?= $activeLink === 'events'      ? 'active' : '' ?>">Events</a>
        <a href="<?= BASE_URL ?>/student/memberships" class="nav-link <?= $activeLink === 'memberships' ? 'active' : '' ?>">My Clubs</a>
    </div>

    <div class="nav-right">
        <?php if (isset($_SESSION["user_ID"])): ?>
            <div class="profile-info">
                <img src="https://api.dicebear.com/7.x/initials/svg?seed=<?= htmlspecialchars($_SESSION['user_ID'] ?? 'ST') ?>"
                     alt="Avatar" class="avatar">
                <span class="student-name"><?= htmlspecialchars($_SESSION['userLastname'] ?? 'Student') ?></span>
            </div>
            <a href="<?= BASE_URL ?>/signout" class="logout-btn" title="Sign Out">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
            </a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/login" class="nav-link" style="font-weight:600;color:var(--text-main);">Log in</a>
            <a href="<?= BASE_URL ?>/login#register" class="btn btn-primary" style="padding:0.5rem 1.25rem;width:auto;">Register</a>
        <?php endif; ?>
    </div>
</nav>

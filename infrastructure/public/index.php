<?php
define("root_dir", dirname(__DIR__));

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}

$stats = [
    ["label" => "Sự kiện đang mở", "value" => "18", "trend" => "+4 tuần này"],
    ["label" => "Sinh viên tham gia", "value" => "1.248", "trend" => "+12% so với tháng trước"],
    ["label" => "CLB hoạt động", "value" => "32", "trend" => "6 CLB nổi bật"],
    ["label" => "Tỉ lệ check-in", "value" => "86%", "trend" => "+8% sau QR"],
];

$events = [
    [
        "title" => "Tech Talk: AI trong sản phẩm sinh viên",
        "club" => "IT Innovation Club",
        "date" => "21 Th05",
        "time" => "18:00 - 20:00",
        "location" => "Nhà A2 - P.304",
        "status" => "open",
        "capacity" => 180,
        "registered" => 142,
        "image" => "https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&w=900&q=80",
    ],
    [
        "title" => "Campus Music Night",
        "club" => "Melody Society",
        "date" => "24 Th05",
        "time" => "19:30 - 22:00",
        "location" => "Sân trung tâm",
        "status" => "open",
        "capacity" => 520,
        "registered" => 488,
        "image" => "https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?auto=format&fit=crop&w=900&q=80",
    ],
    [
        "title" => "Workshop: Thiết kế CV chuyên nghiệp",
        "club" => "Career Hub",
        "date" => "27 Th05",
        "time" => "14:00 - 16:30",
        "location" => "Hội trường B",
        "status" => "pending",
        "capacity" => 120,
        "registered" => 63,
        "image" => "https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&w=900&q=80",
    ],
    [
        "title" => "Green Campus Cleanup",
        "club" => "Volunteer Network",
        "date" => "29 Th05",
        "time" => "07:00 - 10:00",
        "location" => "Khuôn viên phía Đông",
        "status" => "closed",
        "capacity" => 80,
        "registered" => 80,
        "image" => "https://images.unsplash.com/photo-1559027615-cd4628902d4a?auto=format&fit=crop&w=900&q=80",
    ],
];

$clubs = [
    ["name" => "IT Innovation Club", "members" => 186, "status" => "active", "focus" => "AI, Web, Product"],
    ["name" => "Melody Society", "members" => 94, "status" => "active", "focus" => "Band, Vocal, Stage"],
    ["name" => "Career Hub", "members" => 212, "status" => "active", "focus" => "CV, Interview, Jobs"],
    ["name" => "Volunteer Network", "members" => 147, "status" => "low", "focus" => "Community, Campus"],
];

$timeline = [
    ["time" => "08:30", "title" => "Duyệt 12 đơn đăng ký mới", "meta" => "IT Innovation Club"],
    ["time" => "10:00", "title" => "Xác nhận phòng cho Workshop CV", "meta" => "Career Hub"],
    ["time" => "15:20", "title" => "Gửi thông báo check-in QR", "meta" => "Campus Music Night"],
    ["time" => "17:45", "title" => "Tổng hợp feedback sự kiện", "meta" => "Volunteer Network"],
];

function statusLabel(string $status): string
{
    return match ($status) {
        "open" => "Đang mở",
        "pending" => "Chờ duyệt",
        "closed" => "Đã đủ chỗ",
        "low" => "Cần chú ý",
        "active" => "Hoạt động",
        default => "Không rõ",
    };
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Event Management</title>
    <link rel="preconnect" href="https://images.unsplash.com">
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar" aria-label="Điều hướng chính">
            <a class="brand" href="#">
                <span class="brand-mark">SE</span>
                <span>
                    <strong>Student Events</strong>
                    <small>Club operations</small>
                </span>
            </a>

            <nav class="nav-list">
                <a class="active" href="#"><span>01</span>Tổng quan</a>
                <a href="#"><span>02</span>Sự kiện</a>
                <a href="#"><span>03</span>CLB</a>
                <a href="#"><span>04</span>Sinh viên</a>
                <a href="#"><span>05</span>Báo cáo</a>
            </nav>

            <div class="sidebar-note">
                <small>Phiên trực hôm nay</small>
                <strong>Nguyễn Minh Anh</strong>
                <span>Ban điều phối sự kiện</span>
            </div>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div>
                    <p class="eyebrow">Học kỳ 2026</p>
                    <h1>Quản lý sự kiện sinh viên</h1>
                </div>

                <div class="topbar-actions">
                    <label class="search-field">
                        <span>Tìm</span>
                        <input id="eventSearch" type="search" placeholder="Tên sự kiện, CLB, địa điểm">
                    </label>
                    <button class="ghost-button" type="button">Xuất báo cáo</button>
                    <button class="primary-button" type="button">Tạo sự kiện</button>
                </div>
            </header>

            <section class="overview-grid" aria-label="Chỉ số tổng quan">
                <?php foreach ($stats as $stat): ?>
                    <article class="metric">
                        <span><?= e($stat["label"]) ?></span>
                        <strong><?= e($stat["value"]) ?></strong>
                        <small><?= e($stat["trend"]) ?></small>
                    </article>
                <?php endforeach; ?>
            </section>

            <section class="workspace">
                <div class="event-panel">
                    <div class="section-heading">
                        <div>
                            <p class="eyebrow">Lịch sắp tới</p>
                            <h2>Sự kiện cần theo dõi</h2>
                        </div>

                        <div class="segmented-control" role="tablist" aria-label="Lọc trạng thái">
                            <button class="active" type="button" data-filter="all">Tất cả</button>
                            <button type="button" data-filter="open">Đang mở</button>
                            <button type="button" data-filter="pending">Chờ duyệt</button>
                            <button type="button" data-filter="closed">Đã đủ</button>
                        </div>
                    </div>

                    <div class="event-list" id="eventList">
                        <?php foreach ($events as $event): ?>
                            <?php $percent = min(100, round($event["registered"] / $event["capacity"] * 100)); ?>
                            <article
                                class="event-card"
                                data-status="<?= e($event["status"]) ?>"
                                data-search="<?= e(strtolower($event["title"] . " " . $event["club"] . " " . $event["location"])) ?>"
                            >
                                <img src="<?= e($event["image"]) ?>" alt="">
                                <div class="event-date">
                                    <strong><?= e(explode(" ", $event["date"])[0]) ?></strong>
                                    <span><?= e(explode(" ", $event["date"])[1]) ?></span>
                                </div>
                                <div class="event-body">
                                    <div class="event-title-row">
                                        <div>
                                            <span class="club-name"><?= e($event["club"]) ?></span>
                                            <h3><?= e($event["title"]) ?></h3>
                                        </div>
                                        <span class="status-badge <?= e($event["status"]) ?>"><?= e(statusLabel($event["status"])) ?></span>
                                    </div>
                                    <div class="event-meta">
                                        <span><?= e($event["time"]) ?></span>
                                        <span><?= e($event["location"]) ?></span>
                                    </div>
                                    <div class="capacity-row" aria-label="Số lượng đăng ký">
                                        <div>
                                            <strong><?= e((string) $event["registered"]) ?></strong>
                                            <span>/ <?= e((string) $event["capacity"]) ?> đăng ký</span>
                                        </div>
                                        <div class="capacity-bar"><span style="width: <?= e((string) $percent) ?>%"></span></div>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>

                <aside class="inspector">
                    <section class="visual-panel">
                        <img src="https://images.unsplash.com/photo-1523580494863-6f3031224c94?auto=format&fit=crop&w=900&q=80" alt="">
                        <div>
                            <span>Campus pulse</span>
                            <strong>3 sự kiện có tỉ lệ đăng ký trên 75%</strong>
                        </div>
                    </section>

                    <section class="panel-section">
                        <div class="section-heading compact">
                            <h2>CLB nổi bật</h2>
                            <button class="text-button" type="button">Xem tất cả</button>
                        </div>
                        <div class="club-list">
                            <?php foreach ($clubs as $club): ?>
                                <article class="club-row">
                                    <div class="club-avatar"><?= e(substr($club["name"], 0, 2)) ?></div>
                                    <div>
                                        <strong><?= e($club["name"]) ?></strong>
                                        <span><?= e($club["focus"]) ?></span>
                                    </div>
                                    <small class="<?= e($club["status"]) ?>"><?= e((string) $club["members"]) ?> TV</small>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <section class="panel-section">
                        <div class="section-heading compact">
                            <h2>Việc hôm nay</h2>
                            <button class="text-button" type="button">Thêm</button>
                        </div>
                        <div class="timeline">
                            <?php foreach ($timeline as $item): ?>
                                <article class="timeline-item">
                                    <time><?= e($item["time"]) ?></time>
                                    <div>
                                        <strong><?= e($item["title"]) ?></strong>
                                        <span><?= e($item["meta"]) ?></span>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>
                </aside>
            </section>
        </main>
    </div>

    <script src="assets/js/app.js"></script>
</body>
</html>

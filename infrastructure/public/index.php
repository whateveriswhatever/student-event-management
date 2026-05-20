<?php
define("root_dir", dirname(__DIR__));

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}

function statusLabel(string $status): string
{
    return match ($status) {
        "open" => "Đang mở",
        "pending" => "Chờ duyệt",
        "closed" => "Đã đủ chỗ",
        "void" => "Tạm dừng",
        "low" => "Cần chú ý",
        "active" => "Hoạt động",
        "success" => "Đã duyệt",
        "rejected" => "Từ chối",
        "draft" => "Bản nháp",
        default => "Không rõ",
    };
}

function initials(string $name): string
{
    $words = preg_split("/\s+/", trim($name)) ?: [];
    $letters = [];

    foreach ($words as $word) {
        if (!preg_match("/^\p{L}/u", $word, $match)) {
            continue;
        }

        $letters[] = strtoupper($match[0]);

        if (count($letters) >= 2) {
            break;
        }
    }

    return implode("", $letters);
}

$stats = [
    ["label" => "CLB hoạt động", "value" => "32", "trend" => "+2 tháng này", "tone" => "sky"],
    ["label" => "Sự kiện sắp tới", "value" => "18", "trend" => "6 sự kiện trên 75%", "tone" => "violet"],
    ["label" => "Sinh viên đăng ký", "value" => "1.248", "trend" => "+12% so với tháng trước", "tone" => "amber"],
    ["label" => "Tỉ lệ check-in", "value" => "86%", "trend" => "+8% sau QR", "tone" => "green"],
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
        "owner" => "Lê Quốc Bảo",
        "channel" => "Đăng ký công khai",
        "description" => "Chia sẻ quy trình ứng dụng AI vào sản phẩm sinh viên, demo workflow và phần hỏi đáp cuối chương trình.",
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
        "owner" => "Phạm Ngọc Linh",
        "channel" => "Vé QR nội bộ",
        "description" => "Đêm nhạc ngoài trời của các ban nhạc sinh viên, có khu check-in riêng và danh sách tình nguyện viên hỗ trợ.",
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
        "owner" => "Trần Minh Khôi",
        "channel" => "Cần duyệt phòng",
        "description" => "Workshop thực hành chỉnh CV theo nhóm ngành, cần xác nhận diễn giả và tài liệu in trước ngày tổ chức.",
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
        "owner" => "Vũ Hoàng Nam",
        "channel" => "Danh sách cố định",
        "description" => "Hoạt động tình nguyện làm sạch khuôn viên, đã đủ số lượng tham gia và đang chốt vật tư.",
        "image" => "https://images.unsplash.com/photo-1559027615-cd4628902d4a?auto=format&fit=crop&w=900&q=80",
    ],
    [
        "title" => "Startup Pitch Lab",
        "club" => "Entrepreneurship Lab",
        "date" => "02 Th06",
        "time" => "09:00 - 11:30",
        "location" => "Innovation Hub",
        "status" => "pending",
        "capacity" => 90,
        "registered" => 38,
        "owner" => "Đỗ Gia Hân",
        "channel" => "Chờ duyệt ngân sách",
        "description" => "Buổi pitching thử cho đội thi khởi nghiệp, cần duyệt ngân sách mentor và xác nhận phòng livestream.",
        "image" => "https://images.unsplash.com/photo-1556761175-b413da4baf72?auto=format&fit=crop&w=900&q=80",
    ],
];

$clubs = [
    [
        "name" => "IT Innovation Club",
        "members" => 186,
        "status" => "active",
        "focus" => "AI, Web, Product",
        "founded" => "2018",
        "description" => "Cộng đồng sinh viên yêu công nghệ, tổ chức workshop lập trình, demo sản phẩm và mentor dự án học kỳ.",
        "tone" => "sky",
    ],
    [
        "name" => "Melody Society",
        "members" => 94,
        "status" => "active",
        "focus" => "Band, Vocal, Stage",
        "founded" => "2020",
        "description" => "Không gian tập luyện biểu diễn, quản lý ban nhạc sinh viên và các đêm nhạc trong khuôn viên.",
        "tone" => "violet",
    ],
    [
        "name" => "Career Hub",
        "members" => 212,
        "status" => "active",
        "focus" => "CV, Interview, Jobs",
        "founded" => "2017",
        "description" => "CLB hướng nghiệp, kết nối diễn giả doanh nghiệp và hỗ trợ sinh viên chuẩn bị hồ sơ tuyển dụng.",
        "tone" => "amber",
    ],
    [
        "name" => "Volunteer Network",
        "members" => 147,
        "status" => "low",
        "focus" => "Community, Campus",
        "founded" => "2016",
        "description" => "Điều phối các hoạt động cộng đồng, chiến dịch xanh và nhóm hỗ trợ sự kiện lớn của trường.",
        "tone" => "green",
    ],
    [
        "name" => "Entrepreneurship Lab",
        "members" => 72,
        "status" => "active",
        "focus" => "Pitch, Startup, Mentor",
        "founded" => "2021",
        "description" => "Nơi sinh viên thử nghiệm ý tưởng kinh doanh, luyện pitch deck và kết nối mentor khởi nghiệp.",
        "tone" => "rose",
    ],
];

$timeline = [
    ["time" => "08:30", "title" => "Duyệt 12 đơn đăng ký mới", "meta" => "IT Innovation Club"],
    ["time" => "10:00", "title" => "Xác nhận phòng cho Workshop CV", "meta" => "Career Hub"],
    ["time" => "15:20", "title" => "Gửi thông báo check-in QR", "meta" => "Campus Music Night"],
    ["time" => "17:45", "title" => "Tổng hợp feedback sự kiện", "meta" => "Volunteer Network"],
];

$eventStages = [
    ["label" => "Bản nháp", "value" => "06", "meta" => "3 bản thiếu địa điểm", "status" => "draft"],
    ["label" => "Chờ duyệt", "value" => "04", "meta" => "2 sự kiện cần xử lý", "status" => "pending"],
    ["label" => "Đang mở", "value" => "18", "meta" => "6 sự kiện trên 75%", "status" => "open"],
    ["label" => "Hoàn tất", "value" => "09", "meta" => "5 báo cáo đã gửi", "status" => "success"],
];

$weekDays = [
    ["day" => "T2", "date" => "20", "items" => 2],
    ["day" => "T3", "date" => "21", "items" => 4, "active" => true],
    ["day" => "T4", "date" => "22", "items" => 1],
    ["day" => "T5", "date" => "23", "items" => 3],
    ["day" => "T6", "date" => "24", "items" => 5],
    ["day" => "T7", "date" => "25", "items" => 2],
    ["day" => "CN", "date" => "26", "items" => 0],
];

$activityBars = [
    ["label" => "T2", "value" => 42],
    ["label" => "T3", "value" => 34],
    ["label" => "T4", "value" => 28],
    ["label" => "T5", "value" => 46],
    ["label" => "T6", "value" => 31],
    ["label" => "T7", "value" => 52],
    ["label" => "CN", "value" => 24],
];

$approvalQueue = [
    ["student" => "Trần Gia Huy", "event" => "Tech Talk: AI trong sản phẩm sinh viên", "status" => "success", "time" => "10:42"],
    ["student" => "Mai Khánh Vy", "event" => "Workshop: Thiết kế CV chuyên nghiệp", "status" => "pending", "time" => "11:05"],
    ["student" => "Ngô Đức Anh", "event" => "Startup Pitch Lab", "status" => "pending", "time" => "12:18"],
    ["student" => "Hoàng Thảo Nhi", "event" => "Campus Music Night", "status" => "success", "time" => "13:24"],
];

$rooms = [
    ["name" => "Hội trường B", "usage" => "78%", "note" => "2 lịch đặt hôm nay"],
    ["name" => "Nhà A2 - P.304", "usage" => "64%", "note" => "Còn 1 khung tối"],
    ["name" => "Innovation Hub", "usage" => "52%", "note" => "Chờ duyệt thiết bị"],
];

$firstEvent = $events[0];
$firstProgress = min(100, round($firstEvent["registered"] / $firstEvent["capacity"] * 100));
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>StudentClub Event Desk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://images.unsplash.com">
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar" aria-label="Điều hướng chính">
            <a class="brand" href="#overview" data-view-link="overview">
                <span class="brand-mark" aria-hidden="true"></span>
                <span>
                    <strong>StudentClub</strong>
                    <small>Platform v1.0</small>
                </span>
            </a>

            <nav class="nav-list">
                <a class="active" href="#overview" data-view-link="overview" data-mobile-label="01">
                    <span class="nav-icon">D</span>
                    <span>Tổng quan</span>
                </a>
                <a href="#events" data-view-link="events" data-mobile-label="02">
                    <span class="nav-icon">E</span>
                    <span>Sự kiện</span>
                </a>
                <a href="#clubs" data-view-link="clubs" data-mobile-label="03">
                    <span class="nav-icon">C</span>
                    <span>Câu lạc bộ</span>
                </a>
            </nav>

            <section class="sidebar-profile">
                <div class="avatar">MA</div>
                <div>
                    <strong>Nguyễn Minh Anh</strong>
                    <small>Ban điều phối sự kiện</small>
                </div>
            </section>
        </aside>

        <main class="main-shell">
            <header class="topbar">
                <label class="global-search">
                    <span class="search-icon" aria-hidden="true"></span>
                    <input id="globalSearch" type="search" placeholder="Tìm kiếm sự kiện, CLB, địa điểm...">
                </label>

                <div class="topbar-actions">
                    <button class="icon-button" type="button" aria-label="Thông báo">
                        <span class="bell-dot" aria-hidden="true"></span>
                    </button>
                    <div class="divider" aria-hidden="true"></div>
                    <div class="account-chip">
                        <div>
                            <strong>Minh Anh</strong>
                            <small>admin</small>
                        </div>
                        <span class="avatar compact">MA</span>
                    </div>
                </div>
            </header>

            <div class="content-scroll">
                <section class="page-titlebar">
                    <div>
                        <p
                            class="eyebrow"
                            id="pageEyebrow"
                            data-overview="Học kỳ 2026"
                            data-events="Event desk"
                            data-clubs="Club directory"
                        >Học kỳ 2026</p>
                        <h1
                            id="pageTitle"
                            data-overview="Dashboard"
                            data-events="Sự kiện"
                            data-clubs="Câu lạc bộ"
                        >Dashboard</h1>
                        <p
                            id="pageSubtitle"
                            data-overview="Theo dõi đăng ký, lịch tổ chức và các việc cần xử lý trong ngày."
                            data-events="Quản lý lịch, duyệt trạng thái và kiểm tra sức chứa từng sự kiện."
                            data-clubs="Khám phá cộng đồng sinh viên và tình trạng hoạt động của từng CLB."
                        >Theo dõi đăng ký, lịch tổ chức và các việc cần xử lý trong ngày.</p>
                    </div>

                    <div class="title-actions">
                        <button class="ghost-button" type="button">Xuất báo cáo</button>
                        <button class="primary-button" type="button">Tạo sự kiện</button>
                    </div>
                </section>

                <section class="view-panel active" data-view-panel="overview">
                    <section class="stats-grid" aria-label="Chỉ số tổng quan">
                        <?php foreach ($stats as $stat): ?>
                            <article class="stat-card <?= e($stat["tone"]) ?>">
                                <span class="stat-icon" aria-hidden="true"></span>
                                <div>
                                    <p><?= e($stat["label"]) ?></p>
                                    <strong><?= e($stat["value"]) ?></strong>
                                </div>
                                <small><?= e($stat["trend"]) ?></small>
                            </article>
                        <?php endforeach; ?>
                    </section>

                    <section class="dashboard-grid">
                        <article class="analytics-panel">
                            <div class="section-heading">
                                <div>
                                    <p class="eyebrow">Activity</p>
                                    <h2>Hoạt động trong tuần</h2>
                                </div>
                                <span class="legend-dot">Tham gia</span>
                            </div>

                            <div class="bar-chart" aria-label="Biểu đồ hoạt động trong tuần">
                                <?php foreach ($activityBars as $bar): ?>
                                    <div class="bar-item">
                                        <span style="height: <?= e((string) $bar["value"]) ?>%"></span>
                                        <small><?= e($bar["label"]) ?></small>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </article>

                        <article class="spotlight-panel">
                            <div class="spotlight-top">
                                <p class="eyebrow">Sự kiện hot</p>
                                <span>21 Th05</span>
                            </div>
                            <h2><?= e($firstEvent["title"]) ?></h2>
                            <p><?= e($firstEvent["description"]) ?></p>
                            <div class="spotlight-meta">
                                <span><?= e($firstEvent["club"]) ?></span>
                                <span><?= e($firstEvent["location"]) ?></span>
                            </div>
                            <button class="light-button" type="button" data-view-link="events">Xem tất cả</button>
                        </article>
                    </section>

                    <section class="overview-layout">
                        <div class="event-panel">
                            <div class="section-heading">
                                <div>
                                    <p class="eyebrow">Lịch sắp tới</p>
                                    <h2>Sự kiện cần theo dõi</h2>
                                </div>

                                <div class="segmented-control" role="tablist" aria-label="Lọc trạng thái">
                                    <button class="active" type="button" data-overview-filter="all">Tất cả</button>
                                    <button type="button" data-overview-filter="open">Đang mở</button>
                                    <button type="button" data-overview-filter="pending">Chờ duyệt</button>
                                    <button type="button" data-overview-filter="closed">Đã đủ</button>
                                </div>
                            </div>

                            <div class="event-card-list" id="eventList">
                                <?php foreach (array_slice($events, 0, 4) as $event): ?>
                                    <?php
                                        $percent = min(100, round($event["registered"] / $event["capacity"] * 100));
                                        [$dateNumber, $dateMonth] = explode(" ", $event["date"]);
                                    ?>
                                    <article
                                        class="event-card"
                                        data-overview-card
                                        data-status="<?= e($event["status"]) ?>"
                                        data-search="<?= e($event["title"] . " " . $event["club"] . " " . $event["location"] . " " . $event["owner"]) ?>"
                                    >
                                        <div class="event-date">
                                            <span><?= e($dateMonth) ?></span>
                                            <strong><?= e($dateNumber) ?></strong>
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

                        <aside class="side-stack">
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

                            <section class="panel-section">
                                <div class="section-heading compact">
                                    <h2>Đăng ký mới</h2>
                                    <button class="text-button" type="button" data-view-link="events">Duyệt nhanh</button>
                                </div>
                                <div class="approval-list">
                                    <?php foreach ($approvalQueue as $request): ?>
                                        <article class="approval-row">
                                            <time><?= e($request["time"]) ?></time>
                                            <div>
                                                <strong><?= e($request["student"]) ?></strong>
                                                <span><?= e($request["event"]) ?></span>
                                            </div>
                                            <small class="<?= e($request["status"]) ?>"><?= e(statusLabel($request["status"])) ?></small>
                                        </article>
                                    <?php endforeach; ?>
                                </div>
                            </section>
                        </aside>
                    </section>
                </section>

                <section class="view-panel" data-view-panel="events">
                    <section class="event-command">
                        <div class="event-command-main">
                            <div class="section-heading event-command-heading">
                                <div>
                                    <p class="eyebrow">Plan status</p>
                                    <h2>Trung tâm điều phối</h2>
                                </div>
                                <div class="event-actions">
                                    <button class="ghost-button" type="button">Lịch phòng</button>
                                    <button class="primary-button" type="button">Tạo lịch mới</button>
                                </div>
                            </div>

                            <div class="event-toolbar">
                                <label class="inline-search">
                                    <span class="search-icon" aria-hidden="true"></span>
                                    <input id="eventManagerSearch" type="search" placeholder="Sự kiện, CLB, người phụ trách">
                                </label>
                                <select id="clubFilter" aria-label="Lọc theo CLB">
                                    <option value="all">Tất cả CLB</option>
                                    <?php foreach ($clubs as $club): ?>
                                        <option value="<?= e($club["name"]) ?>"><?= e($club["name"]) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="ghost-button" type="button">Tháng 05</button>
                            </div>

                            <div class="event-stage-grid" aria-label="Luồng trạng thái sự kiện">
                                <?php foreach ($eventStages as $stage): ?>
                                    <article class="event-stage <?= e($stage["status"]) ?>">
                                        <span><?= e($stage["label"]) ?></span>
                                        <strong><?= e($stage["value"]) ?></strong>
                                        <small><?= e($stage["meta"]) ?></small>
                                    </article>
                                <?php endforeach; ?>
                            </div>

                            <div class="week-strip" aria-label="Lịch tuần">
                                <?php foreach ($weekDays as $day): ?>
                                    <button class="<?= !empty($day["active"]) ? "active" : "" ?>" type="button">
                                        <span><?= e($day["day"]) ?></span>
                                        <strong><?= e($day["date"]) ?></strong>
                                        <small><?= e((string) $day["items"]) ?> lịch</small>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <aside
                            class="event-detail-panel"
                            id="eventDetailPanel"
                            aria-label="Chi tiết sự kiện đang chọn"
                        >
                            <img data-detail-image src="<?= e($firstEvent["image"]) ?>" alt="" loading="lazy">
                            <div class="detail-body">
                                <span data-detail-status class="status-badge <?= e($firstEvent["status"]) ?>"><?= e(statusLabel($firstEvent["status"])) ?></span>
                                <h2 data-detail-title><?= e($firstEvent["title"]) ?></h2>
                                <p data-detail-description><?= e($firstEvent["description"]) ?></p>
                                <dl class="detail-list">
                                    <div>
                                        <dt>CLB</dt>
                                        <dd data-detail-club><?= e($firstEvent["club"]) ?></dd>
                                    </div>
                                    <div>
                                        <dt>Thời gian</dt>
                                        <dd data-detail-time><?= e($firstEvent["date"]) ?> · <?= e($firstEvent["time"]) ?></dd>
                                    </div>
                                    <div>
                                        <dt>Địa điểm</dt>
                                        <dd data-detail-location><?= e($firstEvent["location"]) ?></dd>
                                    </div>
                                    <div>
                                        <dt>Phụ trách</dt>
                                        <dd data-detail-owner><?= e($firstEvent["owner"]) ?></dd>
                                    </div>
                                </dl>
                                <div class="capacity-row">
                                    <div>
                                        <strong data-detail-registered><?= e((string) $firstEvent["registered"]) ?></strong>
                                        <span data-detail-capacity>/ <?= e((string) $firstEvent["capacity"]) ?> đăng ký</span>
                                    </div>
                                    <div class="capacity-bar"><span data-detail-progress style="width: <?= e((string) $firstProgress) ?>%"></span></div>
                                </div>
                            </div>
                        </aside>
                    </section>

                    <section class="event-management-grid">
                        <div class="event-table-panel">
                            <div class="section-heading">
                                <div>
                                    <p class="eyebrow">Danh sách</p>
                                    <h2>Sự kiện đang xử lý</h2>
                                </div>

                                <div class="segmented-control" role="tablist" aria-label="Lọc danh sách sự kiện">
                                    <button class="active" type="button" data-event-filter="all">Tất cả</button>
                                    <button type="button" data-event-filter="open">Đang mở</button>
                                    <button type="button" data-event-filter="pending">Chờ duyệt</button>
                                    <button type="button" data-event-filter="closed">Đã đủ</button>
                                </div>
                            </div>

                            <div class="event-table">
                                <div class="event-table-head" aria-hidden="true">
                                    <span>Sự kiện</span>
                                    <span>Thời gian</span>
                                    <span>Địa điểm</span>
                                    <span>Đăng ký</span>
                                    <span>Trạng thái</span>
                                </div>

                                <?php foreach ($events as $index => $event): ?>
                                    <?php $percent = min(100, round($event["registered"] / $event["capacity"] * 100)); ?>
                                    <button
                                        class="event-table-row <?= $index === 0 ? "selected" : "" ?>"
                                        type="button"
                                        data-event-row
                                        data-status="<?= e($event["status"]) ?>"
                                        data-club="<?= e($event["club"]) ?>"
                                        data-title="<?= e($event["title"]) ?>"
                                        data-date="<?= e($event["date"]) ?>"
                                        data-time="<?= e($event["time"]) ?>"
                                        data-location="<?= e($event["location"]) ?>"
                                        data-capacity="<?= e((string) $event["capacity"]) ?>"
                                        data-registered="<?= e((string) $event["registered"]) ?>"
                                        data-progress="<?= e((string) $percent) ?>"
                                        data-owner="<?= e($event["owner"]) ?>"
                                        data-description="<?= e($event["description"]) ?>"
                                        data-image="<?= e($event["image"]) ?>"
                                        data-search="<?= e($event["title"] . " " . $event["club"] . " " . $event["owner"] . " " . $event["location"]) ?>"
                                    >
                                        <span>
                                            <strong><?= e($event["title"]) ?></strong>
                                            <small><?= e($event["club"]) ?> · <?= e($event["owner"]) ?></small>
                                        </span>
                                        <span><?= e($event["date"]) ?><small><?= e($event["time"]) ?></small></span>
                                        <span><?= e($event["location"]) ?><small><?= e($event["channel"]) ?></small></span>
                                        <span>
                                            <strong><?= e((string) $event["registered"]) ?>/<?= e((string) $event["capacity"]) ?></strong>
                                            <small><?= e((string) $percent) ?>% công suất</small>
                                        </span>
                                        <span><i class="status-badge <?= e($event["status"]) ?>"><?= e(statusLabel($event["status"])) ?></i></span>
                                    </button>
                                <?php endforeach; ?>
                            </div>

                            <p class="empty-state" id="eventEmptyState">Không có sự kiện phù hợp bộ lọc.</p>
                        </div>

                        <aside class="side-stack">
                            <section class="panel-section">
                                <div class="section-heading compact">
                                    <h2>Phòng & sức chứa</h2>
                                    <button class="text-button" type="button">Mở lịch</button>
                                </div>
                                <div class="room-list">
                                    <?php foreach ($rooms as $room): ?>
                                        <article class="room-row">
                                            <div>
                                                <strong><?= e($room["name"]) ?></strong>
                                                <span><?= e($room["note"]) ?></span>
                                            </div>
                                            <b><?= e($room["usage"]) ?></b>
                                        </article>
                                    <?php endforeach; ?>
                                </div>
                            </section>

                            <section class="visual-panel">
                                <img src="https://images.unsplash.com/photo-1523580494863-6f3031224c94?auto=format&fit=crop&w=900&q=80" alt="" loading="lazy">
                                <div>
                                    <span>Campus pulse</span>
                                    <strong>3 sự kiện có tỉ lệ đăng ký trên 75%</strong>
                                </div>
                            </section>
                        </aside>
                    </section>
                </section>

                <section class="view-panel" data-view-panel="clubs">
                    <section class="club-toolbar">
                        <label class="inline-search">
                            <span class="search-icon" aria-hidden="true"></span>
                            <input id="clubSearch" type="search" placeholder="Tìm tên CLB hoặc lĩnh vực">
                        </label>

                        <div class="segmented-control" role="tablist" aria-label="Lọc CLB">
                            <button class="active" type="button" data-club-filter="all">Tất cả</button>
                            <button type="button" data-club-filter="active">Hoạt động</button>
                            <button type="button" data-club-filter="low">Cần chú ý</button>
                        </div>
                    </section>

                    <section class="club-grid" aria-label="Danh sách câu lạc bộ">
                        <?php foreach ($clubs as $club): ?>
                            <article
                                class="club-card <?= e($club["tone"]) ?>"
                                data-club-card
                                data-status="<?= e($club["status"]) ?>"
                                data-search="<?= e($club["name"] . " " . $club["focus"] . " " . $club["description"]) ?>"
                            >
                                <div class="club-cover">
                                    <span class="club-avatar"><?= e(initials($club["name"])) ?></span>
                                    <small class="<?= e($club["status"]) ?>"><?= e(statusLabel($club["status"])) ?></small>
                                </div>
                                <div class="club-card-body">
                                    <div>
                                        <h2><?= e($club["name"]) ?></h2>
                                        <p>Founded <?= e($club["founded"]) ?></p>
                                    </div>
                                    <span class="club-focus"><?= e($club["focus"]) ?></span>
                                    <p><?= e($club["description"]) ?></p>
                                    <div class="club-footer">
                                        <span><?= e((string) $club["members"]) ?> thành viên</span>
                                        <button type="button">Xem CLB</button>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </section>

                    <p class="empty-state" id="clubEmptyState">Không có CLB phù hợp bộ lọc.</p>
                </section>
            </div>
        </main>
    </div>

    <script src="assets/js/app.js"></script>
</body>
</html>

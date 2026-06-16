<?php define("ASSET_URL", BASE_URL . "/public"); ?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/LoginRegister.css" />
    <title>Cổng Hoạt Động Câu Lạc Bộ & Sự Kiện Sinh Viên - VNU-IS</title>
</head>
<body>

    <!-- 1. Header Banner VNU-IS -->
    <header class="is-header">
        <div class="header-container">
            <div class="logo-area">
                <!-- VNU-IS Logo Shield SVG -->
                <svg class="school-shield" width="48" height="48" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M50 5L15 25V55C15 75 35 90 50 95C65 90 85 75 85 55V25L50 5Z" fill="#ffffff"/>
                    <path d="M50 10L20 28V53C20 70 37 84 50 89C63 84 80 70 80 53V28L50 10Z" fill="#005ba9"/>
                    <text x="50" y="55" fill="#ffffff" font-family="Arial" font-size="28" font-weight="bold" text-anchor="middle">IS</text>
                    <path d="M25 40 H75 M35 60 H65" stroke="#ffffff" stroke-width="3" stroke-linecap="round"/>
                </svg>
                <div class="brand-text">
                    <span class="sub-title">TRƯỜNG QUỐC TẾ - ĐẠI HỌC QUỐC GIA HÀ NỘI</span>
                    <span class="main-title">CỔNG HOẠT ĐỘNG CÂU LẠC BỘ & SỰ KIỆN</span>
                    <span class="eng-title">VNU-IS STUDENT CLUB & EVENT PORTAL</span>
                </div>
            </div>
        </div>
    </header>

    <!-- 2. Nav tabs bar - Các mục liên quan đến CLB & Sự kiện -->
    <nav class="is-navbar">
        <div class="navbar-container">
            <a href="#" class="nav-tab active" data-target="tab-news">TIN HOẠT ĐỘNG</a>
            <a href="#" class="nav-tab" data-target="tab-events">SỰ KIỆN MỚI</a>
            <a href="#" class="nav-tab" data-target="tab-clubs">DANH SÁCH CÂU LẠC BỘ</a>
            <a href="#" class="nav-tab" data-target="tab-forms">BIỂU MẪU ĐĂNG KÝ</a>
            <a href="#" class="nav-tab" data-target="tab-guides">HƯỚNG DẪN HỘI VIÊN</a>
        </div>
    </nav>

    <!-- 3. Portal Body (Two-Column Layout) -->
    <main class="portal-body">
        <div class="portal-container">
            
            <!-- Left Column: Announcements container with tabbed content -->
            <section class="announcements-column">
                
                <!-- TAB 1: TIN HOẠT ĐỘNG -->
                <div class="tab-content-section" id="tab-news">
                    <?php if (!empty($announcements)): ?>
                        <?php foreach ($announcements as $ann): ?>
                            <div class="announcement-item">
                                <div class="date-badge">
                                    <span class="badge-month">Thông báo</span>
                                    <span class="badge-day"><?= sprintf("%02d", $ann['ID']) ?></span>
                                </div>
                                <div class="announcement-content">
                                    <a href="#" class="announcement-title open-ann-modal" 
                                       data-title="<?= htmlspecialchars($ann['title']) ?>" 
                                       data-content="<?= htmlspecialchars($ann['description']) ?>"
                                    ><?= htmlspecialchars($ann['title']) ?></a>
                                    <a href="#" class="detail-link open-ann-modal"
                                       data-title="<?= htmlspecialchars($ann['title']) ?>" 
                                       data-content="<?= htmlspecialchars($ann['description']) ?>"
                                    >Xem chi tiết</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: #64748b; font-style: italic; padding: 1rem 0;">Chưa có thông báo nào được đăng tải.</p>
                    <?php endif; ?>
                </div>

                <!-- TAB 2: SỰ KIỆN MỚI -->
                <div class="tab-content-section hidden" id="tab-events">
                    <?php if (!empty($events)): ?>
                        <?php foreach ($events as $ev): 
                            $dateObj = new DateTime($ev['event_date']);
                            $monthStr = "Tháng " . $dateObj->format("m");
                            $dayStr = $dateObj->format("d");
                            
                            // Map location ID to text
                            $locText = "Tòa A - Hội trường lớn";
                            if ($ev['location_ID'] == 1) $locText = "Tòa C - Phòng 201";
                            else if ($ev['location_ID'] == 2) $locText = "Tòa D - Phòng 402";
                        ?>
                            <div class="announcement-item">
                                <div class="date-badge">
                                    <span class="badge-month"><?= $monthStr ?></span>
                                    <span class="badge-day"><?= $dayStr ?></span>
                                </div>
                                <div class="announcement-content">
                                    <a href="#" class="announcement-title open-event-modal" 
                                       data-title="<?= htmlspecialchars($ev['title']) ?>" 
                                       data-desc="<?= htmlspecialchars($ev['description']) ?>" 
                                       data-date="<?= $dateObj->format('d/m/Y') ?>"
                                       data-time="<?= (new DateTime($ev['start_time']))->format('H:i') ?> - <?= (new DateTime($ev['end_time']))->format('H:i') ?>"
                                       data-location="<?= htmlspecialchars($locText) ?>"
                                       data-slots="<?= $ev['max_participants'] ?>"
                                    ><?= htmlspecialchars($ev['title']) ?></a>
                                    <a href="#" class="detail-link open-event-modal"
                                       data-title="<?= htmlspecialchars($ev['title']) ?>" 
                                       data-desc="<?= htmlspecialchars($ev['description']) ?>" 
                                       data-date="<?= $dateObj->format('d/m/Y') ?>"
                                       data-time="<?= (new DateTime($ev['start_time']))->format('H:i') ?> - <?= (new DateTime($ev['end_time']))->format('H:i') ?>"
                                       data-location="<?= htmlspecialchars($locText) ?>"
                                       data-slots="<?= $ev['max_participants'] ?>"
                                    >Xem chi tiết</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: #64748b; font-style: italic; padding: 1rem 0;">Chưa có sự kiện nào sắp diễn ra.</p>
                    <?php endif; ?>
                </div>

                <!-- TAB 3: DANH SÁCH CÂU LẠC BỘ -->
                <div class="tab-content-section hidden" id="tab-clubs">
                    <?php if (!empty($clubs)): ?>
                        <?php foreach ($clubs as $index => $c): ?>
                            <div class="announcement-item">
                                <div class="date-badge">
                                    <span class="badge-month">CLB</span>
                                    <span class="badge-day"><?= sprintf("%02d", $index + 1) ?></span>
                                </div>
                                <div class="announcement-content">
                                    <a href="#" class="announcement-title open-club-modal"
                                       data-name="<?= htmlspecialchars($c['name']) ?>"
                                       data-desc="<?= htmlspecialchars($c['description']) ?>"
                                       data-founded="<?= (new DateTime($c['founded_date']))->format('d/m/Y') ?>"
                                       data-status="<?= htmlspecialchars($c['status']) ?>"
                                    ><?= htmlspecialchars($c['name']) ?></a>
                                    <a href="#" class="detail-link open-club-modal"
                                       data-name="<?= htmlspecialchars($c['name']) ?>"
                                       data-desc="<?= htmlspecialchars($c['description']) ?>"
                                       data-founded="<?= (new DateTime($c['founded_date']))->format('d/m/Y') ?>"
                                       data-status="<?= htmlspecialchars($c['status']) ?>"
                                    >Tìm hiểu thêm</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: #64748b; font-style: italic; padding: 1rem 0;">Không tìm thấy câu lạc bộ nào.</p>
                    <?php endif; ?>
                </div>

                <!-- TAB 4: BIỂU MẪU ĐĂNG KÝ -->
                <div class="tab-content-section hidden" id="tab-forms">
                    <div class="announcement-item">
                        <div class="date-badge">
                            <span class="badge-month">Mẫu</span>
                            <span class="badge-day">01</span>
                        </div>
                        <div class="announcement-content">
                            <a href="#" class="announcement-title open-form-modal" data-form="form-establish">Mẫu 01: Đơn xin thành lập Câu lạc bộ sinh viên mới và quy định kèm theo</a>
                            <div style="display: flex; gap: 15px; margin-top: 5px;">
                                <a href="#" class="detail-link open-form-modal" data-form="form-establish" style="color: var(--vnu-cyan);">Xem trực tuyến</a>
                                <a href="#" class="detail-link" onclick="alert('Đang tải tệp tin biểu mẫu về máy...')">Tải về (.docx)</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="announcement-item">
                        <div class="date-badge">
                            <span class="badge-month">Mẫu</span>
                            <span class="badge-day">02</span>
                        </div>
                        <div class="announcement-content">
                            <a href="#" class="announcement-title open-form-modal" data-form="form-join">Mẫu 02: Phiếu đăng ký ứng cử vào Ban chủ nhiệm Câu lạc bộ nhiệm kỳ mới</a>
                            <div style="display: flex; gap: 15px; margin-top: 5px;">
                                <a href="#" class="detail-link open-form-modal" data-form="form-join" style="color: var(--vnu-cyan);">Xem trực tuyến</a>
                                <a href="#" class="detail-link" onclick="alert('Đang tải tệp tin biểu mẫu về máy...')">Tải về (.docx)</a>
                            </div>
                        </div>
                    </div>

                    <div class="announcement-item">
                        <div class="date-badge">
                            <span class="badge-month">Mẫu</span>
                            <span class="badge-day">03</span>
                        </div>
                        <div class="announcement-content">
                            <a href="#" class="announcement-title open-form-modal" data-form="form-proposal">Mẫu 03: Tờ trình phê duyệt kế hoạch tổ chức sự kiện và đề xuất kinh phí hỗ trợ</a>
                            <div style="display: flex; gap: 15px; margin-top: 5px;">
                                <a href="#" class="detail-link open-form-modal" data-form="form-proposal" style="color: var(--vnu-cyan);">Xem trực tuyến</a>
                                <a href="#" class="detail-link" onclick="alert('Đang tải tệp tin biểu mẫu về máy...')">Tải về (.docx)</a>
                            </div>
                        </div>
                    </div>

                    <div class="announcement-item">
                        <div class="date-badge">
                            <span class="badge-month">Mẫu</span>
                            <span class="badge-day">04</span>
                        </div>
                        <div class="announcement-content">
                            <a href="#" class="announcement-title open-form-modal" data-form="form-report">Mẫu 04: Biên bản nghiệm thu sự kiện và báo cáo tổng kết hoạt động định kỳ của CLB</a>
                            <div style="display: flex; gap: 15px; margin-top: 5px;">
                                <a href="#" class="detail-link open-form-modal" data-form="form-report" style="color: var(--vnu-cyan);">Xem trực tuyến</a>
                                <a href="#" class="detail-link" onclick="alert('Đang tải tệp tin biểu mẫu về máy...')">Tải về (.docx)</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 5: HƯỚNG DẪN HỘI VIÊN -->
                <div class="tab-content-section hidden" id="tab-guides">
                    <div class="announcement-item">
                        <div class="date-badge">
                            <span class="badge-month">HD</span>
                            <span class="badge-day">01</span>
                        </div>
                        <div class="announcement-content">
                            <a href="#" class="announcement-title open-guide-modal" data-guide="guide-certificate">Hướng dẫn đăng ký cấp Giấy chứng nhận tham gia hoạt động ngoại khóa trực tuyến</a>
                            <a href="#" class="detail-link open-guide-modal" data-guide="guide-certificate">Xem chi tiết</a>
                        </div>
                    </div>

                    <div class="announcement-item">
                        <div class="date-badge">
                            <span class="badge-month">HD</span>
                            <span class="badge-day">02</span>
                        </div>
                        <div class="announcement-content">
                            <a href="#" class="announcement-title open-guide-modal" data-guide="guide-system">Cẩm nang hướng dẫn sử dụng hệ thống đăng ký câu lạc bộ và quét mã điểm danh sự kiện</a>
                            <a href="#" class="detail-link open-guide-modal" data-guide="guide-system">Xem chi tiết</a>
                        </div>
                    </div>

                    <div class="announcement-item">
                        <div class="date-badge">
                            <span class="badge-month">HD</span>
                            <span class="badge-day">03</span>
                        </div>
                        <div class="announcement-content">
                            <a href="#" class="announcement-title open-guide-modal" data-guide="guide-finance">Quy chế chi tiêu tài chính nội bộ và quy trình tài trợ sự kiện dành cho các câu lạc bộ</a>
                            <a href="#" class="detail-link open-guide-modal" data-guide="guide-finance">Xem chi tiết</a>
                        </div>
                    </div>

                    <div class="announcement-item">
                        <div class="date-badge">
                            <span class="badge-month">HD</span>
                            <span class="badge-day">04</span>
                        </div>
                        <div class="announcement-content">
                            <a href="#" class="announcement-title open-guide-modal" data-guide="guide-equipment">Quy định về việc bảo quản, đăng ký mượn trang thiết bị phục vụ văn nghệ và sự kiện sinh viên</a>
                            <a href="#" class="detail-link open-guide-modal" data-guide="guide-equipment">Xem chi tiết</a>
                        </div>
                    </div>
                </div>

            </section>

            <!-- Right Column: Login Card & Register Card -->
            <aside class="auth-column">
                
                <?php if (isset($error)): ?>
                    <div class="error-banner">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <!-- Warning feedback container (inserted via JS for modal clicks) -->
                <div id="auth-warning-banner" class="error-banner hidden" style="background-color: #fff3cd; color: #856404; border-color: #ffeeba; margin-bottom: 0.5rem; text-align: left; font-size: 0.85rem;">
                </div>

                <!-- A. LOGIN CARD -->
                <div class="auth-card" id="login-section">
                    <h2 class="portal-main-title">QUẢN LÝ CLB & SỰ KIỆN</h2>
                    <h3 class="portal-sub-title">ĐĂNG NHẬP HỆ THỐNG</h3>

                    <form action="<?= BASE_URL ?>/auth/login" method="POST">
                        <div class="form-group">
                            <input type="text" name="studentID" class="form-input" placeholder="Nhập mã số sinh viên" required>
                        </div>
                        <div class="form-group password-group">
                            <input type="password" name="inputPassword" class="form-input" placeholder="Nhập mật khẩu" required>
                            <div class="graduated-option">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="remember"> Ghi nhớ đăng nhập
                                </label>
                            </div>
                        </div>

                        <!-- Captcha area -->
                        <div class="captcha-group">
                            <input type="text" class="form-input captcha-input" placeholder="Nhập mã xác nhận" required>
                            <button type="button" class="btn-refresh" title="Làm mới">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l.73-2.73"/></svg>
                            </button>
                            <div class="captcha-img">
                                <span class="captcha-text">x7A8p</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-login">ĐĂNG NHẬP</button>
                    </form>

                    <!-- Manager/Leader Link -->
                    <button type="button" class="btn btn-parents" onclick="alert('Vui lòng liên hệ Ban quản trị để nhận tài khoản quản trị viên!')">Đăng nhập Ban Quản trị</button>

                    <!-- Mobile App Download -->
                    <div class="app-download-section">
                        <p class="app-title">Tải App Sự kiện sinh viên:</p>
                        <div class="app-flex">
                            <div class="store-links">
                                <a href="#" class="store-btn">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Google Play">
                                </a>
                                <a href="#" class="store-btn">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/3/3c/Download_on_the_App_Store_Badge.svg" alt="App Store">
                                </a>
                            </div>
                            <div class="qr-box">
                                <!-- Mock QR Code SVG -->
                                <svg width="50" height="50" viewBox="0 0 100 100" fill="currentColor">
                                    <rect width="100" height="100" fill="white"/>
                                    <rect x="10" y="10" width="20" height="20"/>
                                    <rect x="70" y="10" width="20" height="20"/>
                                    <rect x="10" y="70" width="20" height="20"/>
                                    <rect x="40" y="40" width="20" height="20"/>
                                    <rect x="40" y="10" width="10" height="20"/>
                                    <rect x="10" y="40" width="20" height="10"/>
                                    <rect x="70" y="40" width="20" height="20"/>
                                    <rect x="40" y="70" width="20" height="10"/>
                                    <rect x="70" y="70" width="10" height="20"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="auth-toggle-footer">
                        Sinh viên mới? <a class="toggle-link" id="toggle-to-register">Đăng ký tài khoản</a>
                    </div>
                </div>

                <!-- B. REGISTER CARD -->
                <div class="auth-card hidden" id="register-section">
                    <h2 class="portal-main-title">ĐĂNG KÝ HỘI VIÊN</h2>
                    <h3 class="portal-sub-title">TẠO TÀI KHOẢN MỚI</h3>

                    <form action="<?= BASE_URL ?>/auth/signup" method="POST">
                        <div class="form-row">
                            <div class="form-group flex-1">
                                <input type="text" name="firstname" class="form-input" placeholder="Họ đệm" required>
                            </div>
                            <div class="form-group flex-1">
                                <input type="text" name="lastname" class="form-input" placeholder="Tên" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <input type="text" name="ID" class="form-input" placeholder="Mã số sinh viên (8 số)" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group flex-1">
                                <input type="number" name="age" class="form-input" placeholder="Tuổi" required>
                            </div>
                            <div class="form-group flex-2">
                                <input type="tel" name="phoneNumber" class="form-input" placeholder="Số điện thoại" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <input type="email" name="email" class="form-input" placeholder="Địa chỉ Email" required />
                        </div>

                        <div class="form-group">
                            <select name="major" class="form-select" required>
                                <option value="" disabled selected>Chọn ngành học của bạn...</option>
                                <option value="informatics and computer engineering">Informatics and Computer Engineering</option>
                                <option value="business and data analysis">Business and Data Analysis</option>
                                <option value="management information system">Management Information System</option>
                                <option value="accounting, analyzing and auditing">Accounting, Analyzing and Auditing</option>
                                <option value="automation and informatics">Automation and Informatics</option>
                                <option value="english language">English Language</option>
                                <option value="digital communication">Digital Communication</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <input type="password" name="password" class="form-input" placeholder="Mật khẩu tài khoản" required />
                        </div>

                        <button type="submit" class="btn btn-login">ĐĂNG KÝ TÀI KHOẢN</button>
                    </form>

                    <div class="auth-toggle-footer">
                        Đã có tài khoản? <a class="toggle-link" id="toggle-to-login">Quay lại đăng nhập</a>
                    </div>
                </div>

            </aside>
            
        </div>
    </main>

    <!-- Interactive Overlays / Modals -->
    
    <!-- A. Announcement Detail Modal -->
    <div id="modal-announcement" class="custom-modal-overlay hidden">
        <div class="custom-modal-box">
            <div class="modal-header">
                <h3 id="modal-ann-title" style="color: var(--vnu-blue); font-size: 1.1rem; line-height: 1.4;">Thông báo</h3>
                <button type="button" class="modal-close-btn">&times;</button>
            </div>
            <div class="modal-body">
                <div id="modal-ann-content" style="line-height: 1.6; font-size: 0.95rem; white-space: pre-line;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-parents btn-close-modal" style="margin-bottom: 0;">Đóng</button>
            </div>
        </div>
    </div>

    <!-- B. Event Detail Modal -->
    <div id="modal-event" class="custom-modal-overlay hidden">
        <div class="custom-modal-box">
            <div class="modal-header">
                <h3 style="color: var(--vnu-blue); font-size: 1.1rem;">Chi tiết sự kiện</h3>
                <button type="button" class="modal-close-btn">&times;</button>
            </div>
            <div class="modal-body">
                <h4 id="modal-event-title" style="color: var(--vnu-orange); font-size: 1.05rem; margin-bottom: 1rem; line-height: 1.4;"></h4>
                <div class="event-meta-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 0.9rem; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                    <div><strong>Ngày tổ chức:</strong> <span id="modal-event-date"></span></div>
                    <div><strong>Thời gian:</strong> <span id="modal-event-time"></span></div>
                    <div><strong>Địa điểm:</strong> <span id="modal-event-location"></span></div>
                    <div><strong>Giới hạn:</strong> <span id="modal-event-slots"></span> người</div>
                </div>
                <div class="event-desc-box" style="line-height: 1.6; font-size: 0.95rem;">
                    <strong>Mô tả sự kiện:</strong>
                    <p id="modal-event-desc" style="margin-top: 5px; color: #4a5568; white-space: pre-line;"></p>
                </div>
            </div>
            <div class="modal-footer" style="display: flex; gap: 10px;">
                <button type="button" class="btn btn-login btn-register-event-action" style="margin-bottom: 0; flex: 1;">ĐĂNG KÝ THAM GIA</button>
                <button type="button" class="btn btn-parents btn-close-modal" style="margin-bottom: 0; flex: 1;">Đóng</button>
            </div>
        </div>
    </div>

    <!-- C. Club Detail Modal -->
    <div id="modal-club" class="custom-modal-overlay hidden">
        <div class="custom-modal-box">
            <div class="modal-header">
                <h3 style="color: var(--vnu-blue); font-size: 1.1rem;">Giới thiệu Câu lạc bộ</h3>
                <button type="button" class="modal-close-btn">&times;</button>
            </div>
            <div class="modal-body">
                <h4 id="modal-club-name" style="color: var(--vnu-blue-dark); font-size: 1.1rem; margin-bottom: 0.75rem; border-bottom: 2px solid var(--vnu-blue); padding-bottom: 5px;"></h4>
                <div class="club-meta-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 0.9rem; margin-bottom: 1rem;">
                    <div><strong>Ngày thành lập:</strong> <span id="modal-club-founded"></span></div>
                    <div><strong>Trạng thái:</strong> <span id="modal-club-status" style="color: green; font-weight: bold; text-transform: uppercase;"></span></div>
                </div>
                <div class="club-desc-box" style="line-height: 1.6; font-size: 0.95rem;">
                    <strong>Giới thiệu hoạt động:</strong>
                    <p id="modal-club-desc" style="margin-top: 5px; color: #4a5568; white-space: pre-line;"></p>
                </div>
            </div>
            <div class="modal-footer" style="display: flex; gap: 10px;">
                <button type="button" class="btn btn-login btn-join-club-action" style="margin-bottom: 0; flex: 1;">ĐĂNG KÝ GIA NHẬP</button>
                <button type="button" class="btn btn-parents btn-close-modal" style="margin-bottom: 0; flex: 1;">Đóng</button>
            </div>
        </div>
    </div>

    <!-- D. Form Previewer Modal -->
    <div id="modal-form" class="custom-modal-overlay hidden">
        <div class="custom-modal-box modal-lg" style="max-width: 750px;">
            <div class="modal-header">
                <h3 style="color: var(--vnu-blue); font-size: 1.1rem;">Trình xem & Điền thử biểu mẫu trực tuyến</h3>
                <button type="button" class="modal-close-btn">&times;</button>
            </div>
            <div class="modal-body document-body" style="background-color: #f1f5f9; padding: 1.5rem; overflow-y: auto; max-height: 60vh;">
                <div class="a4-page" style="background: white; padding: 2.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); font-family: 'Times New Roman', Times, serif; color: black; line-height: 1.5;">
                    <div class="a4-header" style="text-align: center; margin-bottom: 2rem; font-size: 1.05rem;">
                        <strong>ĐẠI HỌC QUỐC GIA HÀ NỘI</strong><br>
                        <strong style="text-decoration: underline;">TRƯỜNG QUỐC TẾ</strong>
                    </div>
                    <div class="a4-title" style="text-align: center; margin-bottom: 2rem;">
                        <h3 id="form-doc-title" style="font-size: 1.4rem; font-weight: bold; margin-bottom: 5px;">ĐƠN ĐĂNG KÝ TỔ CHỨC HOẠT ĐỘNG</h3>
                        <span style="font-style: italic; font-size: 0.95rem;">(Dành cho các Câu lạc bộ sinh viên trực thuộc VNU-IS)</span>
                    </div>
                    
                    <form id="online-mock-form" style="font-size: 1.05rem;">
                        <div class="form-doc-fields" style="display: flex; flex-direction: column; gap: 15px;">
                            <div class="doc-row" style="display: flex; gap: 10px; align-items: baseline;">
                                <label style="flex-shrink: 0; font-weight: bold;">Họ và tên sinh viên:</label>
                                <input type="text" class="doc-input" placeholder="Nhập họ và tên sinh viên..." style="flex: 1; border: none; border-bottom: 1px dashed black; outline: none; font-family: inherit; font-size: inherit; padding: 2px 5px;" required>
                            </div>
                            <div class="doc-row-half" style="display: flex; gap: 20px;">
                                <div class="doc-row" style="display: flex; gap: 10px; align-items: baseline; flex: 1;">
                                    <label style="flex-shrink: 0; font-weight: bold;">Mã số sinh viên:</label>
                                    <input type="text" class="doc-input" placeholder="MSSV gồm 8 số..." style="flex: 1; border: none; border-bottom: 1px dashed black; outline: none; font-family: inherit; font-size: inherit; padding: 2px 5px;" required>
                                </div>
                                <div class="doc-row" style="display: flex; gap: 10px; align-items: baseline; flex: 1;">
                                    <label style="flex-shrink: 0; font-weight: bold;">Lớp khóa học:</label>
                                    <input type="text" class="doc-input" placeholder="Ví dụ: 22B1..." style="flex: 1; border: none; border-bottom: 1px dashed black; outline: none; font-family: inherit; font-size: inherit; padding: 2px 5px;" required>
                                </div>
                            </div>
                            <div class="doc-row" style="display: flex; gap: 10px; align-items: baseline;">
                                <label style="flex-shrink: 0; font-weight: bold;">Số điện thoại liên hệ:</label>
                                <input type="text" class="doc-input" placeholder="Nhập số điện thoại liên lạc..." style="flex: 1; border: none; border-bottom: 1px dashed black; outline: none; font-family: inherit; font-size: inherit; padding: 2px 5px;" required>
                            </div>
                            <div class="doc-row" style="display: flex; flex-direction: column; gap: 5px;">
                                <label id="form-dynamic-label" style="font-weight: bold;">Nội dung đề xuất / Lý do đăng ký:</label>
                                <textarea class="doc-textarea" rows="4" placeholder="Nhập chi tiết thông tin tờ trình hoặc đề xuất của bạn lên trường..." style="width: 100%; border: 1px dashed black; border-radius: 4px; outline: none; font-family: inherit; font-size: inherit; padding: 10px; resize: none;" required></textarea>
                            </div>
                        </div>
                        
                        <div class="doc-signatures" style="display: flex; justify-content: space-between; margin-top: 3rem; font-size: 0.95rem;">
                            <div class="sig-left" style="text-align: center;">
                                <strong>Ý KIẾN BAN CHỦ NHIỆM CLB</strong><br>
                                <span class="sig-note" style="font-style: italic; font-size: 0.85rem; opacity: 0.7;">(Ký và ghi rõ họ tên)</span>
                            </div>
                            <div class="sig-right" style="text-align: center;">
                                <strong>NGƯỜI LÀM ĐƠN</strong><br>
                                <span class="sig-note" style="font-style: italic; font-size: 0.85rem; opacity: 0.7;">(Ký và ghi rõ họ tên)</span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="display: flex; gap: 10px;">
                <button type="button" class="btn btn-login btn-submit-form-action" style="margin-bottom: 0; flex: 2;">NỘP BIỂU MẪU (YÊU CẦU ĐĂNG NHẬP)</button>
                <button type="button" class="btn btn-parents btn-close-modal" style="margin-bottom: 0; flex: 1;">Hủy bỏ</button>
            </div>
        </div>
    </div>

    <!-- E. Guide Reader Modal -->
    <div id="modal-guide" class="custom-modal-overlay hidden">
        <div class="custom-modal-box" style="max-width: 600px;">
            <div class="modal-header">
                <h3 style="color: var(--vnu-blue); font-size: 1.1rem;">Hướng dẫn hội viên</h3>
                <button type="button" class="modal-close-btn">&times;</button>
            </div>
            <div class="modal-body">
                <h4 id="modal-guide-title" style="color: var(--vnu-orange); font-size: 1.05rem; margin-bottom: 1rem; line-height: 1.4; border-bottom: 1px dashed var(--border-color); padding-bottom: 8px;"></h4>
                <div id="modal-guide-content" class="guide-steps-container" style="font-size: 0.95rem; line-height: 1.6; color: #334155;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-parents btn-close-modal" style="margin-bottom: 0;">Đóng</button>
            </div>
        </div>
    </div>

    <script src="<?= ASSET_URL ?>/assets/js/LoginRegister.js"></script>
    <script>
        // Captcha refresh logic
        document.querySelector('.btn-refresh').addEventListener('click', function() {
            const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let mockCaptcha = '';
            for (let i = 0; i < 5; i++) {
                mockCaptcha += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.querySelector('.captcha-text').textContent = mockCaptcha;
        });

        // Tab switching logic
        document.querySelectorAll('.nav-tab').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                // Remove active class from all tabs
                document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('active'));
                // Add active class to clicked tab
                this.classList.add('active');
                
                // Hide all tab content sections
                document.querySelectorAll('.tab-content-section').forEach(sec => sec.classList.add('hidden'));
                // Show target section
                const targetId = this.getAttribute('data-target');
                document.getElementById(targetId).classList.remove('hidden');
            });
        });

        // Interactive Modal Overlays JS Handlers

        // 1. Utility function to close any modal
        function closeAllModals() {
            document.querySelectorAll('.custom-modal-overlay').forEach(modal => {
                modal.classList.add('hidden');
            });
        }

        document.querySelectorAll('.modal-close-btn, .btn-close-modal').forEach(btn => {
            btn.addEventListener('click', closeAllModals);
        });

        // Close on backdrop overlay click
        document.querySelectorAll('.custom-modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeAllModals();
                }
            });
        });

        // Close on Escape key press
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAllModals();
            }
        });

        // 2. Scroll smooth and highlight/shake Login Card
        function scrollToAuthAndShake(message) {
            closeAllModals();
            
            // Highlight message above the login
            const warningBanner = document.getElementById('auth-warning-banner');
            warningBanner.textContent = message;
            warningBanner.classList.remove('hidden');

            const loginCard = document.getElementById('login-section');
            const registerSection = document.getElementById('register-section');
            
            // If register card is active, toggle back to login card
            if (loginCard.classList.contains('hidden')) {
                registerSection.classList.add('hidden');
                loginCard.classList.remove('hidden');
                document.title = "Sign In - Club&Event Seeker";
            }

            // Scroll smoothly
            loginCard.scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Apply CSS shake effect
            loginCard.classList.add('shake-effect');
            setTimeout(() => {
                loginCard.classList.remove('shake-effect');
            }, 600);
        }

        // 3. Opening Announcements Modals
        document.querySelectorAll('.open-ann-modal').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const title = this.getAttribute('data-title');
                const content = this.getAttribute('data-content');
                
                document.getElementById('modal-ann-title').textContent = title;
                document.getElementById('modal-ann-content').textContent = content;
                
                document.getElementById('modal-announcement').classList.remove('hidden');
            });
        });

        // 4. Opening Event Modals
        document.querySelectorAll('.open-event-modal').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const title = this.getAttribute('data-title');
                const desc = this.getAttribute('data-desc');
                const date = this.getAttribute('data-date');
                const time = this.getAttribute('data-time');
                const loc = this.getAttribute('data-location');
                const slots = this.getAttribute('data-slots');

                document.getElementById('modal-event-title').textContent = title;
                document.getElementById('modal-event-desc').textContent = desc;
                document.getElementById('modal-event-date').textContent = date;
                document.getElementById('modal-event-time').textContent = time;
                document.getElementById('modal-event-location').textContent = loc;
                document.getElementById('modal-event-slots').textContent = slots;

                document.getElementById('modal-event').classList.remove('hidden');
            });
        });

        document.querySelector('.btn-register-event-action').addEventListener('click', function() {
            scrollToAuthAndShake('Bạn cần đăng nhập bằng tài khoản Sinh viên VNU-IS để đăng ký tham gia sự kiện này!');
        });

        // 5. Opening Club Modals
        document.querySelectorAll('.open-club-modal').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const name = this.getAttribute('data-name');
                const desc = this.getAttribute('data-desc');
                const founded = this.getAttribute('data-founded');
                const status = this.getAttribute('data-status');

                document.getElementById('modal-club-name').textContent = name;
                document.getElementById('modal-club-desc').textContent = desc;
                document.getElementById('modal-club-founded').textContent = founded;
                
                const statusLabel = document.getElementById('modal-club-status');
                statusLabel.textContent = status === 'active' ? 'Đang hoạt động' : (status === 'low' ? 'Ít hoạt động' : 'Tạm dừng');
                statusLabel.style.color = status === 'active' ? 'green' : (status === 'low' ? '#d97706' : 'red');

                document.getElementById('modal-club').classList.remove('hidden');
            });
        });

        document.querySelector('.btn-join-club-action').addEventListener('click', function() {
            scrollToAuthAndShake('Bạn cần đăng nhập hoặc tạo tài khoản Hội viên để đăng ký gia nhập Câu lạc bộ sinh viên!');
        });

        // 6. Opening Form Modals (Online mock editor)
        const formTitles = {
            'form-establish': 'ĐƠN XIN THÀNH LẬP CÂU LẠC BỘ SINH VIÊN MỚI',
            'form-join': 'PHIẾU ĐĂNG KÝ ỨNG CỬ VÀO BAN CHỦ NHIỆM CLB',
            'form-proposal': 'TỜ TRÌNH PHÊ DUYỆT KẾ HOẠCH & ĐỀ XUẤT KINH PHÍ HỖ TRỢ',
            'form-report': 'BIÊN BẢN NGHIỆM THU SỰ KIỆN & BÁO CÁO TỔNG KẾT CLB'
        };

        const formLabels = {
            'form-establish': 'Danh sách Ban sáng lập CLB và Đề án hoạt động tóm tắt:',
            'form-join': 'Vị trí ứng cử (Chủ nhiệm/Phó chủ nhiệm) và Kế hoạch phát triển CLB của bạn:',
            'form-proposal': 'Kế hoạch chi tiết, thời gian địa điểm và các hạng mục xin ngân sách hỗ trợ:',
            'form-report': 'Báo cáo số lượng tham dự, kết quả đạt được và bảng kê khai tài chính chi tiết:'
        };

        document.querySelectorAll('.open-form-modal').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const formType = this.getAttribute('data-form');
                
                document.getElementById('form-doc-title').textContent = formTitles[formType] || 'BIỂU MẪU ĐĂNG KÝ';
                document.getElementById('form-dynamic-label').textContent = formLabels[formType] || 'Chi tiết nội dung đăng ký:';
                
                // Clear mock fields
                document.getElementById('online-mock-form').reset();

                document.getElementById('modal-form').classList.remove('hidden');
            });
        });

        document.querySelector('.btn-submit-form-action').addEventListener('click', function(e) {
            e.preventDefault();
            scrollToAuthAndShake('Tính năng nộp biểu mẫu trực tuyến yêu cầu bạn đăng nhập bằng tài khoản Sinh viên VNU-IS!');
        });

        // 7. Opening Guides Modals
        const guideData = {
            'guide-certificate': `
                <p style="margin-bottom: 10px; font-weight: bold;">Quy trình 4 bước đăng ký cấp Giấy chứng nhận ngoại khóa:</p>
                <div style="display: flex; flex-direction: column; gap: 12px; margin-left: 10px;">
                    <div><strong>Bước 1:</strong> Đăng nhập hệ thống bằng mã số sinh viên (8 chữ số).</div>
                    <div><strong>Bước 2:</strong> Vào trang cá nhân của bạn, chọn menu <strong>"Yêu cầu chứng nhận"</strong>.</div>
                    <div><strong>Bước 3:</strong> Chọn sự kiện mà bạn đã check-in điểm danh thành công từ danh sách có sẵn.</div>
                    <div><strong>Bước 4:</strong> Bấm gửi yêu cầu. Phòng CT&HSSV sẽ kiểm tra lịch sử điểm danh đối chiếu của bạn và phê duyệt cấp Giấy chứng nhận bản điện tử PDF trong vòng 3 đến 5 ngày làm việc.</div>
                </div>
            `,
            'guide-system': `
                <p style="margin-bottom: 10px; font-weight: bold;">Cẩm nang sử dụng hệ thống đăng ký & quét mã điểm danh sự kiện:</p>
                <div style="display: flex; flex-direction: column; gap: 12px; margin-left: 10px;">
                    <div><strong>Bước 1:</strong> Tạo tài khoản và cập nhật chính xác ngành học, lớp học của bạn.</div>
                    <div><strong>Bước 2:</strong> Duyệt qua danh sách <strong>"Sự kiện mới"</strong>, xem chi tiết và ấn <strong>"Đăng ký tham gia"</strong> để nhận mã QR cá nhân cho sự kiện đó.</div>
                    <div><strong>Bước 3:</strong> Khi đến tham dự sự kiện trực tiếp, xuất trình mã QR cá nhân trên ứng dụng cho Ban tổ chức quét tại bàn đón tiếp khi Check-in và Check-out.</div>
                    <div><strong>Bước 4:</strong> Hệ thống sẽ tự động đối chiếu điểm danh và cập nhật điểm rèn luyện ngoại khóa cho bạn ngay sau khi sự kiện kết thúc.</div>
                </div>
            `,
            'guide-finance': `
                <p style="margin-bottom: 10px; font-weight: bold;">Quy chế quản lý tài chính và xét duyệt tài trợ ngân sách CLB:</p>
                <div style="display: flex; flex-direction: column; gap: 12px; margin-left: 10px;">
                    <div><strong>Bước 1:</strong> Ban chủ nhiệm CLB chuẩn bị kế hoạch chi tiết kèm theo bảng dự trù kinh phí tổ chức sự kiện theo <strong>Mẫu 03</strong>.</div>
                    <div><strong>Bước 2:</strong> Trình Ban Giám hiệu trường và Đoàn Thanh niên phê duyệt ít nhất 14 ngày trước khi bắt đầu chuẩn bị sự kiện.</div>
                    <div><strong>Bước 3:</strong> Sau khi nhận quyết định phê duyệt, CLB thực hiện tạm ứng kinh phí để trang trải chi phí ban đầu từ kế toán trường.</div>
                    <div><strong>Bước 4:</strong> Thu thập đầy đủ hóa đơn đỏ, chứng từ hợp lệ và nộp báo cáo nghiệm thu theo <strong>Mẫu 04</strong> trong vòng 7 ngày sau khi sự kiện kết thúc để được quyết toán.</div>
                </div>
            `,
            'guide-equipment': `
                <p style="margin-bottom: 10px; font-weight: bold;">Quy trình mượn thiết bị âm thanh, ánh sáng và hội trường của trường:</p>
                <div style="display: flex; flex-direction: column; gap: 12px; margin-left: 10px;">
                    <div><strong>Bước 1:</strong> Tải phiếu đăng ký mượn cơ sở vật chất từ mục <strong>"Biểu mẫu"</strong>.</div>
                    <div><strong>Bước 2:</strong> Điền đầy đủ thông tin trang thiết bị cần mượn, phòng hoặc hội trường cần sử dụng và xin xác nhận từ Giảng viên Cố vấn của CLB.</div>
                    <div><strong>Bước 3:</strong> Gửi phiếu yêu cầu đến Ban quản lý Cơ sở vật chất tại văn phòng phòng Thiết bị (Phòng 102 nhà C).</div>
                    <div><strong>Bước 4:</strong> Nhận bàn giao thiết bị trực tiếp từ thủ kho, ký biên bản bàn giao. Sau khi kết thúc sự kiện, câu lạc bộ có nghĩa vụ thu dọn và hoàn trả nguyên vẹn toàn bộ tài sản đã mượn.</div>
                </div>
            `
        };

        const guideTitles = {
            'guide-certificate': 'Hướng dẫn đăng ký cấp Giấy chứng nhận tham gia hoạt động ngoại khóa trực tuyến',
            'guide-system': 'Cẩm nang hướng dẫn sử dụng hệ thống đăng ký câu lạc bộ và quét mã điểm danh sự kiện',
            'guide-finance': 'Quy chế chi tiêu tài chính nội bộ và quy trình tài trợ sự kiện dành cho các câu lạc bộ',
            'guide-equipment': 'Quy định về việc bảo quản, đăng ký mượn trang thiết bị phục vụ văn nghệ và sự kiện sinh viên'
        };

        document.querySelectorAll('.open-guide-modal').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const guideKey = this.getAttribute('data-guide');

                document.getElementById('modal-guide-title').textContent = guideTitles[guideKey] || 'Hướng dẫn hội viên';
                document.getElementById('modal-guide-content').innerHTML = guideData[guideKey] || '<p>Không có nội dung hướng dẫn cho mục này.</p>';

                document.getElementById('modal-guide').classList.remove('hidden');
            });
        });
    </script>
</body>
</html>
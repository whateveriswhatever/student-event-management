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

    <!-- 1. Header Banner VNU-IS - Định hướng Quản lý CLB & Sự kiện -->
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
            <a href="#" class="nav-tab active">TIN HOẠT ĐỘNG</a>
            <a href="#" class="nav-tab">SỰ KIỆN MỚI</a>
            <a href="#" class="nav-tab">DANH SÁCH CÂU LẠC BỘ</a>
            <a href="#" class="nav-tab">BIỂU MẪU ĐĂNG KÝ</a>
            <a href="#" class="nav-tab">HƯỚNG DẪN HỘI VIÊN</a>
        </div>
    </nav>

    <!-- 3. Portal Body (Two-Column Layout) -->
    <main class="portal-body">
        <div class="portal-container">
            
            <!-- Left Column: Announcements - Các tin sự kiện và hoạt động thực tế -->
            <section class="announcements-column">
                
                <div class="announcement-item">
                    <div class="date-badge">
                        <span class="badge-month">Tháng 06</span>
                        <span class="badge-day">09</span>
                    </div>
                    <div class="announcement-content">
                        <a href="#" class="announcement-title">THÔNG BÁO: MỞ ĐĂNG KÝ THÀNH VIÊN MỚI CHO CÁC CÂU LẠC BỘ ĐỢT 1 NĂM HỌC 2026-2027</a>
                        <a href="#" class="detail-link">Xem chi tiết</a>
                    </div>
                </div>

                <div class="announcement-item">
                    <div class="date-badge">
                        <span class="badge-month">Tháng 06</span>
                        <span class="badge-day">08</span>
                    </div>
                    <div class="announcement-content">
                        <a href="#" class="announcement-title">SỰ KIỆN: SEMINAR ĐỊNH HƯỚNG PHÁT TRIỂN SỰ NGHIỆP TRONG KỶ NGUYÊN AI DO CLB TIN HỌC TỔ CHỨC</a>
                        <a href="#" class="detail-link">Xem chi tiết</a>
                    </div>
                </div>

                <div class="announcement-item">
                    <div class="date-badge">
                        <span class="badge-month">Tháng 06</span>
                        <span class="badge-day">08</span>
                    </div>
                    <div class="announcement-content">
                        <a href="#" class="announcement-title">HƯỚNG DẪN: QUY TRÌNH ĐĂNG KÝ TỔ CHỨC SỰ KIỆN NỘI BỘ VÀ XIN KINH PHÍ HỖ TRỢ HOẠT ĐỘNG CLB</a>
                        <a href="#" class="detail-link">Xem chi tiết</a>
                    </div>
                </div>

                <div class="announcement-item">
                    <div class="date-badge">
                        <span class="badge-month">Tháng 06</span>
                        <span class="badge-day">04</span>
                    </div>
                    <div class="announcement-content">
                        <a href="#" class="announcement-title">TIN TỨC: ĐỘI VĂN NGHỆ XUNG KÍCH TRƯỜNG QUỐC TẾ ĐẠT GIẢI NHẤT LIÊN HOAN CA KHÚC SINH VIÊN ĐHQGHN</a>
                        <a href="#" class="detail-link">Xem chi tiết</a>
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

    <script src="<?= ASSET_URL ?>/assets/js/LoginRegister.js"></script>
    <script>
        // Simple JS helper to mock captcha change
        document.querySelector('.btn-refresh').addEventListener('click', function() {
            const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let mockCaptcha = '';
            for (let i = 0; i < 5; i++) {
                mockCaptcha += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.querySelector('.captcha-text').textContent = mockCaptcha;
        });
    </script>
</body>
</html>
<?php
    require_once __DIR__ . "/database-config.php";

    class DatabaseSeeder {
        public static function seed(): void {
            try {
                $db = DatabaseConfig::getInstance()->getConnection();

                // 1. Seed Roles
                $db->exec("INSERT IGNORE INTO `Role` (ID, role_title, permission) VALUES
                    (1, 'president', 'manager'),
                    (2, 'vice president', 'moderator'),
                    (3, 'secretary', 'moderator'),
                    (4, 'member', 'regular')");

                // 2. Seed Locations
                $db->exec("INSERT IGNORE INTO `Location` (ID, building, room, attendance_capacity) VALUES
                    (1, 'Building C', '201', 120),
                    (2, 'Building D', '402', 60),
                    (3, 'Building A', 'Hall', 400)");

                // 3. Seed Profiles
                $db->exec("INSERT IGNORE INTO `Profile` (ID, student_ID, major, class, degree) VALUES
                    (1, '22010123', 'informatics', '22B1', 'undergraduate'),
                    (2, '22010456', 'business', '22B2', 'undergraduate')");

                // 4. Seed Students (Password is 'password123' hashed)
                $db->exec("INSERT IGNORE INTO `Student` (ID, firstname, lastname, age, profile_ID, phone_number, email, password) VALUES
                    ('22010123', 'Nguyen Van', 'A', 20, 1, '0912345678', 'studenta@vnu.edu.vn', '$2y$10\$w09Z9mGq.D.L3c/OqFv9Q.dC/l6aF9kR6k.O1o5kE6V7/1J7aV5qy'),
                    ('22010456', 'Tran Thi', 'B', 20, 2, '0987654321', 'studentb@vnu.edu.vn', '$2y$10\$w09Z9mGq.D.L3c/OqFv9Q.dC/l6aF9kR6k.O1o5kE6V7/1J7aV5qy')");

                // 5. Seed Clubs
                $db->exec("INSERT IGNORE INTO `Club` (ID, name, description, founded_date, logo_url, status) VALUES
                    (1, 'CLB Tin học (IS-Tech Club)', 'Cộng đồng nghiên cứu lập trình phần mềm, AI và phát triển Web của sinh viên VNU-IS.', '2024-09-15 00:00:00', '', 'active'),
                    (2, 'CLB Nghệ thuật (IS-Art Club)', 'Sân chơi nghệ thuật sôi nổi gồm đội Hát, đội Vũ đạo và đội Nhạc cụ.', '2024-10-10 00:00:00', '', 'active'),
                    (3, 'CLB Tiếng Anh (IS-English Club)', 'Môi trường phát triển kỹ năng giao tiếp tiếng Anh, thuyết trình và biện luận phản biện.', '2024-11-01 00:00:00', '', 'active'),
                    (4, 'CLB Thể thao (IS-Sport Club)', 'Tập hợp các đội tuyển bóng đá, bóng rổ, cầu lông và cờ vua đại diện trường.', '2024-09-01 00:00:00', '', 'active')");

                // 6. Seed Events
                $db->exec("INSERT IGNORE INTO `Event` (ID, club_ID, title, description, event_date, start_time, end_time, location_ID, max_participants, status) VALUES
                    (1, 4, 'Đại hội Thể thao Sinh viên VNU-IS Sport Festival 2026', 'Sự kiện thể thao thường niên thu hút đông đảo sinh viên VNU-IS đăng ký tham gia các môn bóng đá, bóng rổ và điền kinh.', '2026-06-20 08:00:00', '2026-06-20 08:00:00', '2026-06-20 17:00:00', 3, 300, 'open'),
                    (2, 1, 'Cuộc thi VNU-IS Hackathon 2026', 'Lập trình ứng dụng công nghệ xanh vì cộng đồng trong vòng 24 giờ. Cơ hội làm việc nhóm và nhận giải thưởng lớn.', '2026-06-25 09:00:00', '2026-06-25 09:00:00', '2026-06-26 12:00:00', 1, 150, 'open'),
                    (3, 2, 'Ngày hội giao lưu văn hóa quốc tế International Culture Day 2026', 'Trải nghiệm không gian văn hóa ẩm thực và âm nhạc đa quốc gia tại khuôn viên Hòa Lạc của VNU-IS.', '2026-07-02 14:00:00', '2026-07-02 14:00:00', '2026-07-02 21:00:00', 3, 500, 'open'),
                    (4, 4, 'Hoạt động Hiến máu nhân đạo thường niên - Chủ nhật Đỏ VNU-IS năm 2026', 'Lan tỏa thông điệp sẻ chia giọt máu hồng vì cộng đồng của sinh viên Trường Quốc tế.', '2026-07-15 07:30:00', '2026-07-15 07:30:00', '2026-07-15 11:30:00', 2, 200, 'open')");

                // 7. Seed Announcements
                $db->exec("INSERT IGNORE INTO `Announcement` (ID, club_ID, author_ID, title, description) VALUES
                    (1, 1, '22010123', 'THÔNG BÁO: MỞ ĐĂNG KÝ THÀNH VIÊN MỚI CHO CÁC CÂU LẠC BỘ ĐỢT 1 NĂM HỌC 2026-2027', 'Ban chủ nhiệm các Câu lạc bộ chính thức mở đơn đăng ký chào đón các bạn tân sinh viên và sinh viên khóa cũ tham gia hoạt động.'),
                    (2, 1, '22010123', 'SỰ KIỆN: SEMINAR ĐỊNH HƯỚNG PHÁT TRIỂN SỰ NGHIỆP TRONG KỶ NGUYÊN AI DO CLB TIN HỌC TỔ CHỨC', 'Buổi seminar cung cấp góc nhìn sâu sắc về tác động của trí tuệ nhân tạo và các kỹ năng lập trình cần chuẩn bị cho tương lai.'),
                    (3, 1, '22010123', 'HƯỚNG DẪN: QUY TRÌNH ĐĂNG KÝ TỔ CHỨC SỰ KIỆN NỘI BỘ VÀ XIN KINH PHÍ HỖ TRỢ HOẠT ĐỘNG CLB', 'Quy trình chi tiết và các bước chuẩn bị tờ trình phê duyệt kế hoạch cho ban điều hành câu lạc bộ.'),
                    (4, 2, '22010456', 'TIN TỨC: ĐỘO VĂN NGHỆ XUNG KÍCH TRƯỜNG QUỐC TẾ ĐẠT GIẢI NHẤT LIÊN HOAN CA KHÚC SINH VIÊN ĐHQGHN', 'Đội văn nghệ đã xuất sắc vượt qua các đội bạn để giành vị trí quán quân chung cuộc.')");

            } catch (PDOException $e) {
                error_log("[DatabaseSeeder::seed] " . $e->getMessage());
            }
        }
    }
?>

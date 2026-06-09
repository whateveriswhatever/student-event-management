drop database if exists student_club_and_event_management_platform;
create database student_club_and_event_management_platform
    character set utf8mb4
    collate utf8mb4_unicode_ci;
use student_club_and_event_management_platform;

-- Xóa bảng theo đúng thứ tự phụ thuộc (con trước, cha sau)
drop table if exists `Feedback`;
drop table if exists `Attendance`;
drop table if exists `Event_Registration`;
drop table if exists `Announcement`;
drop table if exists `Club_Membership`;
drop table if exists `Event`;
drop table if exists `Role`;
drop table if exists `Club`;
drop table if exists `Location`;
drop table if exists `Student`;
drop table if exists `Profile`;

-- ============================================================
-- BẢNG CỐT LÕI
-- ============================================================

create table `Profile` (
    `ID`         int          auto_increment primary key,
    `student_ID` varchar(8)   not null,
    `major`      varchar(10)  not null,
    `class`      char(5)      not null,
    `degree`     enum('undergraduate', 'postgraduate') not null default 'undergraduate'
);

create table `Student` (
    `ID`           varchar(8)  not null primary key,
    `firstname`    varchar(50) not null,
    `lastname`     varchar(50) not null,
    `age`          int         not null default 18,
    `profile_ID`   int         not null,
    `phone_number` varchar(12) not null,
    `email`        varchar(36) not null unique,
    `password`     varchar(95) not null,

    foreign key (`profile_ID`) references `Profile`(`ID`)
);

create table `Location` (
    `ID`                  int         auto_increment primary key,
    `building`            varchar(20) not null,
    `room`                varchar(4)  not null,
    `attendance_capacity` int         not null
);

create table `Club` (
    `ID`           int          auto_increment primary key,
    `name`         varchar(55)  not null,
    `description`  varchar(555) not null,
    `founded_date` datetime     default current_timestamp,
    `logo_url`     varchar(222) not null default '',
    `status`       enum('active', 'low', 'closed') not null default 'active'
);

create table `Role` (
    `ID`          int  auto_increment primary key,
    `role_title`  enum('president', 'vice president', 'secretary', 'member') not null,
    `permission`  enum('regular', 'moderator', 'manager') not null default 'regular'
);

-- membership_status dùng enum string để đồng bộ với PHP MembershipStatus enum
create table `Club_Membership` (
    `ID`                int        auto_increment primary key,
    `student_ID`        varchar(8) not null,
    `club_ID`           int        not null,
    `role_ID`           int        not null,
    `joined_at`         timestamp  default current_timestamp,
    `membership_status` enum('approval', 'rejected', 'left', 'banned', 'pending') not null default 'pending',

    foreign key (`student_ID`) references `Student`(`ID`),
    foreign key (`club_ID`)    references `Club`(`ID`),
    foreign key (`role_ID`)    references `Role`(`ID`)
);

-- `Event` được đặt trong backtick vì là reserved word của MySQL
create table `Event` (
    `ID`               int          auto_increment primary key,
    `club_ID`          int          not null,
    `title`            varchar(200) not null,
    `description`      varchar(500) not null,
    `event_date`       datetime     default current_timestamp,
    `start_time`       timestamp    default current_timestamp,
    `end_time`         timestamp    default current_timestamp,
    `location_ID`      int          not null,
    `max_participants` int          not null default 0,
    `status`           enum('open', 'closed', 'pending', 'void') not null default 'pending',

    foreign key (`club_ID`)     references `Club`(`ID`),
    foreign key (`location_ID`) references `Location`(`ID`)
);

create table `Event_Registration` (
    `ID`                  int        auto_increment primary key,
    `event_ID`            int        not null,
    `student_ID`          varchar(8) not null,
    `registered_at`       timestamp  default current_timestamp,
    `registration_status` enum('success', 'rejected', 'pending') not null default 'pending',

    foreign key (`event_ID`)   references `Event`(`ID`),
    foreign key (`student_ID`) references `Student`(`ID`)
);

create table `Attendance` (
    `ID`                int  auto_increment primary key,
    `registration_ID`   int  not null,
    `checkin_time`      timestamp default current_timestamp,
    `attendance_status` enum('checked-in', 'checked-out', 'void') not null default 'checked-in',

    foreign key (`registration_ID`) references `Event_Registration`(`ID`)
);

create table `Announcement` (
    `ID`          int          auto_increment primary key,
    `club_ID`     int          not null,
    `author_ID`   varchar(8)   not null,
    `title`       varchar(55)  not null,
    `description` varchar(1000) not null,

    foreign key (`club_ID`)   references `Club`(`ID`),
    foreign key (`author_ID`) references `Student`(`ID`)
);

create table `Feedback` (
    `ID`           int           auto_increment primary key,
    `from_user_ID` varchar(8)    not null,
    `to_user_ID`   varchar(8)    not null,
    `on_event_ID`  int           not null,
    `content`      varchar(1000) not null,
    `at_timestamp` timestamp     default current_timestamp,

    foreign key (`from_user_ID`) references `Student`(`ID`),
    foreign key (`to_user_ID`)   references `Student`(`ID`),
    foreign key (`on_event_ID`)  references `Event`(`ID`)
);

-- ============================================================
-- INDEXES: Tăng tốc truy vấn cho các cột thường xuyên dùng
-- ============================================================

-- Student: tìm theo email khi đăng nhập
create index `idx_student_email` on `Student`(`email`);

-- Club_Membership: lọc theo sinh viên / câu lạc bộ / trạng thái
create index `idx_membership_student` on `Club_Membership`(`student_ID`);
create index `idx_membership_club`    on `Club_Membership`(`club_ID`);
create index `idx_membership_status`  on `Club_Membership`(`membership_status`);

-- Event: lọc theo câu lạc bộ, trạng thái, ngày
create index `idx_event_club`   on `Event`(`club_ID`);
create index `idx_event_status` on `Event`(`status`);
create index `idx_event_date`   on `Event`(`event_date`);

-- Event_Registration: lọc theo sự kiện / sinh viên / trạng thái
create index `idx_registration_event`   on `Event_Registration`(`event_ID`);
create index `idx_registration_student` on `Event_Registration`(`student_ID`);
create index `idx_registration_status`  on `Event_Registration`(`registration_status`);

-- Attendance: tìm điểm danh theo registration
create index `idx_attendance_registration` on `Attendance`(`registration_ID`);
create index `idx_attendance_status`       on `Attendance`(`attendance_status`);

-- Announcement: lọc theo câu lạc bộ và tác giả
create index `idx_announcement_club`   on `Announcement`(`club_ID`);
create index `idx_announcement_author` on `Announcement`(`author_ID`);

-- Feedback: lọc theo người gửi / người nhận / sự kiện
create index `idx_feedback_from`  on `Feedback`(`from_user_ID`);
create index `idx_feedback_to`    on `Feedback`(`to_user_ID`);
create index `idx_feedback_event` on `Feedback`(`on_event_ID`);

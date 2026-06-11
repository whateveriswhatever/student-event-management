drop database if exists student_club_and_event_management_platform;
create database student_club_and_event_management_platform;
use student_club_and_event_management_platform;

drop table if exists Student;
drop table if exists Profile;
drop table if exists Location;
drop table if exists Club;
drop table if exists Role;
drop table if exists Club_Membership;
drop table if exists Event;
drop table if exists Attendance;
drop table if exists Event_Registration;
drop table if exists Annoucement;
drop table if exists Feedback;

create table Profile (
    ID int auto_increment primary key,
    student_ID varchar(4) not null,
    major varchar(10) not null, 
    class char(5) not null,
    degree enum("undergraduate", "postgraduate") not null default "undergraduate"
);

create table Student (
    ID varchar(8) not null primary key, 
    firstname varchar(50) not null,
    lastname varchar(50) not null,
    age int,
    profile_ID int not null,
    phone_number varchar(12) not null,
    email varchar(36) not null,
    password varchar(95) not null,

    foreign key (profile_ID) references Profile(ID)
);

create table Location (
    ID int auto_increment primary key,
    building varchar(55) not null,
    room varchar(55) not null,
    attendance_capacity int not null
);

create table Club (
    ID int auto_increment primary key,
    name varchar(55) not null,
    description varchar(555) not null,
    founded_date datetime default current_timestamp,
    logo_url varchar(222) not null,
    status enum("active", "low", "closed") not null,
    total_members int default 0
);

create table Role (
    ID int auto_increment primary key,
    role_title enum("president", "vice president", "secretary", "member") not null,
    permission enum("regular", "moderator", "manager") not null
);

create table Club_Membership (
    ID int auto_increment primary key,
    student_ID varchar(8) not null,
    club_ID int not null,
    role_ID int not null,
    joined_at timestamp default current_timestamp,
    membership_status boolean not null,

    foreign key (student_ID) references Student(ID),
    foreign key (club_ID) references Club(ID),
    foreign key (role_ID) references Role(ID)
);

create table Event (
    ID int auto_increment primary key,
    club_ID int not null,
    title varchar(200) not null,
    description varchar(500) not null,
    event_date datetime default current_timestamp,
    start_time timestamp default current_timestamp,
    end_time timestamp default current_timestamp,
    location_ID int not null,
    max_participants int not null,
    current_participants int default 0,
    status enum("open", "closed", "pending", "void") not null default "pending",
    is_private boolean not null,

    foreign key (club_ID) references Club(ID),
    foreign key (location_ID) references Location(ID)

);

create table Event_Registration (
    ID int auto_increment primary key,
    event_ID int not null,
    student_ID varchar(4) not null,
    registered_at timestamp default current_timestamp,
    registration_status enum("success", "rejected", "pending"),

    foreign key (event_ID) references Event(ID),
    foreign key (student_ID) references Student(ID)
);

create table Attendance (
    ID int auto_increment primary key,
    registration_ID int not null,
    checkin_time timestamp default current_timestamp,
    attendance_status enum("checked-in", "checked-out", "void"),

    foreign key (registration_ID) references Event_Registration(ID) 
);


create table Annoucement(
    ID int auto_increment primary key,
    club_ID int not null,
    author_ID varchar(8) not null,
    title varchar(55) not null,
    description varchar(1000) not null,

    foreign key (club_ID) references Club(ID),
    foreign key (author_ID) references Student(ID)
);

create table Feedback (
    ID int auto_increment primary key,
    from_user_ID varchar(8) not null,
    to_user_ID varchar(8) not null,
    on_event_ID int not null,
    at_timestamp timestamp default current_timestamp,

    foreign key (from_user_ID) references Student(ID),
    foreign key (to_user_ID) references Student(ID),
    foreign key (on_event_ID) references Event(ID)
);
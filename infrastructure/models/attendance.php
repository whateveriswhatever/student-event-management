<?php
    require_once root_dir . "/config/database-config.php";
    require_once root_dir . "/models/base.php";

    enum AttendanceStatus: string {
        case CHECKIN = "checked-in";
        case CHECKOUT = "checked-out";
        case VOID = "void";
    };

    class Attendance {
        private ?int $ID;
        private int $registeredID;
        private DateTime $checkinTime;
        private AttendanceStatus $status;

        public function __construct(int $rID, DateTime $cT, AttendanceStatus $s, ?int $id = null) {

        }

        private function setID(int $id): void {
            if ($id !== null && $id > 0) $this->ID = $id; 
        }

        private function setRegisteredID(int $id): void {
            if($id > 0)  {$this->registeredID = $id;} else {throw new InvalidArgumentException("ID can't be less than 1!");}
        }

        private function setCheckinTime(DateTime $x): void {
            $this->checkinTime = $x;
        }

        private function setAttendanceStatus(AttendanceStatus $s): void {
            $this->status = $s;
        }

        public function getID(): ?int {return $this->ID;}
        public function getRegisteredID(): int {return $this->registeredID;}
        public function getCheckinTime(): DateTime {return $this->checkinTime;}
        public function getStatus(): AttendanceStatus {return $this->status;}
    }

    class AttendanceRepository extends BaseRepository {
        public function __construct() {
            parent::__construct()
        }
    }
?>
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
            $this->setID($id);
            $this->setRegisteredID($rID);
            $this->setCheckinTime($cT);
            $this->setAttendanceStatus($s);
        }

        private function setID(?int $id): void {
            if ($id !== null && $id <= 0) throw new InvalidArgumentException("ID must be positive");
            $this->ID = $id; 
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
            parent::__construct("attendance");
        }

        public function create(int $rID, DateTime $cT, AttendanceStatus $s): ?Attendance {
            $attendance = new Attendance(
                $rID,
                $cT,
                $s
            );
            $isSuccess = $this->add([
                "registration_ID" => (int)$attendance->getRegisteredID(),
                "checkin_time" => $attendance->getCheckinTime()->format("Y-m-d H:i:s"),
                "attendance_status" => ($attendance->getStatus())->value
            ]);

            if (!$isSuccess) throw new RuntimeException("Failed to create a new attendance!");
            $generatedID = $this->getLatestID();
            return new Attendance (
                $rID,
                $cT,
                $s, 
                $generatedID
            );
        }

        protected function hydrate(array $row): Attendance {
            if (empty($row)) throw new RuntimeException("Empty rows!");
            try {
                $checkinTime = new DateTime($row["checkin_time"]);
            } catch (Exception $ex) {
                throw new RuntimeException("Invalid check_in_time");
            } 
            return new Attendance(
                (int)$row["registration_ID"],
                $checkinTime,
                AttendanceStatus::from($row["attendance_status"]),
                (int)$row["ID"]
            );
        }

        public function getAllCheckin(): array {
            $rows = $this->findViaCriteria(["attendance_status" => (AttendanceStatus::CHECKIN)->value]);
            try {
                return array_map(
                    fn ($each) => $this->hydrate($each), $rows
                );
            } catch (RuntimeException $ex) {
                error_log($ex->getMessage());
                throw $ex;
            }
        }

        public function getAllCheckout(): array {
            $rows = $this->findViaCriteria(["attendance_status" => (AttendanceStatus::CHECKOUT)->value]);
            try {
                return array_map(
                    fn ($each) => $this->hydrate($each), $rows
                );
            } catch (RuntimeException $ex) {
                error_log($ex->getMessage());
                throw $ex;
            }
        }

        public function getAllVoid(): array {
            $rows = $this->findViaCriteria(["attendance_status" => (AttendanceStatus::VOID)->value]);
            try {
                return array_map(
                    fn ($each) => $this->hydrate($each), $rows
                );
            } catch (RuntimeException $ex) {
                error_log($ex->getMessage());
                throw $ex;
            }
        }

        public function findByTimestamp(AttendanceStatus $s, DateTime $tl): array {
            $all = $this->findViaCriteria([
                "attendance_status" => $s->value,
                "checkin_time" => $tl->format("Y-m-d H:i:s")
            ]);

            $instances = array_map(
                fn ($each) => $this->hydrate($each), $all
            );
            return $instances;
        }
    }
?>
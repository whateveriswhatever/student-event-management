<?php
    require_once root_dir . "/config/database-config.php";
    require_once root_dir . "/models/base.php";

    enum EventStatus: string {
        case OPEN = "open";
        case CLOSED = "closed";
        case PENDING = "pending";
        case VOID = "void";
    };

    enum RegisteredStatus: string {
        case SUCCESS = "success";
        case REJECT = "rejected";
        case PENDING = "pending";
    };


    class Event {
        private ?int $ID;
        private int $clubID;
        private string $title;
        private string $description;
        private DateTime $eventDate;
        private string $startTime;
        private string $endTime;
        private int $locationID;
        private int $maxParticipants;
        private string $status = EventStatus::VOID->value;

        public function __construct(
            ?int $ID = null,
            int $cID,
            string $t,
            string $d,
            DateTime $eD,
            string $sT,
            string $eT,
            int $lID,
            int $mP,
            ?string $s = null
        ) {
            $this->ID = $ID;
            $this->setClubID($cID);
            $this->setTitle($t);
            $this->setDescription($d);
            $this->setEventDate($eD);
            $this->setStartTime($sT);
            $this->setEndTime($eT);
            $this->setLocationID($lID);
            $this->setMaxPariticipants($mP);
            $this->setStatus($s); 
        }

        private function isValidTime(string $time): bool {
            return preg_match("/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/", $time);
        }

        private function setClubID(int $cID): void {
            if ($cID < 1) throw new InvalidArgumentException("Invalid ID!");
            $this->clubID = $cID;
        }

        private function setTitle(string $t): void {
            if (strlen($t) > 200 || strlen(trim($t)) === 0) throw new InvalidArgumentException("Invalid event title!");
            $this->title = $t;
        }

        private function setDescription(string $d): void {
            if (strlen($d) > 500) throw new InvalidArgumentException("Exceeds maximum length!");
            $this->description = $d;
        }

        private function setEventDate(DateTime $eD): void {
            $this->eventDate = $eD;
        }

        private function setStartTime(string $sT): void {
            $isValidated = $this->isValidTime($sT);
            if (!$isValidated) throw new InvalidArgumentException("Invalid time format!");
            $this->startTime = $sT;
        }

        private function setEndTime(string $eT): void {
            $isValidated = $this->isValidTime($eT);
            if (!$isValidated) throw new InvalidArgumentException("Invalid time format!");
            $startTime = $this->getStartTime();
            if ($startTime >= $eT) throw new InvalidArgumentException("Ending time period must be greater than starting time period!");
            $this->endTime = $eT;
        }

        private function setLocationID(int $id): void {
            if ($id < 0) throw new InvalidArgumentException("Invalid value for location ID!");
            $this->locationID = $id;
        }

        private function setMaxPariticipants(int $x): void {
            if ($x < 5) throw new InvalidArgumentException("Minimum participants is at least 5!");
            $this->maxParticipants = $x;
        }

        private function closeEvent(): void {
            $this->status = (EventStatus::CLOSED)->value;
        }

        private function pendingEvent(): void {
            $this->status = (EventStatus::PENDING)->value;
        }

        private function openEvent(): void {
            $this->status = (EventStatus::OPEN)->value;
        }

        public function setStatus(string $case): void {
            // void by default 
            switch ($case) {
                case "open":
                    $this->openEvent();
                    break;
                case "closed":
                    $this->closeEvent();
                    break;
                case "pending":
                    $this->pendingEvent();
                    break;
                default:
                    $this->status = (EventStatus::VOID)->value;
                    break;
            }

        }

        public function getID(): ?int {
            return $this->ID;
        }

        public function getClubID(): int {
            return $this->clubID;
        }

        public function getTitle(): string {
            return $this->title;
        }

        public function getDescription(): string {
            return $this->description;
        }

        public function getEventDate(): DateTime {
            return $this->eventDate;
        }

        public function getStartTime(): string {
            return $this->startTime;
        }

        public function getEndTime(): string {
            return $this->endTime;
        }

        public function getLocationID(): int {
            return $this->locationID;
        }

        public function getMaxParticipants(): int {
            return $this->maxParticipants;
        }

        public function getStatus(): string {
            return $this->status;
        }
    }

    class EventRepository extends BaseRepository {
        public function __construct() {
            parent::__construct("event");
        }

        public function create(
            int $clubID,
            string $t,
            string $d,
            DateTime $eD,
            string $sT,
            string $eT,
            int $lID,
            int $mP,
            string $s
        ): Event {
            $event = new Event(
                null,
                $clubID,
                $t,
                $d, 
                $eD,
                $sT,
                $eT,
                $lID,
                $mP,
                $s
            );
            $isSuccess = $this->add([
                "club_ID" => $event->getClubID(),
                "title" => $event->getTitle(),
                "description" => $event->getDescription(),
                "event_date" => $event->getEventDate()->format("Y-m-d"),
                "start_time" => $event->getStartTime(),
                "end_time" => $event->getEndTime(),
                "location_ID" => $event->getLocationID(),
                "max_participants" => $event->getMaxParticipants(),
                "status" => $event->getStatus()
            ]);

            if (!$isSuccess) throw new RuntimeException("Failed to create event");

            $generatedID = (int)$this->dbConnection->lastInsertId();
            return new Event(
                $generatedID,
                $clubID,
                $t,
                $d,
                $eD,
                $sT,
                $eT,
                $lID,
                $mP,
                $s
            );
        }

        public function findByID(int $id): ?Event {
            $data = $this->findViaCriteria(["ID" => $id]);
            if (empty($data)) return null;
            return $data[0];
        }

        public function findAllFromClub(int $cID): array {
            $rows = $this->findViaCriteria(["club_ID" => $cID]);
            return array_map(
                fn($row) => $this->$row, $rows
            );
        }

        public function updateStatus(int $eID, string $s): bool {
            return $this->updateViaCriteria(["status" => $s], ["ID" => $eID]);
        }


    }

    class EventRegistration {
        private ?int $ID;
        private int $eventID;
        private int $studentID;
        private DateTime $registeredAt;
        private string $status;

        public function __construct(?int $id = null, int $eID, int $sID, DateTime $rdAt, ?string $s = null) {
            $this->ID = $id;
            $this->setEventID($eID);
            $this->setStudentID($sID);
            $this->setRegisteredTime($rdAt);
            $this->setStatus($s);
        }

        private function setEventID(int $eID): void {
            if ($eID < 1) throw new InvalidArgumentException("Invalid event ID");
            $this->eventID = $eID;
        }

        private function setStudentID(int $sID): void {
            if ($sID < 1) throw new InvalidArgumentException("Invalid event ID");
            $this->studentID = $sID;
        }

        private function setRegisteredTime(DateTime $x): void {
            $this->registeredAt = $x;
        }

        private function markSuccess(): void {
            $this->status = (RegisteredStatus::SUCCESS)->value;
        }

        private function markPending(): void {
            $this->status = (RegisteredStatus::PENDING)->value;
        }

        private function markReject(): void {
            $this->status = (RegisteredStatus::REJECT)->value;
        }

        public function setStatus(string $s): void {
            switch ($s) {
                case "success":
                    $this->markSuccess();
                    break;
                case "pending":
                    $this->markPending();
                    break;
                case "reject":
                    $this->markReject();
                    break;
                default:
                    $this->markPending();
                    break;
            }
        }

        public function getID(): ?int {return $this->ID;}
        public function getEventID(): int {return $this->eventID;}
        public function getStudentID(): int {return $this->studentID;}
        public function getRegisteredAt(): DateTime {return $this->registeredAt;}
        public function getStatus(): string {return $this->status;}
    }

   

    class EventRegistrationRepository extends BaseRepository {
        public function __construct() {
            parent::__construct("event_registration");
        }

        public function register(int $eID, int $sID, DateTime $rdAt, ?string $s = null): EventRegistration {
            $registration = new EventRegistration(
                null,
                $eID,
                $sID,
                $rdAt,
                $s
            );
            $isSuccess = $this->add([
                "event_ID" => $registration->getEventID(),
                "student_ID" => $registration->getStudentID(),
                "registered_at" => $registration->getRegisteredAt()->format("Y-m-d H:i:s"),
                "registration_status" => $registration->getStatus()
            ]);
            
            if (!$isSuccess) {
                throw new RuntimeException("Failed to register for new event!");
            }

            $generatedID = (int)$this->dbConnection->lastInsertId();
            return new EventRegistration(
                $generatedID,
                $eID,
                $sID,
                $rdAt,
                $s
            );
        }

        public function findAllRegistrationsFromEvent(int $eID): array {
            $rows = $this->findViaCriteria(["ID" => $eID]);
            return array_map(
                fn($row) => $row, $rows
            );
        }

        public function updateRegistrationStatus(int $rID, RegisteredStatus $s): bool {
            return $this->updateViaCriteria([
                "registration_status" => $s->value
            ], ["ID" => $rID]);
        }
    }
?>
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
        private DateTime $startTime;
        private DateTime $endTime;
        private int $locationID;
        private int $maxParticipants;
        private EventStatus $status;

        public function __construct(
            int $cID,
            string $t,
            string $d,
            DateTime $eD,
            DateTime $sT,
            DateTime $eT,
            int $lID,
            int $mP,
            ?EventStatus $s = null,
            ?int $ID = null,
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

        private function setStartTime(DateTime $sT): void {
            if ($this->getEndTime() !== null) {
                if ($sT > $this->getEndTime()) throw new RuntimeException("Invalid time!");
                $this->startTime = $sT;
            } else {
                $this->startTime = $sT;
            }
        }

        private function setEndTime(DateTime $eT): void {
            if ($this->getStartTime() !== null) {
                if ($eT < $this->getEndTime()) throw new RuntimeException("Invalid time!");
                $this->endTime = $eT;
            }
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
            $this->status = EventStatus::CLOSED;
        }

        private function pendingEvent(): void {
            $this->status = EventStatus::PENDING;
        }

        private function openEvent(): void {
            $this->status = EventStatus::OPEN;
        }

        public function setStatus(EventStatus $x): void {
            if ($x === null) {
                $this->status = EventStatus::VOID;
            } else {
                $this->status = $x;
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

        public function getStartTime(): DateTime {
            return $this->startTime;
        }

        public function getEndTime(): DateTime {
            return $this->endTime;
        }

        public function getLocationID(): int {
            return $this->locationID;
        }

        public function getMaxParticipants(): int {
            return $this->maxParticipants;
        }

        public function getStatus(): EventStatus {
            return $this->status;
        }
    }

    class EventRepository extends BaseRepository {
        public function __construct() {
            parent::__construct("event");
        }

        public function create(
            int $cID,
            string $t,
            string $d,
            DateTime $eD,
            DateTime $sT,
            DateTime $eT,
            int $lID,
            int $mP,
            EventStatus $s
        ): Event {
            $event = new Event(
                $cID,
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
                "status" => ($event->getStatus())->value
            ]);

            if (!$isSuccess) throw new RuntimeException("Failed to create event");

            $generatedID = (int)$this->dbConnection->lastInsertId();
            return new Event(
                $cID,
                $t,
                $d,
                $eD,
                $sT,
                $eT,
                $lID,
                $mP,
                $s,
                $generatedID
            );
        }

        public function findByID(int $id): ?Event {
            $data = $this->findViaCriteria(["ID" => $id]);
            if (empty($data)) return null;
            return $this->hydrate($data[0]);
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

        #[Override]
        protected function hydrate(array $row): Event {
            if (empty($row)) throw new RuntimeException("Empty row!");

            try {
                $eD = new DateTime($row["event_date"]);
                $sT = new DateTime($row["start_time"]);
                $eT = new DateTime($row["end_time"]);

                $event = new Event(
                    (int)$row["club_id"],
                    (string)$row["title"],
                    (string)$row["description"],
                    $eD,
                    $sT,
                    $eT,
                    (int)$row["location_ID"],
                    (int)$row["max_participants"],
                    EventStatus::from($row["status"]),
                    (int)$row["ID"]
                );
                return $event;
            } catch (PDOException $ex) {
                error_log($ex->getMessage());
                throw new RuntimeException("Invalid founded date!");
            }
        }
    }

    class EventRegistration {
        private ?int $ID;
        private int $eventID;
        private int $studentID;
        private DateTime $registeredAt;
        private RegisteredStatus $status;

        public function __construct(int $eID, int $sID, DateTime $rdAt, ?RegisteredStatus $s = null, ?int $id = null) {
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
            $this->status = RegisteredStatus::SUCCESS;
        }

        private function markPending(): void {
            $this->status = RegisteredStatus::PENDING;
        }

        private function markReject(): void {
            $this->status = RegisteredStatus::REJECT;
        }

        public function setStatus(?RegisteredStatus $s, ?string $typeMarking = null): void {
            if (($typeMarking || $typeMarking !== null) && ($s === null || !$s)) {
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

            if ((!$typeMarking || $typeMarking === null) && ($s || $s !== null)) {
                $this->status = $s;
            }
        }

        public function getID(): ?int {return $this->ID;}
        public function getEventID(): int {return $this->eventID;}
        public function getStudentID(): int {return $this->studentID;}
        public function getRegisteredAt(): DateTime {return $this->registeredAt;}
        public function getStatus(): RegisteredStatus {return $this->status;}
    }

   

    class EventRegistrationRepository extends BaseRepository {
        private EventRepository $eventRepo;
        public function __construct() {
            parent::__construct("event_registration");
            $this->eventRepo = new EventRepository();
        }

        public function register(int $eID, int $sID, DateTime $rdAt, ?string $s = null): EventRegistration {
            $registration = new EventRegistration(
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
                $eID,
                $sID,
                $rdAt,
                $s,
                $generatedID
            );
        }

        public function findAllRegistrationsFromStudent(int $sID): array {
            $rows = $this->findViaCriteria(["student_ID" => $sID]);
            return array_map(
                fn($row) => $this->hydrate($row), $rows
            );
        }

        
        public function updateRegistrationStatus(int $rID, RegisteredStatus $s): bool {
            return $this->updateViaCriteria([
                "registration_status" => $s->value
            ], ["ID" => $rID]);
        }

        protected function hydrate(array $row): EventRegistration {
            if (empty($row)) throw new RuntimeException("Empty row!");

            try {
                $rA = new DateTime($row["registered_at"]);
                $status = RegisteredStatus::from($row["registration_status"]);

                $event = new EventRegistration(
                    (int)$row["event_ID"],
                    (int)$row["student_ID"],
                    $rA,
                    $status
                );
                return $event;
            } catch (PDOException $ex) {
                error_log($ex->getMessage());
                throw new RuntimeException("Invalid founded date!");
            }
        }

        public function findAllEventsFromAStudent(int $sID): array {
            $rows = $this->findViaCriteria(["student_ID" => $sID]);
            $eventIds = array_map(
                fn ($row) => $row["event_ID"], $rows
            );
            $events = array_map(
                fn ($id) =>($this->eventRepo)->findByID($id), $eventIds
            );
            return $events;
        }
    }
?>
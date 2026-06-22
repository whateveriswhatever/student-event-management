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
        private int $currParticipants;
        private EventStatus $status;
        private bool $isPrivate;

        private array $timeline = [
            "open"  => null,
            "end"   => null
        ];

        public function __construct(
            int $cID,
            string $t,
            string $d,
            DateTime $eD,
            DateTime $sT,
            DateTime $eT,
            int $lID,
            int $mP,
            ?int $cP = 0,
            ?EventStatus $s = null,
            ?int $ID = null,
            ?bool $iPt = false
        ) {
            $this->setID($ID);
            $this->setClubID($cID);
            $this->setTitle($t);
            $this->setDescription($d);
            $this->setEventDate($eD);
            $this->setStartTime($sT);
            $this->setEndTime($eT);
            $this->setLocationID($lID);
            $this->setMaxPariticipants($mP);
            $this->setCurrParticipants($cP);
            $this->setStatus($s); 
            $this->setPrivacy($iPt);
        }

        private function setID(?int $id): void {
            $this->ID = $id;
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
            if (isset($this->timeline["end"])) {
                if ($sT > $this->timeline["end"]) throw new RuntimeException("Invalid time!");
                $this->startTime = $sT; 
            } else {
                $this->startTime = $sT;
            }
            $this->startTime = $sT;
        }

        private function setEndTime(DateTime $eT): void {
            if (isset($this->timeline["open"])) {
                if ($eT < $this->timeline["open"]) throw new RuntimeException("Invalid time!");
                $this->endTime = $eT;
            } else {
                $this->endTime = $eT;
            }
            
        }

        private function setLocationID(int $id): void {
            if ($id < 0) throw new InvalidArgumentException("Invalid value for location ID!");
            $this->locationID = $id;
        }

        private function setMaxPariticipants(int $x): void {
            if ($x < 5) throw new InvalidArgumentException("Minimum participants is at least 5!");
            $this->maxParticipants = $x;
        }

        private function setCurrParticipants(?int $x = 0): void {
            $this->currParticipants = $x;
        }

        private function setPrivacy(?bool $x = false): void {
            $this->isPrivate = $x;
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
            $this->status = $x;
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

        public function getCurrParticipants(): int {
            return $this->currParticipants;
        }

        public function getPrivacyMode(): bool {
            return $this->isPrivate;
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
            ?int $cP = 0,
            EventStatus $s,
            ?bool $iPt = false
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
                $cP,
                $s,
                $iPt
            );

            $isSuccess = $this->add([
                "club_ID" => $event->getClubID(),
                "title" => $event->getTitle(),
                "description" => $event->getDescription(),
                "event_date" => $event->getEventDate()->format("Y-m-d"),
                "start_time" => $event->getStartTime()->format("Y-m-d H:i:s"),
                "end_time" => $event->getEndTime()->format("Y-m-d H:i:s"),
                "location_ID" => $event->getLocationID(),
                "max_participants" => $event->getMaxParticipants(),
                "current_participants" => $event->getCurrParticipants(),
                "status" => ($event->getStatus())->value,
                "is_private" => ($event->getPrivacyMode())
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
                $cP,
                $s,
                $generatedID,
                $iPt
            );
        }

        public function findByID(int $id): ?Event {
            $data = $this->findViaCriteria(["ID" => $id]);
            if (empty($data)) return null;
            return $this->hydrate($data[0]);
        }

        public function findAllFromClub(int $cID): array {
            $rows = $this->findViaCriteria(["club_ID" => $cID]);
            if (empty($rows)) return [];
            return array_map(
                fn($row) => $this->hydrate($row), $rows
            );
        }

        public function updateStatus(int $eID, string $s): bool {
            return $this->updateViaCriteria(["status" => $s], ["ID" => $eID]);
        }

        #[Override]
        public function hydrate(array $row): Event {
            if (empty($row)) throw new RuntimeException("Empty row!");

            try {
                $eD = new DateTime($row["event_date"]);
                $sT = new DateTime($row["start_time"]);
                $eT = new DateTime($row["end_time"]);

                $event = new Event(
                    (int)$row["club_ID"],
                    (string)$row["title"],
                    (string)$row["description"],
                    $eD,
                    $sT,
                    $eT,
                    (int)$row["location_ID"],
                    (int)$row["max_participants"],
                    (int)$row["current_participants"],
                    EventStatus::from($row["status"]),
                    (int)$row["ID"]
                );
                return $event;
            } catch (PDOException $ex) {
                error_log($ex->getMessage());
                throw new RuntimeException("Invalid founded date!");
            }
        }

        public function updateEventStatus(int $eID, EventStatus $status): bool {
            return $this->updateViaCriteria(
                ["status" => $status->value], 
                ["ID" => $eID]);
        }

        public function increaseCurrentParticipants(int $eID): bool {
            $event = $this->findByID($eID);
            $curr = $event->getCurrParticipants();
            $curr++;
            return $this->updateViaCriteria(
                ["current_participants" => $curr],
                ["ID" => $eID]
            );
        }

        public function updatePrivacyViaID(int $eID, bool $isPrt): bool {
            return $this->updateViaCriteria(
                ["is_private" => $isPrt],
                ["ID" => $eID]
            );
        }

        public function findAll(): array {
            $rows = $this->all();
            return array_map(
                fn ($row) => $this->hydrate($row), $rows
            );
        }

        public function searchByName(string $keyword): array {
            $stmt = ($this->dbConnection)->prepare(
                "select
                    *
                from event
                where title like :keyword
                order by event_date asc"
            );
            $stmt->execute(["keyword" => '%' . $keyword . '%']);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array_map(
                fn ($row) => $this->hydrate($row), $rows
            );
        }
    }

    class EventRegistration {
        private ?int $ID;
        private int $eventID;
        private string $studentID;
        private DateTime $registeredAt;
        private RegisteredStatus $status;

        public function __construct(int $eID, string $sID, DateTime $rdAt, ?RegisteredStatus $s = null, ?int $id = null) {
            $this->ID = $id;
            $this->setEventID($eID);
            $this->setStudentID($sID);
            $this->setRegisteredTime($rdAt);
            if ($s instanceof RegisteredStatus) {
                $this->setStatus($s);
            } else {
                $this->setStatus(null, $s);
            }
        }

        private function setEventID(int $eID): void {
            if ($eID < 1) throw new InvalidArgumentException("Invalid event ID");
            $this->eventID = $eID;
        }

        private function setStudentID(string $sID): void {
            if (empty($sID)) throw new InvalidArgumentException("Invalid student ID");
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
            /* 
                Constraints:
                    - Students are free to assign for any events they favour.
                    - They get rejected only when the current amount of participants reach to defined limit.
            */
            if (($typeMarking || $typeMarking !== null) && ($s === null || !$s)) {
                switch ($s) {
                    case "success":
                        $this->markSuccess();
                        break;
                    case "pending":
                        $this->markPending();
                        break;
                    case "reject":
                    case "rejected":
                        $this->markReject();
                        break;
                    default:
                        $this->markPending();
                        break;
                }
                return;
            }

            // Nếu truyền enum $s trực tiếp → gán thẳng
            if ($s !== null) {
                $this->status = $s;
                return;
            }

            // Mặc định: pending
            $this->markPending();
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
                RegisteredStatus::from($s)
            );
            $isSuccess = $this->add([
                "event_ID" => $registration->getEventID(),
                "student_ID" => $registration->getStudentID(),
                "registered_at" => $registration->getRegisteredAt()->format("Y-m-d H:i:s"),
                "registration_status" => $registration->getStatus()->value
            ]);
            
            if (!$isSuccess) {
                throw new RuntimeException("Failed to register for new event!");
            }
            // Increasing the current participants of the registed event
            $isIncrease = ($this->eventRepo)->increaseCurrentParticipants($registration->getEventID());
            $generatedID = (int)$this->dbConnection->lastInsertId();
            return new EventRegistration(
                $eID,
                $sID,
                $rdAt,
                RegisteredStatus::from($s),
                $generatedID
            );
        }

        public function findAllRegistrationsFromStudent(string $sID): array {
            $rows = $this->findViaCriteria(["student_ID" => $sID]);
            return array_map(
                fn($row) => $this->hydrate($row), $rows
            );
        }
        
        public function checkRegistration(string $sID, int $eID): bool {
            $row = $this->findViaCriteria(
                [
                    "student_ID"    => $sID,
                    "event_ID"      => $eID
                ]
            );
            if (!empty($row)) {
                return true;
            } else {
                return false;
            }
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
                    (string)$row["student_ID"],
                    $rA,
                    $status,
                    (int)$row["ID"]
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
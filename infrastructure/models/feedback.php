<?php 
    require_once root_dir . "/config/database-config.php";
    require_once root_dir . "/models/base.php";

    class Feedback {
        private ?int $ID;
        private string $fromID;
        private ?string $toID;
        private int $onEventID;
        private DateTime $timestamp;
        private string $content;
        
        public function __construct(string $fID, ?string $tID, string $c, int $onEID, DateTime $t, ?int $id = null) {
            $this->setID($id);
            $this->setFromID($fID);
            $this->setToID($tID);
            $this->setContent($c);
            $this->setOnEventID($onEID);
            $this->setEventDate($t);
        }

        private function setID(?int $id): void {
            $this->ID = $id;
        } 

        private function setFromID(string $id): void {
            $this->fromID = $id;
        }

        private function setToID(?string $id): void {
            $this->toID = $id;
        }

        private function setOnEventID(int $id): void {
            if ($id < 1) {
                throw new InvalidArgumentException(
                    "Invalid event ID"
                );
            }

            $this->onEventID = $id;
        }

        private function setContent(string $content): void {
             $content = trim($content);

            if (strlen($content) === 0) {
                throw new InvalidArgumentException(
                    "Feedback content cannot be empty"
                );
            }

            if (strlen($content) > 1000) {
                throw new InvalidArgumentException(
                    "Feedback content too long"
                );
            }

            $this->content = $content;
        }

        private function setEventDate(DateTime $t): void {
            $this->timestamp = $t;
        }

        public function getID(): int {return $this->ID;}
        public function getFromID(): string {return $this->fromID;}
        public function getToID(): string {return $this->toID;}
        public function getEventID(): int {return $this->onEventID;}
        public function getEventDate(): DateTime {return $this->timestamp;}
        public function getContent(): string {return $this->content;}
    }

    class FeedBackRepository extends BaseRepository {
        public function __construct() {
            parent::__construct("feedback");
        }

        #[Override]
        public function hydrate(array $row): Feedback
        {
            return new Feedback(
                (string)$row["from_user_ID"],
                (string)$row["to_user_ID"],
                (string)$row["content"],
                (int)$row["on_event_ID"],
                new DateTime($row["at_timestamp"]),
                (int)$row["ID"]
            );
        }

        public function create(
            string $fID,
            int $oED,
            string $c,
            ?string $tID = null
        ): Feedback {
            $currTimestamp = new DateTime();
            $feedback = new Feedback(
                $fID,
                $tID,
                $c,
                $oED,
                $currTimestamp,
                null
            );
            $isSuccess = $this->add(
                [
                    "from_user_ID" => $feedback->getFromID(),
                    "to_user_ID" => $feedback->getToID(),
                    "on_event_ID" => (int)$feedback->getEventID(),
                    "at_timestamp" => ($feedback->getEventDate())->format("Y-m-d H:i:s"),
                    "content" => $feedback->getContent()
                ]
            );
            if (!$isSuccess) {
                throw new RuntimeException("
                    Failed to create new feedback!
                ");
            }
            $generatedID = (int)$this->getLatestID();
            return new Feedback(
                $fID,
                $tID,
                $c,
                $oED,
                $currTimestamp,
                $generatedID 
            );
        }

        public function findByID(int $id): ?Feedback {
            $row = $this->findViaCriteria(["ID" => $id]);
            if (empty($row)) {
                return null;
            }
            return $this->hydrate($row);
        }

        public function findAllFromEvent(int $eventID): array {
            $rows = $this->findViaCriteria([
                "on_event_ID" => $eventID
            ]);
            return array_map(
                fn ($row) => $this->hydrate($row), $rows
            );
        }
        
        public function findAllFromUser(string $userID): array {
            $rows = $this->findViaCriteria([
                "from_user_ID" => $userID
            ]);
            return array_map(
                fn ($row) => $this->hydrate($row), $rows
            );
        }

        public function findAllToStudent(string $userID): array {
            $rows = $this->findViaCriteria([
                "to_user_ID" => $userID
            ]);
            return array_map(
                fn ($row) => $this->hydrate($row), $rows
            );
        }


        public function deleteFeedback(int $id): bool {
            return $this->deleteViaCriteria(["ID" => $id]);
        }

        public function timeElapsedString(DateTime $dt): string {
            $now = new DateTime();
            $diff = $now->diff($dt);

            if ($diff->y > 0) return $diff->y . "y ago";
            if ($diff->m > 0) return $diff->m . "mo ago";
            if ($diff->d > 0) return $diff->d . "d ago";
            if ($diff->h > 0) return $diff->h . "h ago";
            if ($diff->i > 0) return $diff->i . "m ago";
            return "Just now";
        }

        // This method is used to convert data objects to arrays (useful to send data to the frontend under JSON format)
        public function toArray(Feedback $obj): array {
            return [
                "ID"            => $obj->getID(),
                "from_user_ID"  => $obj->getFromID(),
                "to_user_ID"    => $obj->getToID(),
                "on_event_ID"   => $obj->getEventID(),
                "at_timestamp"  => $obj->getEventDate()->format("Y-m-d H:i:s"),
                "content"       => $obj->getContent()
            ];
        }
    }
?>
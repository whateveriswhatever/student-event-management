<?php 
    require_once root_dir . "/config/database-config.php";
    require_once root_dir . "/models/base.php";

    class Feedback {
        private ?int $ID;
        private int $fromID;
        private int $toID;
        private int $onEventID;
        private DateTime $timestamp;
        private string $content;
        
        public function __construct(int $fID, int $tID, string $c, int $onEID, DateTime $t, ?int $id = null) {
            $this->setID($id);
            $this->setFromID($fID);
            $this->setToID($tID);
            $this->setContent($c);
            $this->setOnEventID($onEID);
            $this->setEventDate($t);
        }

        private function setID(int $id): void {
            $this->ID = $id;
        } 

        private function setFromID(int $id): void {
            if ($id < 1) {

                throw new InvalidArgumentException(
                    "Invalid sender ID"
                );
            }

            $this->fromID = $id;
        }

        private function setToID(int $id): void {
            if ($id < 1) {

                throw new InvalidArgumentException(
                    "Invalid receiver ID"
                );
            }

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
        public function getFromID(): int {return $this->fromID;}
        public function getToID(): int {return $this->toID;}
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
                (int)$row["from_user_ID"],
                (int)$row["to_user_ID"],
                (int)$row["on_event_ID"],
                (string)$row["content"],
                new DateTime($row["at_timestamp"]),
                (int)$row["ID"]
            );
        }

        public function create(
            int $fID,
            int $tID,
            int $oED,
            string $c
        ): Feedback {
            $currTimestamp = new DateTime();
            $feedback = new Feedback(
                $fID,
                $tID,
                $c,
                $oED,
                $currTimestamp
            );
            $isSuccess = $this->add(
                [
                    "from_user_ID" => $feedback->getFromID(),
                    "to_user_ID" => $feedback->getToID(),
                    "on_event_ID" => $feedback->getEventID(),
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
                "to_event_ID" => $eventID
            ]);
            return array_map(
                fn ($row) => $this->hydrate($row), $rows
            );
        }
        
        public function findAllFromUser(int $userID): array {
            $rows = $this->findViaCriteria([
                "from_user_ID" => $userID
            ]);
            return array_map(
                fn ($row) => $this->hydrate($row), $rows
            );
        }

        public function findAllToStudent(int $userID): array {
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


    }
?>
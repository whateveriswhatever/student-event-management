<?php
    require_once root_dir . "/models/base.php";
    require_once root_dir . "/config/database-config.php";

    class Announcement {
        private int $ID;
        private int $authorID;
        private int $clubID;
        private string $title;
        private string $description;

        public function __construct(?int $id, int $aID, int $cID, string $t, string $d) {
            $this->setAuthorID($aID);
            $this->setClubID($cID);
            $this->setTitle($t);
            $this->setContent($d);
            $this->assignID($id);
        }

        private function assignID(int $id): void {
            if ($this->ID !== null) {
                throw new LogicException("ID already assigned!");
            }

            $this->ID = $id;
        }

        private function setAuthorID(int $id): void {
            if ($id < 1) throw new InvalidArgumentException("Invalid ID, can't be negative!");
            $this->authorID = $id;
        }

        private function setTitle(string $t): void {
            if (strlen($t) > 55) throw new InvalidArgumentException("Exceeds maximum length!");
            $this->title = $t;
        }

        private function setContent(string $d): void {
            if (strlen($d) > 1000) throw new InvalidArgumentException("Exceeds maximum length!");
            $this->description = $d;
        }

        private function setClubID(int $id): void {
            if ($id < 1) throw new InvalidArgumentException("Invalid ID, can't be negative!");
            $this->clubID = $id;
        }

        public function getID(): int {return $this->ID;}
        public function getAuthorID(): int {return $this->authorID;}
        public function getTitle(): string {return $this->title;}
        public function getContent(): string {return $this->description;}

    }

    class AnnouncementRepository extends BaseRepository {
        public function __construct() {
            parent::__construct("announcement");
        }

        // public function addOne(array $data): bool {
        //     $isSuccess = $this->add($data);
        //     if (!$isSuccess) return false;

        // }

        public function findAllFromAuthorViaID(int $authorID): array {
            if ($authorID < 1) throw new InvalidArgumentException("Invalid user ID!"); 
            $data = $this->findViaCriteria(["author_ID" => $authorID]);
            if (empty($data)) return [];
            return array_map(
                fn($row) => new Announcement(
                    $row["ID"],
                    $row["club_ID"],
                    $row["author_ID"],
                    $row["title"],
                    $row["description"]
                ),
                $data
            );
        }

        public function findAllFromClubViaID(int $clubID): array {
            if ($clubID < 1) throw new InvalidArgumentException("Invalid club ID!"); 
            $data = $this->findViaCriteria(["club_ID" => $clubID]);
            if (empty($data)) return [];
            return $data;
        }

        #[Override]
        protected function hydrate(array $row): Announcement
        {
            return new Announcement(
                (int)$row["ID"],
                (int)$row["club_ID"],
                (int)$row["author_ID"],
                (string)$row["title"],
                (string)$row["description"]
            );
        }
    }
?>
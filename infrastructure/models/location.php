<?php
    require_once root_dir . "/config/database-config.php";
    require_once root_dir . "/models/base.php";

    class Location extends BaseModel {
        private int $ID;
        private string $building;
        private string $room;
        private int $attendanceCapacity;

        public function __construct(string $b, string $r, int $a, ?int $id = null) {
            $this->setID($id);
            $this->setBuilding($b);
            $this->setRoom($r);
            $this->setCapacity($a);
        }

        private function setID(int $id): void {
            if ($id !== null) {
                $this->ID = $id;
            }
        }

        private function setBuilding(string $b): void {
            $specialChars = $this->doesContainSpecialChars($b);
            if ($specialChars) throw new InvalidArgumentException("The name of building can't contain special characters or symbols!");
            $this->building = $b; 
        }

        private function setRoom(string $r): void {
            $specialChars = $this->doesContainSpecialChars($r);
            if ($specialChars) throw new InvalidArgumentException("The name of building can't contain special characters or symbols!");
            $this->room = $r;
        }

        private function setCapacity(int $x): void {
            if ($x < 10) throw new InvalidArgumentException("Minimum amount of attendance is 10!");
            $this->attendanceCapacity = $x;
        }

        public function getID(): int {return $this->ID;}
        public function getBuilding(): string {return $this->building;}
        public function getRoom(): string {return $this->room;}
        public function getMaximumCapacity(): int {return $this->attendanceCapacity;}
    }

    class LocationRepository extends BaseRepository {
        public function __construct() {
            parent::__construct("location");
        }

        #[Override]
        public function hydrate(array $row): object
        {
            return new Location(
                (string)$row["building"],
                (string)$row["room"],
                (int)$row["attendance_capacity"],
                (int)$row["ID"]
            );
        }

        public function create(string $b, string $r, int $a): Location {
            $location = new Location($b, $r, $a);
            $isSuccess = $this->add(
                [
                    "building" => $b,
                    "room" => $r, 
                    "attendance_capacity" => $a
                ]
            );
            if (!$isSuccess) throw new RuntimeException("Failed to create new location!");

            $generatedID = $this->getLatestID();
            return new Location(
                $b, 
                $r,
                $a,
                $generatedID
            );
        }

        public function findByID(
            int $locationID
        ): ?Location {
            $rows = $this->findViaCriteria([
                "ID" => $locationID
            ]);

            if (empty($rows)) {
                return null;
            }

            return $this->hydrate(
                $rows[0]
            );
        }

        public function updateCapacity(int $id, int $x): bool {
            $isExisted = $this->isExist($id);
            if (!$isExisted) throw new InvalidArgumentException("ID is never existed!");
            if ($x < 10) throw new InvalidArgumentException("Minimum capacity for an event is 10!"); 
            return $this->updateViaCriteria(["attendance_capcity" => $x], ["ID" => $id]);
        }

        public function deleteLocation(int $id): bool {
            $isExisted = $this->isExist($id);
            if (!$isExisted) throw new InvalidArgumentException("ID is never existed!");
            return $this->deleteViaCriteria(["ID" => $id]);
        }
    }
?>
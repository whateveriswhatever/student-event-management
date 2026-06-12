<?php
    require_once root_dir . "/config/database-config.php";
    require_once root_dir . "/models/base.php";
    
    enum Status: string {
        case ACTIVE = "active";
        case LOW = "low";
        case CLOSED = "closed";
    };

    class Club extends BaseModel {
        private ?int $ID;
        private string $name;
        private string $description;
        private DateTime $foundedDate;
        private string $logoURL;
        private Status $status;
        private int $totalMembers;

        public function __construct(string $n, string $d, DateTime $fd, string $url, Status $s, ?int $id = null, ?int $total = 0) {
            $this->setName($n);
            $this->setDescription($d);
            $this->setFoundedDate($fd);
            $this->setLogoURL($url);
            $this->setStatus($s);
            $this->setID($id);
            $this->setTotalMembers($total);
        }

        private function setID(?int $id): void {
            // if ($id < 1 && $id === null) throw new InvalidArgumentException("Invalid ID");
            $this->ID = $id;
        }

        private function setName(string $n): void {
            if (strlen($n) > 55) throw new InvalidArgumentException("Invalid club name");
            $this->name = $n;
            
        }

        private function setDescription(string $d): void {
            if (strlen($d) > 555) throw new InvalidArgumentException("Exceed maximum length!");
            $this->description = $d;
        }

        private function setFoundedDate(DateTime $fd): void {
            // // dd-mm-yyyy
            // $parts = explode('-', $fd);
            // $day = $parts[0];
            // $month = $parts[1];
            // $year = $parts[2];
            // if (count($parts) !== 3) return false;
            // $validator = function (string $x, string $type): bool {
            //     $castedX = (int)$x; 
            //     if ($type === 'd') {
            //         if ($castedX < 1 || $castedX > 31) return false;
            //     } else if ($type === 'm') {
            //         if ($castedX < 1 || $castedX > 12) return false;
            //     } else if ($type === 'y') {
            //         if ($castedX < 1920 || $castedX > 2008) return false;
            //     }
            //     return true;
            // };
            // if (!$validator($day, 'd') || !$validator($month, 'm') || !$validator($year, 'y')) return false;
            // $daysInAMonth = cal_days_in_month(CAL_GREGORIAN, (int)$month, (int)$year);
            // if ((int)$day > $daysInAMonth) return false;
            // $this->foundedDate = $fd;
            // return true;
            $this->foundedDate = $fd;
        }

        private function setLogoURL(string $url): void {
            if (strlen($url) === 0) throw new InvalidArgumentException("URL address can not be empty!");
            $this->logoURL = $url;
        }

        private function setStatus(Status $s): void {
            $this->status = $s;
        }

        private function setTotalMembers(int $x): void {
            if ($x < 0) throw new InvalidArgumentException("Invalid input for total members!");
            else {$this->totalMembers = $x;}
        }

        public function getID(): int {return $this->ID;}
        public function getName(): string {return $this->name;}
        public function getDescription(): string {return $this->description;}
        public function getFoundedDate(): DateTime {return $this->foundedDate;}
        public function getLogoURL(): string {return $this->logoURL;}
        public function getStatus(): Status {return $this->status;}
        public function getTotalMembers(): int {return $this->totalMembers;}
    }

    class ClubRepository extends BaseRepository {
        public function __construct() {
            parent::__construct("club");
        }

        

        public function create(string $n, string $d, DateTime $fd, string $url, Status $s): ?Club {
            $isSuccess = $this->add([
                "name" => $n,
                "description" => $d,
                "founded_date" => $fd->format("Y-m-d H:i:s"),
                "status" => $s->value,
                "logo_url" => $url,
                "total_members" => 0
                ]);
            if ($isSuccess) {
                $generatedID = $this->getLatestID();
                return new Club ($n, $d, $fd, $url, $s, $generatedID, 0);
            } else {
                return null;
            }
        }

        public function findByID(int $id): ?Club {
            $data = $this->findViaCriteria(["ID" => $id]);
            if (empty($data)) throw new InvalidArgumentException("Club with ID {$id} doesn't exist!");
            $row = $data[0];
            return $this->hydrate($row);
        }

        public function findByName(string $n): ?Club {
            $n = strtolower($n);
            $data = $this->findViaCriteria(["name" => $n]);
            if (empty($data)) throw new InvalidArgumentException("No one participates into the club!");

            $row = $data[0];
            return new Club(
                $row["name"],
                $row["description"],
                $row["founded_date"],
                $row["logo_url"],
                $row["status"],
                $row["ID"],
                $row["total_members"]);
        }

        public function save(Club $c): bool {
            if ($c === null) return false;
            return $this->updateViaCriteria([
                "name" => $c->getName(),
                "description" => $c->getDescription(),
                "founded_date" => $c->getFoundedDate(),
                "logo_url" => $c->getLogoURL(),
                "status" => $c->getStatus(),
                "total_members" => $c->getTotalMembers()
            ], ["ID" => $c->getID()]);
        }

        public function hydrate(array $row): Club {
            if (empty($row)) throw new RuntimeException("Empty row!");
            try {
                $fD = new DateTime($row["founded_date"]);
            } catch (PDOException $ex) {
                throw new RuntimeException("Invalid founded date!");
            }
            $club = new Club(
                (string)$row["name"],
                (string)$row["description"],
                new DateTime($row["founded_date"]),
                (string)$row["logo_url"],
                Status::from($row["status"]),
                (int)($row["ID"]),
                (int)($row["total_members"])
            );
            return $club;
        }

        public function findByStatus(Status $s): array {
            $detail = $s->value;
            $all = $this->findViaCriteria(["status" => $detail]);
            return array_map(
                fn ($each) => $this->hydrate($each), $all 
            );
        }

        public function increaseTotalMembers(int $cID): bool {
            $rows = ($this->findViaCriteria(["ID" => $cID]));
            if (!empty($rows)) {
                $curr = (int)$rows[0]["total_members"];

                // echo "<div>Current total members in club ID {$cID}: {$curr}</div>";
                $curr++;
                // echo "<div>Current total members in club ID {$cID} after getting incremented: {$curr}</div>";
                $isSuccess = $this->updateViaCriteria(["total_members" => $curr], ["ID" => $cID]);
                return $isSuccess;
            }
            return false;
        }

        public function decreaseTotalMembers(int $cID): bool {
            $club = $this->findByID($cID);
            if (!$club) {
                return false;
            }

            $curr = $club->getTotalMembers();
            if ($curr > 0) {
                $curr--;
                $isSuccess = $this->updateViaCriteria(
                    [
                        "total_members" => $curr
                    ],
                    [
                        "ID" => (int)$club->getID()
                    ]
                );
                return $isSuccess;
            }
            return false;
        }
    }
?>
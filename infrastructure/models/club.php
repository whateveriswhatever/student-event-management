<?php
    require_once root_dir . "/config/database-config.php";
    
    enum Status: string {
        case ACTIVE = "active";
        case LOW = "low";
        case CLOSED = "closed";
    };

    class Club extends BaseModel {
        private int $ID;
        private string $name;
        private string $description;
        private DateTime $foundedDate;
        private string $logoURL;
        private string $status;

        public function __construct(int $id, string $n, string $d, DateTime $fd, string $url, Status $s) {
            $this->setName($n);
            $this->setDescription($d);
            $this->setFoundedDate($fd);
            $this->setLogoURL($url);
            $this->setStatus($s);
            $this->setID($id);
        }

        private function setID(int $id): void {
            if ($id < 1) throw new InvalidArgumentException("Invalid ID");
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
            $this->status = $s->value;
        }

        public function getName(): string {return $this->name;}
        public function getDescription(): string {return $this->description;}
        public function getFoundedDate(): DateTime {return $this->foundedDate;}
        public function getLogoURL(): string {return $this->logoURL;}
        public function getStatus(): string {return $this->status;}
    }

    class ClubRepository extends BaseRepository {
        public function __construct() {
            parent::__construct("club");
        }

        public function findByStatus(Status $s): array {
            $data = $this->findViaCriteria(["status" => $s->value]);
            return $data;
        }

        public function findByName(string $n): ?Club {
            $n = strtolower($n);
            $data = $this->findViaCriteria(["name" => $n]);
            if (empty($data)) throw new InvalidArgumentException("No one participates into the club!");

            $row = $data[0];
            return new Club(
                $row["ID"],
                $row["name"],
                $row["description"],
                $row["founded_date"],
                $row["logo_url"],
                $row["status"]);
        }
    }
?>
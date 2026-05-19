<?php
    require_once root_dir . "/config/database-config.php";

    /*
        Student entity:
        - Business state 
        - Validation
        - Business logic 
        
        Student repository:
        - Persistence
        - SQL 
        - Database access
    */
    class Student {
        private string $name;
        private string $ID;
        private string $phoneNumber;
        private string $email;
        private int $profileID;

        public function __construct(string $id, string $f, string $l,string $pn, string $e, int $pID ) {
            $this->setName($f, $l);
            $this->setID($id);
            $this->setPhoneNumber($pn);
            $this->setEmail($e);
            $this->setProfileID($pID);
        }

        private function doesContainSpecialChars(string $str): bool {
            // \p{L}: Khớp với bất kỳ chữ cái nào từ bất kỳ ngôn ngữ nào (có dấu hoặc không)
            // \p{N}: Khớp với bất kỳ con số nào
            // \s: Khớp với khoảng trắng (dấu cách, tab, xuống dòng)
            // Modifier /u: Bắt buộc phải có để PHP hiểu chuỗi theo chuẩn UTF-8
            if (preg_match("/[^\p{L}\p{N}\s]/u", $str)) return true;
            return false;
        }

        // Setters / Getters
        private function setName(string $f, string $l): void {
            $normalize = function (string $str): string {
                $chars = str_split($str);
                $chars[0] = strtoupper($chars[0]);
                for ($i = 1; $i < count($chars); $i++) {
                    $chars[$i] = strtolower($chars[$i]);
                }
                $x = implode('', $chars);
                return $x;
            };
            $hasNumber = function (string $str): bool {
                return preg_match("/\d/", $str);
            };
            if (gettype($f) !== "string" || gettype($l) !== "string") throw new InvalidArgumentException("Data type must be string!");
            if ($this->doesContainSpecialChars($f) || $this->doesContainSpecialChars($l)
                || $hasNumber($f) || $hasNumber($l)) throw new InvalidArgumentException("Name can't have any special characters!");
            // dickinson -> Dickinson
            $transformedF = $normalize($f);
            $transformedL = $normalize($l);
            $n = implode(" ", [$transformedF, $transformedL]);
            $this->name = $n;
        }

       

        private function setID(string $id): void {
            $checkLength = function ($x): bool {return strlen($x) === 8 ? true : false;};
            $doesContainChars = function (string $x): bool {
                if (preg_match("/[a-zA-Z]/", $x)) return true;
                return false;
            };
            if (gettype($id) !== "string") throw new InvalidArgumentException("Invalid data type for ID!");
            if (!$checkLength($id) || $doesContainChars($id)) throw new InvalidArgumentException("Length of ID is not sufficicent and it contains special characters!");

            $this->ID = $id;
        }

        private function setPhoneNumber(string $pn): void {
            $doesContainChars = function (string $x): bool {
                if (preg_match("/[a-zA-Z]/", $x)) return true;
                return false;
            };
            if (gettype($pn) !== "string") throw new InvalidArgumentException("Invalid data type!");
            if ($doesContainChars($pn)) throw new InvalidArgumentException("Phone number can't have any special chars or symbols!");
            $x = str_split($pn);
            $y = [];
            foreach ($x as $digit) {
                if ($digit !== ' ') array_push($y, $digit);
            }
            $pn = implode('', $y);
            $this->phoneNumber = $pn;
        }

        private function setEmail(string $e): void {
            $x = explode('@', $e);
            if (count($x) > 2) throw new InvalidArgumentException("Invalid phone number format!");
            $username = $x[0];
            $domain = $x[1];

            $validateUsername = function (string $u): bool {
                if (strlen($u) > 64) return false;
                /*
                    ^ and $ anchor the match to the start and end of the string 
                    (?!.*[_-]{2}) is a negative lookahead that ensures the string doesn't contain two consecutive underscores or hyphens (__ or -- or -_)
                    [a-zA-Z0-9] ensures the string starts with a letter or number 
                    [a-zA-Z0-9_-]* allows zero or more of the allowed characters (letters, numbers, underscores, hyphens) in the middle
                    [a-zA-Z0-9]$ ensures the string ends with a letter or number 
                */
                if (preg_match("^(?!.*[_-]{2})[a-zA-Z0-9][a-zA-Z0-9_-]*[a-zA-Z0-9]$", $u)) return true;
                return false;
            };
            $validateDomain = function (string $d): bool {
                $x = explode('.', $d);
                if (count($x) > 2) return false;
                if (preg_match("/^[a-zA-Z0-9](?!.*--)[a-zA-Z0-9-]*[a-zA-Z0-9]$/", $x[0])) return true;
                return false;
            };
            $lowerize = function (string $s): string {
                $chars = str_split($s);
                for ($i = 0; $i < count($chars); $i++) {
                    $chars[$i] = strtolower($chars[$i]);
                }
                $x = implode('', $chars);
                return $x;
            };
            $username = $lowerize($username);
            $domain = $lowerize($domain);
            if (!$validateUsername($username) || !$validateDomain($domain)) throw new InvalidArgumentException("Invalid username or invalid domain!");
            $this->email = implode('@', [$username, $domain]);
        }

        private function setProfileID(int $id): void {
            if (gettype($id) !== "integer" || $id < 1) throw new InvalidArgumentException("Invalid ID");
            $this->profileID = $id;
        }

        public function getName(): string {return $this->name;}
        public function getPhoneNumber(): string {return $this->phoneNumber;}
        public function getEmail(): string {return $this->email;}
        public function getID(): string {return $this->ID;}
        public function getProfileID(): int {return $this->profileID;}

    }

    class StudentRepository extends BaseRepository {
        public function __construct() {
            parent::__construct("student");
        }

        public function findByID(string $ID): ?Student {
            $data = $this->findViaCriteria(["ID" => $ID]);
            if (empty($data)) return null;

            $row = $data[0];
            return new Student(
                $row["ID"],
                $row["firstname"],
                $row["lastname"],
                $row["email"],
                $row["phone"],
                $row["profile_ID"]
            );
        }

        public function save(Student $s): bool {
            return $this->updateViaCriteria(["email" => $s->getEmail()], ["ID" => $s->getID()]);
        }
    }

    
?>
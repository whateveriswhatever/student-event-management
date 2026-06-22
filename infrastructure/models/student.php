<?php
    require_once root_dir . "/config/database-config.php";
    require_once root_dir . "/models/profile.php";
    require_once root_dir . "/models/club.php";
    require_once root_dir . "/models/membership.php";
    require_once root_dir . "/models/event.php";
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
        private int $age;
        private string $phoneNumber;
        private string $email;
        private int $profileID;
        private string $password;

        public function __construct(string $id, string $f, string $l, int $a, string $pn, string $e, int $pID, string $p ) {
            $this->setName($f, $l);
            $this->setID($id);
            $this->setPhoneNumber($pn);
            $this->setEmail($e);
            $this->setProfileID($pID);
            $this->setAge($a);
            $this->setPassword($p);
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

       private function setAge(int $a): void {
            if ($a < 12) throw new InvalidArgumentException("Invalid value for a college student age!");
            $this->age = $a; 
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
                if (preg_match("/^(?!.*[_-]{2})[a-zA-Z0-9][a-zA-Z0-9_-]*[a-zA-Z0-9]$/", $u)) return true;
                // if (preg_match("/^(?!.*[_-]{2})[a-zA-Z0-9](?:[a-zA-Z0-9_-]*[a-zA-Z0-9])?$/", $u) === true) return true;
                return false;
            };
            $validateDomain = function (string $d): bool {
                $x = explode('.', $d);
                if (preg_match("/^[a-zA-Z0-9](?!.*--)[a-zA-Z0-9-]*[a-zA-Z0-9]$/", $x[0])) return true;
                // if (preg_match(
                //     "/^(?!-)([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}$/", $x[0]
                // ) === true) return true;
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

        private function setPassword(string $p): void {
            /*
            // at least 1 digit number, 1 special character, one uppercase letter, minimum length of 12
            // no blank space
            if (strlen($p) < 12) throw new InvalidArgumentException("Minimum length for the password is 12!");
            $isSpecialChar = function ($c): bool {
                return preg_match("/[^a-zA-Z0-9]/", $c) === 1;
            };
            $init = 0;
            $isDigit = 1;   // 0001 -> 1 
            $isUpper = 2;   // 0010 -> 2
            $isSpecial = 4; // 0100 -> 4

            $satisfied = $isDigit | $isUpper | $isSpecial;  // 0111 = 7
            for ($i = 0; $i < strlen($p); $i++) {
                $curr = $p[$i];
                if (ctype_digit($curr)) $init |= $isDigit;
                if (ctype_upper($curr)) $init |= $isUpper;
                if (ctype_space($curr)) throw new InvalidArgumentException("Password can not contain whitespace!");
                if ($isSpecialChar($curr)) $init |= $isSpecial;
            }
            if ($init === $satisfied) $this->password = $p;
            else throw new InvalidArgumentException("Invalid password! The password must have at least 1 digit number, 1 special character. 1 uppercase letter!");
            */
            // The validation process for password will be conducted at frontend side
            $this->password = $p;
        }

        public function getName(): string {return $this->name;}
        public function getPhoneNumber(): string {return $this->phoneNumber;}
        public function getEmail(): string {return $this->email;}
        public function getID(): string {return $this->ID;}
        public function getProfileID(): int {return $this->profileID;}
        public function getAge(): int {return $this->age;}
        public function getPassword(): string {return $this->password;}
        public function getFirstname(): string {
            $full = $this->getName();
            $parts = explode(' ', $full);
            return $parts[0];
        }
        public function getLastname(): string {
            $full = $this->getName();
            $parts = explode(' ', $full);
            return $parts[1];
        }
    }

    class StudentRepository extends BaseRepository {
        private ClubRepository $clubRepo;
        private MembershipRepository $membershipRepo;
        private EventRegistrationRepository $eventRegisRepo;
        public function __construct() {
            parent::__construct("student");
            $this->clubRepo = new ClubRepository();
            $this->membershipRepo = new MembershipRepository();
            $this->eventRegisRepo = new EventRegistrationRepository();
        }

        public function findByID(string $ID): ?Student {
            $data = $this->findViaCriteria(["ID" => $ID]);
            if (empty($data)) return null;

            $row = $data[0];
            $student = $this->hydrate($row);
            return $student;
        }

        public function save(Student $s): bool {
            return $this->updateViaCriteria(["email" => $s->getEmail()], ["ID" => $s->getID()]);
        }

        public function create(
            string $id, 
            string $f, 
            string $l, 
            int $a, 
            string $pn, 
            string $e, 
            int $pID, 
            string $p): ?Student {
            $student = new Student($id, $f, $l, $a, $pn, $e, $pID, $p);
            // hashing the user password using bcrypt to avoid security fraud
            $hashedPassword = password_hash($p, PASSWORD_BCRYPT);
            $isSuccess = $this->add([
                "ID" => $student->getID(), 
                "firstname" => $student->getFirstname(),
                "lastname" => $student->getLastname(), 
                "age" => $a, 
                "phone_number" => $student->getPhoneNumber(),
                "profile_ID" => $student->getProfileID(),
                "password" => $hashedPassword,
                "email" => $student->getEmail()
            ]);
            if ($isSuccess) {
                return new Student(
                    (string)$student->getID(),
                    (string)$student->getFirstname(),
                    (string)$student->getLastname(),
                    (int)$student->getAge(),
                    (string)$student->getPhoneNumber(),
                    (string)$student->getEmail(),
                    (int)$student->getProfileID(),
                    (string)$hashedPassword
                );
            } else {
                throw new RuntimeException("Failed to create a new student! Re-checking student information to meet up constraints!");
            }
        }

        public function login(string $id, string $password): bool {
            // check if the student has registered before
            $foundOne = $this->findByID($id);
            if ($foundOne === null) throw new RuntimeException("This student with ID {$id} doesn't exist!");
            $storedPassword = $foundOne->getPassword();
            // verifying the password against the hash
            if (password_verify($password, $storedPassword)) {
                return true;
            } else {
                return false;
            } 
        }

        #[Override]
        public function hydrate(array $row): object
        {
            return new Student(
                (string)$row["ID"],
                (string)$row["firstname"],
                (string)$row["lastname"],
                (int)$row["age"],
                (string)$row["phone_number"],
                (string)$row["email"],
                (int)$row["profile_ID"],
                (string)$row["password"]
            );
        }

        public function getAllJoinedClubs(int $sID): array {
            // Get all memberships
            $clubIds = ($this->membershipRepo)->getAllJoinedClubIDsViaStudentID($sID);
            $clubs = [];
            for ($i = 0; $i < count($clubIds); $i++) {
                $clubs[] = ($this->clubRepo)->findByID($clubIds[$i]);
            }

            return $clubs;
        }

        public function getAllJoinedEvents(int $sID): array {
            // Get all joined events
            $events = ($this->eventRegisRepo)->findAllEventsFromAStudent($sID);
            return $events;
        }

        public function findByName(string $n, string $providerID): array {
            $n = trim($n);
            $process = function (string $input): array {
                $input = trim($input);
                $storage = preg_split("/\s+/", $input, -1, PREG_SPLIT_NO_EMPTY);
                for ($i = 0; $i < count($storage); $i++) {
                    $curr = $storage[$i];
                    $curr = strtolower($curr);
                    $curr[0] = strtoupper($curr[0]);
                    $storage[$i] = $curr;
                }
                return $storage;
            };
            
            $storage = $process($n);
            $tokens = [
                "firstname" => $storage[0],
                "lastname"  => []
            ];
            for ($i = 1; $i < count($storage); $i++) {
                $tokens["lastname"][] = $storage[$i];
            }
            $tokens["lastname"] = implode(" ", $tokens["lastname"]);
            // echo "<div>{$tokens['lastname']}</div>";
            $stmt = ($this->dbConnection)->prepare("
                select
                    *
                from {$this->tableName}
                where (
                    firstname like :f1
                    or lastname like :f2
                ) and (
                    firstname like :l1
                    or lastname like :l2
                ) and
                ID != :id
            ");
            $stmt->execute([
                ":f1"   => '%' . $tokens["firstname"] . '%',
                ":f2"   => '%' . $tokens["firstname"] . '%',
                ":l1"   => '%' . $tokens["lastname"] . '%',
                ":l2"   => '%' . $tokens["lastname"] . '%',
                ":id"   => $providerID
            ]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($data)) {
                return array_map(
                    fn ($row) => $this->hydrate($row), $data
                );
            } else {
                return [];
            }
        }
    }

    
?>
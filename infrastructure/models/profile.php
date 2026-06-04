<?php
    require_once root_dir . "/config/database-config.php";
    require_once root_dir . "/models/student.php";
    require_once root_dir . "/models/base.php";

    enum DegreeType: string {
        case UNDER = "undergraduate";
        case POST = "postgraduate";
    };


    class Profile extends BaseModel {
        private string $ID;
        private string $studentID;
        private string $major;
        private string $class;
        private DegreeType $degree;
        private int $enrolledYear;

        private array $availableMajors = [
        // Major -> Class code
        "informatics and computer engineering" => "ICE",
        "business and data analysis" => "BDA",
        "management information system" => "MIS",
        "accounting, analyzing and auditing" => "AC",
        "automation and informatics" => "AAI",
        "english language" => "EL",
        "digital business" => "DB",
        "digital communication" => "DC"
        ];

        public function __construct(string $sID, string $m, DegreeType $dtype, ?int $ID = null) {
            $this->setStudentID($sID);
            $this->setMajor($m);
            $this->setClass($this->getMajor());
            $this->setDegree($dtype);
            $this->setProfileID($ID);
        }

        private function setProfileID(?int $pID = null): void {
            // if ($pID < 1) throw new InvalidArgumentException("ID can not be lower than 1!");
            if ($pID !== null) {
                $this->ID = $pID;
            }
        }

        private function setStudentID(string $sID): void {
            $checkLength = function ($x): bool {return strlen($x) === 8 ? true : false;};
            $doesContainChars = function (string $x): bool {
                if (preg_match("/[a-zA-Z]/", $x)) return true;
                return false;
            };
            if (gettype($sID) !== "string") throw new InvalidArgumentException("Invalid data type for student ID!");
            if (!$checkLength($sID) || $doesContainChars($sID)) throw new InvalidArgumentException("Student ID contains speical characters or not sufficient length!");
            $this->studentID = $sID;
        }

        private function setMajor(string $m): void {
            if ($this->doesContainSpecialChars($m)) throw new InvalidArgumentException("Major title contains special characters!");
            $m = strtolower($m);
            $isExisted = array_key_exists($m, $this->availableMajors);
            if ($isExisted) {
                $this->major = $m;
            } else {
                throw new InvalidArgumentException("Major {$m} doesn't exist!");
            }
        }

        private function setClass(string $m): void {
            // $enrolledYear = $this->getEnrolledYear();
            $major = $this->getMajor();
            $classCode = $this->availableMajors[$major];
            // ICE2022A
            $c = $classCode . "";
            $this->class = $c;
        }

        private function setDegree(DegreeType $d): void {
            $this->degree = $d;
        }

        private function setEnrolledYear(int $y): void {
            if ($y < 1980) throw new InvalidArgumentException("Invalid enrolled year!");
            $this->enrolledYear = $y;
        }

        public function getMajor(): string {return $this->major;}
        public function getProfileID(): int {return $this->ID;}
        public function getStudentID(): string {return $this->studentID;}
        public function getDegree(): DegreeType {return $this->degree;}
        public function getClass(): string {return $this->class;}
        // public function getEnrolledYear(): int {return $this->enrolledYear;}
    }


    class ProfileRepository extends BaseRepository {
        public function __construct() {
            parent::__construct("profile");
        }

        public function findByID(int $id): ?Profile {
            $data = $this->findViaCriteria(["ID" => $id]);
            if (empty($data)) return null;

            $row = $data[0];
            return new Profile(
                    $row["student_ID"],
                    $row["major"],
                    DegreeType::from($row["degree"]) ?? DegreeType::UNDER,
                    $row["ID"]
            );
        }

        private function isIDExist(int $id): bool {
            $data = $this->findViaCriteria(["ID" => $id]);
            if (empty($data)) return false;
            return true;
        }

        public function findByStudentID(string $sID): ?Profile {
            $data = $this->findViaCriteria(["student_ID" => $sID]);
            if (empty($data)) return null;

            $row = $data[0];
            return $this->hydrate($row);
        }

        public function linkToStudentViaID(string $sID, string $pID): bool {
            /* A student is allowed to have only one profile linked to them */
            // Check if current student has any profile linked to him yet
            $studentRepo = new StudentRepository();
            $currStudent = $studentRepo->findByID($sID);
            if ($currStudent !== null) {
                $currProfileID = $currStudent->getProfileID();
                if ($currProfileID !== null) return false;   
            }
            if ($this->isIDExist($pID)) return false;
            $isSuccess = $studentRepo->updateViaCriteria(["profile_ID" => $pID], ["ID" => $sID]);
            return $isSuccess;
            
        }

        public function create(string $sID, string $m, ?DegreeType $d = null): ?Profile {
            if ($d === null) {
                $d = DegreeType::UNDER;
            }
            $newProfile = new Profile($sID, $m, $d);
            $isSuccess = $this->add(
                [
                    "student_ID" => $sID,
                    "major" => $m, 
                    "degree" => $d->value,
                    "class" => $newProfile->getClass()
                ]
            );
            if ($isSuccess) {
                $generatedID = $this->getLatestID();
                return new Profile(
                    $newProfile->getStudentID(), 
                    $newProfile->getMajor(), 
                    $newProfile->getDegree(),
                    $generatedID
                );
            } else {
                throw new RuntimeException("Failed to create new student profile!");
                
            }
        }

        #[Override]
        public function hydrate(array $row): Profile
        {
            return new Profile(
                (string)$row["student_ID"],
                (string)$row["major"],
                DegreeType::from((string)$row["degree"])
            );
        }
    }

    
?>
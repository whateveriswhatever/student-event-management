<?php
    require_once root_dir . "/config/database-config.php";
    require_once root_dir . "/models/student.php";

    enum DegreeType: string {
        case UNDER = "undergraduate";
        case POST = "postgraduate";
    };


    class Profile extends BaseModel {
        private string $ID;
        private string $studentID;
        private string $major;
        private string $class;
        private string $degree;
        private int $enrolledYear;

        private array $availableMajors = [
        // Major -> Class code
        "mathematics" => "MAT",
        "physics" => "PHY",
        "electrical engineering" => "EEG",
        "economics" => "ECO",
        "audit & accountance" => "AAE",
        "computer science" => "CSE",
        "computer engineering" => "CEG",
        "marketing" => "MAR",
        "mechanical engineering" => "MEG",
        "civial engineering" => "CEG",
        "finance" => "FIN",
        "business ananysis" => "BAS"
        ];

        public function __construct(string $sID, string $m, DegreeType $dtype) {
            $this->setStudentID($sID);
            $this->setMajor($m);
            $this->setClass($this->getMajor());
            $this->setDegree($dtype);
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
            if (!isset($availableMajors[$m])) throw new InvalidArgumentException("Major doesn't exist!");
            $this->major = $m;
        }

        private function setClass(string $m): void {
            $enrolledYear = $this->getEnrolledYear();
            $major = $this->getMajor();
            $classCode = $this->availableMajors[$major];
            // ICE2022A
            $c = $classCode + (string)$enrolledYear;
            $this->class = $c;
        }

        private function setDegree(DegreeType $d): void {
            $this->degree = $d->value;
        }

        private function setEnrolledYear(int $y): void {
            if ($y < 1980) throw new InvalidArgumentException("Invalid enrolled year!");
            $this->enrolledYear = $y;
        }

        public function getMajor(): string {return $this->major;}
        public function getProfileID(): int {return $this->ID;}
        public function getStudentID(): string {return $this->studentID;}
        public function getDegree(): string {return $this->degree;}
        public function getEnrolledYear(): int {return $this->enrolledYear;}
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
                    $row["degree"]);
        }

        private function isIDExist(int $id): bool {
            $data = $this->findViaCriteria(["ID" => $id]);
            if (empty($data)) return false;
            return true;
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

        public function create(string $sID, string $m, DegreeType $d): ?Profile {
            $newProfile = new Profile($sID, $m, $d);
            $isSuccess = $this->add(
                [
                    "student_ID" => $sID,
                    "major" => $m, 
                    "degree" => $d->value
                ]
            );
            if ($isSuccess) {
                return $newProfile;
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
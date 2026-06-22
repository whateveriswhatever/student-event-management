<?php
    require_once root_dir . "/models/student.php";
    require_once root_dir . "/app/controllers/BaseController.php";
    require_once root_dir . "/models/profile.php";
    require_once root_dir . "/models/friendship.php";


    class StudentController extends BaseController {
        private StudentRepository $studentRepo;
        private ProfileRepository $profileRepo;
        private string $baseFolderPath;
        private FriendshipRepository $friendshipRepo;

        public function __construct() {
            $this->studentRepo = new StudentRepository();
            $this->profileRepo = new ProfileRepository();
            $this->baseFolderPath = base_folder_path;
            $this->friendshipRepo = new FriendshipRepository();
        }

        public function index(): void {
            try {
                $studentID = $_SESSION["user_ID"];
                $student = ($this->studentRepo)->findByID($studentID);
                $profile = ($this->profileRepo)->findByStudentID($studentID);
            } catch (Exception $ex) {

            }
        }

        public function register(): void {
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                try {
                    // student information
                    $id = trim($_POST["ID"]);
                    $firstname = trim($_POST["firstname"]);
                    $lastname = trim($_POST["lastname"]);
                    $age = (int)$_POST["age"];
                    $phoneNumber = trim($_POST["phoneNumber"]);
                    $email = trim($_POST["email"]);
                    $password = trim($_POST["password"]);
                    
                    // profile data
                    $major = $_POST["major"];
                    // $degree = $_POST["degree"];
                    $profileID = null;

                    $profile = ($this->profileRepo)->create($id, $major);
                    if ($profile) {
                        $profileID = $profile->getProfileID();
                    } else {
                        $profileID = null;
                    }
                    
                    $student = ($this->studentRepo)->create($id, $firstname, $lastname, $age, $phoneNumber, $email, $profileID, $password);
                    if ($student) {
                        // Redirecting back to main page
                        header("Location: {$this->baseFolderPath}/login");
                        exit;
                    }
                    $this->render("auth/login_register", ["error" => "Couldn't register a new student!"]);
                } catch (Exception $ex) {
                    $this->render("auth/login_register", ["error" => $ex->getMessage()]);
                }
            }
        }

        public function login(): void {
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                try {
                    $studentID = trim($_POST["studentID"]);
                    $password = trim($_POST["inputPassword"]);

                    $student = ($this->studentRepo)->findByID($studentID);
                    if ($student !== null) {
                        $hashedPassword = $student->getPassword();
                        if (password_verify($password, $hashedPassword) === true) {
                            // Successful log-in: saving user details to the session
                            $_SESSION["user_ID"] = $student->getID();
                            $_SESSION["userLastname"] = $student->getLastname();
                            // $profile = ($this->profileRepo)->findByID((int)$student->getID());
                            // $this->render("clubs/index", ["data" => [
                            //     "student" => $student,
                            //     "profile" => $profile
                            // ]]);
                            header("Location: {$this->baseFolderPath}");
                            exit;
                        } else {
                            $this->render("auth/login_register", ["error" => "Incorrect password!"]);
                        }   
                    } else {
                        $this->render("auth/login_register", ["error" => "Student with ID {$studentID} doesn't exist!"]);
                    }
                } catch (Exception $ex) {
                    $this->render("auth/login_register", ["error" => $ex->getMessage()]);
                }
            }
        }

        public function showAuthPage(): void {
            $this->render("auth/login_register");
        }

        public function signout(): void {
            // Clearing all session variables
            session_unset();
            // Destroying the session
            session_destroy();

            // Redirecting back to the login page
            header("Location: {$this->baseFolderPath}/login");
            exit;
        }

        public function showProfile(): void {
            // Securing the page: redirecting users to login page if the user session isn't active
            if (!isset($_SESSION["user_ID"])) {
                header("Location: {$this->baseFolderPath}/login");
                exit;
            }

            try {
                $studentID = $_SESSION["user_ID"];
                $student = ($this->studentRepo)->findByID($studentID);
                $profile = ($this->profileRepo)->findByStudentID($studentID);
                // if ($student === null) {
                //     throw new Exception("Student data couldn't be retrieved!");
                // } else {
                //     echo "<div>Found student with ID: {$studentID}!</div>";
                // }
                
                /* Get joined clubs and events */
                // Only clubs that students were allowed or able to join
                $joinedClubs = ($this->studentRepo)->getAllJoinedClubs($studentID);
                $joinedEvents = ($this->studentRepo)->getAllJoinedEvents($studentID);

                $calendarEvents = [];
                foreach ($joinedEvents as $event) {
                    $dateStr = $event->getEventDate()->format("Y-m-d\TH:i:s");
                    $startStr = $event->getStartTime()->format("Y-m-d\TH:i:s");
                    $endStr = $event->getEndTime()->format("H:i:s");

                    $calendarEvents[] = [
                        "title"             => $event->getTitle(),
                        "start"             => $startStr,
                        "end"               => $endStr,
                        "url"               => $this->baseFolderPath . "/clubs/show?id=" . $event->getClubID(),
                        "backgroundColor"   => "#3b82f6",
                        "borderColor"       => "#2563eb"
                    ];
                }

                // Converting array into JSON string so JavaScript can understand and process
                $calendarEventsJSON = json_encode($calendarEvents);

                // Fetching accepted friend list
                $friendsList = ($this->friendshipRepo)->getAllFriendsFromUserID($studentID);
                $friendsData = [];
                foreach ($friendsList as $f) {
                    if ($f->getFromID() !== $studentID) $friendsData[] = ($this->studentRepo)->findByID($f->getFromID());
                    if ($f->getToID() !== $studentID) $friendsData[] = ($this->studentRepo)->findByID($f->getToID());
                }
                
                $this->render("profile/index", [
                    "student"       => $student,
                    "profile"       => $profile,
                    "joinedClubs"   => $joinedClubs,
                    "joinedEvents"  => $joinedEvents,
                    "calendarJSON"  => $calendarEvents,
                    "friends"       => $friendsData
                ]);


            } catch (Exception $ex) {
                $this->render("clubs/index", ["error" => $ex->getMessage(), "description" => "Failed to load the profile page!"]);
            }
        }
    }
?>
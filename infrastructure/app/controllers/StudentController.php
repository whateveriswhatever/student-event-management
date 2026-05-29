<?php
    require_once root_dir . "/models/student.php";
    require_once root_dir . "/app/controllers/BaseController.php";
    require_once root_dir . "/models/profile.php";

    class StudentController extends BaseController {
        private StudentRepository $studentRepo;
        private ProfileRepository $profileRepo;

        public function __construct() {
            $this->studentRepo = new StudentRepository();
            $this->profileRepo = new ProfileRepository();
        }

        public function index(): void {
            $rawStudents = ($this->studentRepo)->all();
            $this->render("students/index", ["students" => $rawStudents]);
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
                        header("Location: /final-project/infrastructure/login");
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
                            header("Location: /final-project/infrastructure/");
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
            header("Location: /final-project/infrastructure/login");
            exit;
        }
    }
?>
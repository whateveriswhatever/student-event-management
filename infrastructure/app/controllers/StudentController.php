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
            $this->requireAuth();
            $rawStudents = $this->studentRepo->all();
            $this->render("students/index", ["students" => $rawStudents]);
        }

        /** POST /auth/signup — Đăng ký tài khoản mới */
        public function register(): void {
            if ($_SERVER["REQUEST_METHOD"] !== "POST") {
                $this->render("auth/login_register");
                return;
            }
            try {
                $id          = $this->post("ID");
                $firstname   = $this->post("firstname");
                $lastname    = $this->post("lastname");
                $age         = $this->postInt("age");
                $phoneNumber = $this->post("phoneNumber");
                $email       = $this->post("email");
                $password    = $this->post("password");
                $major       = $this->post("major");

                // 1. Tạo profile trước
                $profile   = $this->profileRepo->create($id, $major);
                $profileID = $profile ? $profile->getProfileID() : null;

                // 2. Tạo student với profileID vừa tạo
                $student = $this->studentRepo->create(
                    $id, $firstname, $lastname, $age,
                    $phoneNumber, $email, $profileID, $password
                );

                if ($student) {
                    $this->redirect("/final-project/infrastructure/login");
                }
                $this->render("auth/login_register", ["error" => "Không thể tạo tài khoản!"]);
            } catch (Exception $ex) {
                $this->render("auth/login_register", ["error" => $ex->getMessage()]);
            }
        }

        /** POST /auth/login — Đăng nhập */
        public function login(): void {
            if ($_SERVER["REQUEST_METHOD"] !== "POST") {
                $this->render("auth/login_register");
                return;
            }
            try {
                $studentID = $this->post("studentID");
                $password  = $this->post("inputPassword");

                $student = $this->studentRepo->findByID($studentID);
                if ($student === null) {
                    $this->render("auth/login_register", [
                        "error" => "Sinh viên với mã {$studentID} không tồn tại!"
                    ]);
                    return;
                }

                if (!password_verify($password, $student->getPassword())) {
                    $this->render("auth/login_register", ["error" => "Mật khẩu không đúng!"]);
                    return;
                }

                // Đăng nhập thành công:
                // Regenerate session ID để chống session fixation attack
                session_regenerate_id(true);

                $_SESSION["user_ID"]      = $student->getID();
                $_SESSION["userLastname"] = $student->getLastname();
                $_SESSION["userFullName"] = $student->getName();

                $this->redirect("/final-project/infrastructure/");
            } catch (Exception $ex) {
                $this->render("auth/login_register", ["error" => $ex->getMessage()]);
            }
        }

        /** GET /login — Hiển thị trang auth */
        public function showAuthPage(): void {
            // Nếu đã đăng nhập thì chuyển thẳng về trang chủ
            if ($this->isAuthenticated()) {
                $this->redirect("/final-project/infrastructure/");
            }
            $this->render("auth/login_register");
        }

        /** GET /signout — Đăng xuất */
        public function signout(): void {
            // Xóa toàn bộ session data
            $_SESSION = [];
            // Xóa session cookie nếu có
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(), "", time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            session_destroy();
            $this->redirect("/final-project/infrastructure/login");
        }
    }
?>
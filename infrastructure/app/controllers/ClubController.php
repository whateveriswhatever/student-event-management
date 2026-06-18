<?php
    require_once root_dir . "/app/controllers/BaseController.php";
    require_once root_dir . "/models/club.php";

    class ClubController extends BaseController {
        private ClubRepository $clubRepo;

        public function __construct() {
            $this->clubRepo = new ClubRepository();
        }

        /** GET /clubs — Danh sách tất cả câu lạc bộ */
        public function index(): void {
            $rawClubs = $this->clubRepo->all();
            $this->render("clubs/index", ["clubs" => $rawClubs]);
        }

        /** GET /clubs/create — Hiển thị form tạo câu lạc bộ mới */
        public function showCreateForm(): void {
            $this->requireAuth();
            $this->render("clubs/create");
        }

        /** POST /clubs/create — Lưu câu lạc bộ mới */
        public function store(): void {
            $this->requireAuth();
            if ($_SERVER["REQUEST_METHOD"] !== "POST") {
                $this->jsonError("Method not allowed", 405);
                return;
            }
            try {
                $name        = $this->post("name");
                $description = $this->post("description");
                $logoURL     = $this->post("logo_url");
                $status      = Status::tryFrom($this->post("status", "active"));
                if (!$status) throw new Exception("Trạng thái câu lạc bộ không hợp lệ");
                $foundedDate = new DateTime($this->post("founded_date", "now"));

                $newClub = $this->clubRepo->create($name, $description, $foundedDate, $logoURL, $status);
                if ($newClub) {
                    $this->redirect(BASE_URL . "/clubs?success=1");
                }
                $this->render("clubs/create", ["error" => "Không thể lưu thông tin câu lạc bộ!"]);
            } catch (Exception $ex) {
                $this->render("clubs/create", ["error" => $ex->getMessage()]);
            }
        }

        /** GET /clubs/view?id=X — Chi tiết một câu lạc bộ */
        public function view(): void {
            $clubID = $this->queryInt("id");
            if ($clubID < 1) {
                $this->render("errors/400", ["message" => "ID câu lạc bộ không hợp lệ!"]);
                return;
            }
            $club = $this->clubRepo->findByID($clubID);
            $this->render("clubs/view", ["club" => $club]);
        }
    }
?>
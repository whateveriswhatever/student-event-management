<?php
    require_once root_dir . "/app/controllers/BaseController.php";
    require_once root_dir . "/models/announcement.php";

    class AnnouncementController extends BaseController {
        private AnnouncementRepository $announcementRepo;

        public function __construct() {
            $this->announcementRepo = new AnnouncementRepository();
        }

        /** GET /announcements?club_ID=X — Danh sách thông báo của câu lạc bộ */
        public function index(): void {
            $clubID = $this->queryInt("club_ID");
            if ($clubID < 1) {
                $this->jsonError("Thiếu hoặc sai club ID!", 400);
                return;
            }
            $announcements = $this->announcementRepo->findAllFromClubViaID($clubID);
            $this->render("announcements/index", [
                "announcements" => $announcements,
                "clubID"        => $clubID
            ]);
        }

        /** POST /announcements/create — Đăng thông báo mới */
        public function store(): void {
            $this->requireAuth();
            if ($_SERVER["REQUEST_METHOD"] !== "POST") {
                $this->redirect("/");
                return;
            }

            $clubID      = $this->postInt("club_ID");
            // Lấy authorID từ session — không tin tưởng POST data
            $authorID    = (int)$this->getCurrentUserID();
            $title       = $this->post("title");
            $description = $this->post("description");

            if ($clubID < 1 || $authorID < 1) {
                $this->jsonError("Thông tin không hợp lệ!", 400);
                return;
            }

            try {
                $announcement = new Announcement(null, $authorID, $clubID, $title, $description);
                $payload = [
                    "club_ID"     => $clubID,
                    "author_ID"   => $authorID,
                    "title"       => $title,
                    "description" => $description
                ];
                if ($this->announcementRepo->add($payload)) {
                    $this->redirect(BASE_URL . "/announcements?club_ID={$clubID}&success=1");
                }
            } catch (Exception $e) {
                $this->render("announcements/create", [
                    "error"  => $e->getMessage(),
                    "clubID" => $clubID
                ]);
            }
        }
    }
?>
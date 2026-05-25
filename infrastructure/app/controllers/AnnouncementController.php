<?php
    require_once root_dir . "/app/controllers/BaseController.php";
    require_once root_dir . "/models/announcement.php";

    class AnnouncementController extends BaseController {
        private AnnouncementRepository $announcementRepo;

        public function __construct() {
            $this->announcementRepo = new AnnouncementRepository();
        }

        public function index(): void {
            $clubID = (int)($_GET["club_ID"] ?? 0);
            if ($clubID < 1) {
                throw new InvalidArgumentException("Missing or invalid club ID!");
            }

            $announements = ($this->announcementRepo)->findAllFromClubViaID($clubID);
            $this->render("announcements/index", [
                "announcements" => $announements,
                "clubID" => $clubID
            ]);
        }

        public function store(): void {
            if ($_SERVER["REQUEST_METHOD"] !== "POST") {
                header("Location: /");
                exit;
            }

            $clubID = (int)($_POST["club_ID"] ?? 0);
            $authorID = (int)($_POST["author_ID"] ?? 0);
            $title = trim($_POST["title"] ?? "");
            $description = trim($_POST["description"] ?? "");

            try {
                $announcement = new Announcement(null, $authorID, $clubID, $title, $description);

                $payload = [
                    "club_ID" => $clubID,
                    "author_ID" => $authorID,
                    "title" => $title,
                    "description" => $description
                ];

                if (($this->announcementRepo)->add($payload)) {
                    header("Location: /announcements?club_ID={$clubID}&success=1");
                    exit;
                }
            } catch (Exception $e) {
                $this->render("announcements/create", ["error" => $e->getMessage(), "clubID" => $clubID]);
            }
        }
    }
?>
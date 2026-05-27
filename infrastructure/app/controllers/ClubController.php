<?php
    require_once root_dir . "/app/controllers/BaseController.php";
    require_once root_dir . "/models/club.php";

    class ClubController extends BaseController {
        private ClubRepository $clubRepo;

        public function __construct() {
            $this->clubRepo = new ClubRepository();
        }

        public function index(): void {
            $rawClubs = ($this->clubRepo)->all();

            $this->render("clubs/index", ["clubs" => $rawClubs]);
        }

        public function store(): void {
            if ($_SERVER["REQUEST_METHOD"] !== "POST") {
                $this->json(["error" => "Method not allowed"], 405);
                return;
            }

            try {
                $name = trim($_POST["name"] ?? '');
                $description = trim($_POST["description"] ?? '');
                $logoURL = trim($_POST["logo_url"] ?? '');
                $status = Status::from($_POST["status"] ?? "active");
                $foundedDate = new DateTime($_POST["founded_date"] ?? "now");


                $newClub = ($this->clubRepo)->create($name, $description, $foundedDate, $logoURL, $status);
                if ($newClub) {
                    // Redirect back to main page or return success
                    header("Location: /clubs?success=1");
                    exit;
                }
                $this->render("clubs/create", ["error" => "Couldn't save the club data!"]);
            } catch (Exception $ex) {
                $this->render("clubs/create", ["error" => $ex->getMessage()]);
            }
        }

        public function view(): void {
            $clubID = (int)($_GET["id"] ?? 0);
            $club = ($this->clubRepo)->findByID($clubID);
            $this->render("clubs/view", ["club" => $club]);
        }

    }
?>
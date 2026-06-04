<?php
    require_once root_dir . "/app/controllers/BaseController.php";
    require_once root_dir . "/models/club.php";
    require_once root_dir . "/models/membership.php";
    require_once root_dir . "/models/role.php";

    class ClubController extends BaseController {
        private ClubRepository $clubRepo;
        private MembershipRepository $membershipRepo;
        private RoleRepository $roleRepo;

        public function __construct() {
            $this->clubRepo = new ClubRepository();
            $this->membershipRepo = new MembershipRepository();
            $this->roleRepo = new RoleRepository();
        }

        public function index(): void {
            $rawClubs = ($this->clubRepo)->all();
            $hydatedClubs = [];
            for ($i = 0; $i < count($rawClubs); $i++) {
                $hydatedClubs[] = ($this->clubRepo)->hydrate($rawClubs[$i]);
            }
            
            // Fetching user membership
            $userMemberships = [];
            if (isset($_SESSION["user_ID"])) {
                $studentID = (string)$_SESSION["user_ID"];

                // Fetching all membership rows for this student
                $memberships = ($this->membershipRepo)->findViaCriteria(["student_ID" => $studentID]);

                if (!empty($memberships)) {
                    foreach ($memberships as $m) {
                        $userMemberships[(int)$m["club_ID"]] = $m["membership_status"];
                    }
                }
            }

            $this->render("clubs/index", ["clubs" => $hydatedClubs,
                                            "userMemberships" => $userMemberships]);
        }

        public function store(): void {
            if ($_SERVER["REQUEST_METHOD"] !== "POST") {
                $this->json(["error" => "Method not allowed"], 405);
                return;
            }

            if (!isset($_SESSION["user_ID"])) {
                $this->redirect("/final-project/infrastructure/login");
                return;
            }

            try {
                $studentID = (string)$_SESSION["user_ID"];
                $name = trim($_POST["name"] ?? '');
                $description = trim($_POST["description"] ?? '');
                $status = Status::from($_POST["status"] ?? "active");
                $foundedDate = new DateTime($_POST["founded_date"] ?? "now");

                
                $logoURL = null;
                if (isset($_FILES["logo_image"]) && $_FILES["logo_image"]["error"] === UPLOAD_ERR_OK) {
                    $fileExtension = strtolower(pathinfo($_FILES["logo_image"]["name"], PATHINFO_EXTENSION));
                    $newFileName = uniqid("club_", true) . "." . $fileExtension;
                    $destinationPath = root_dir . "/public/assets/images/clubs/" . $newFileName;

                    if (move_uploaded_file($_FILES["logo_image"]["tmp_name"], $destinationPath)) {
                        $logoURL = base_folder . "/public/assets/images/clubs/" . $newFileName;
                        echo "<div>Logo URL: {$logoURL}</div>";
                    } else {
                        throw new Exception("Failed to upload the club logo!");
                    }
                }
                
                // echo "<div>Logo URL: {$logoURL}</div>";

                $newClub = ($this->clubRepo)->create($name, $description, $foundedDate, $logoURL, $status);
                if ($newClub) {
                    // Redirect back to main page or return success
                    $clubID = $newClub->getID();
                    $adminRole = ($this->roleRepo)->create(RoleTitle::PRESIDENT, RolePermission::MANAGER);
                    if ($adminRole === null) {
                        throw new Exception("Failed to assign the administrator role!");
                    } 
                    $roleID = $adminRole->getID();
                    $joinRequest = ($this->membershipRepo)->createJoinRequest($studentID, $clubID, $roleID);
                    if ($joinRequest) {
                        $requestID = $joinRequest->getID(); echo "<div>Membership ID for the request: {$requestID}</div>";
                        $isSuccess = ($this->membershipRepo)->approveMembership($requestID);
                        if ($isSuccess) {
                            ($this->clubRepo)->increaseTotalMembers($clubID);
                            $this->redirect("/final-project/infrastructure/clubs?success=created");
                        } else {
                            throw new Exception("Club was created but failed to join in!");
                        }    
                    } else {
                        throw new Exception("Failed to establish a joining request!");
                    }
                } else {
                    throw new Exception("Failed to create a new club!");
                }
                
            } catch (Exception $ex) {
                $this->render("admin/create_club", ["error" => $ex->getMessage()]);
            }
        }

        

        public function view(): void {
            $clubID = (int)($_GET["id"] ?? 0);
            $club = ($this->clubRepo)->findByID($clubID);
            $this->render("clubs/view", ["club" => $club]);
        }

        public function showCreateForm(): void {
            $this->render("admin/create_club");
        }

        public function register(): void {
            /* By default, a student registers into an club will take a role as member.
                They can be promoted later via admin, manager.
             */
            if ($_SERVER["REQUEST_METHOD"] !== "POST") {
                $this->json(["error" => "Method is not allowed"], 405);
                return; 
            }

            // Authentication check: ensuring user has logged in
            if (!isset($_SESSION["user_ID"])) {
                $this->redirect("/final-project/infrastructure/login");
            }
            try {
                $clubID = (int)($_POST["club_ID"] ?? 0);
                $studentID = (string)($_SESSION["user_ID"]);

                if ($clubID <= 0) {
                    throw new Exception("Invalid club selected!");
                }

                $newRole = ($this->roleRepo)->create(RoleTitle::MEMBER, RolePermission::REGULAR);
                if ($newRole !== null) {
                    $roleID = $newRole->getID();
                    $membership = ($this->membershipRepo)->createJoinRequest($studentID, $clubID, $roleID);

                    if ($membership !== null) {
                        // Get the membership status
                        if ($membership->getStatus()->value === "approval") {
                            $isSuccess = ($this->clubRepo)->increaseTotalMembers($clubID);
                            if ($isSuccess) {
                                // $this->redirect("/final-project/infrastructure/clubs?success=registered");
                                echo "<div>Updated successfully!</div>";
                            } else {
                                throw new Exception("Failed to increase the current members of the club!");
                            }
                        } else {
                            $this->redirect("/final-project/infrastructure/");
                        }
                    } else {
                        throw new Exception("Failed to establish a membership record!");
                    }
                } else {
                    throw new Exception("Failed to formulate a registration system role.");
                }
            } catch (Exception $ex) {
                // If anything fails, hydrate the clubs listing array so the view doesn't render blank
                $rawClubs = ($this->clubRepo)->all();
                $hydratedClubs = [];    
                for ($i = 0; $i < count($rawClubs); $i++) {
                    $hydratedClubs[] = ($this->clubRepo)->hydrate($rawClubs[$i]);
                }
                $this->render("clubs/index", [
                    "clubs" => $hydratedClubs,
                    "error" => $ex->getMessage()
                ]);

            }
            
        }

    }
?>
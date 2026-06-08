<?php
    require_once root_dir . "/app/controllers/BaseController.php";
    require_once root_dir . "/models/club.php";
    require_once root_dir . "/models/membership.php";
    require_once root_dir . "/models/role.php";
    require_once root_dir . "/models/event.php";
    require_once root_dir . "/models/student.php";
    require_once root_dir . "/models/location.php";

    class ClubController extends BaseController {
        private ClubRepository $clubRepo;
        private MembershipRepository $membershipRepo;
        private RoleRepository $roleRepo;
        private LocationRepository $locationRepo;
        private EventRegistrationRepository $eventRegisterRepo;

        public function __construct() {
            $this->clubRepo = new ClubRepository();
            $this->membershipRepo = new MembershipRepository();
            $this->roleRepo = new RoleRepository();
            $this->locationRepo = new LocationRepository();
            $this->eventRegisterRepo = new EventRegistrationRepository();
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
                        $logoURL = base_folder_path . "/public/assets/images/clubs/" . $newFileName;
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

        public function show(): void {
            // Validating incoming Club ID
            if (!isset($_GET["id"])) {
                $this->redirect(base_folder_path . "/clubs");
                return;
            }
            $eventAddressMapper = [];
            $clubID = (int)$_GET["id"];
            $clubData = ($this->clubRepo)->findByID($clubID);
            $currentUserRole = null;
            if (!$clubData) {
                $this->render("clubs/index", ["error" => "The requested club doesn't exist!"]);
                return;
            }

            $club = is_array($clubData) ? ($this->clubRepo)->hydrate($clubData) : $clubData;
            $isMember = false;
            $events = [];

            // Checking if user is logged in and holds an approved membership
            if (isset($_SESSION["user_ID"])) {
                $studentID = (string)$_SESSION["user_ID"];
                $membership = ($this->membershipRepo)->findViaCriteria([
                    "student_ID"        => $studentID,
                    "club_ID"           => $clubID,
                    "membership_status" => "active"
                ]);
                if (!empty($membership)) {
                    $isMember = true;
                    $userRoleID = (int)($membership[0])["role_ID"];
                    $roleObj = ($this->roleRepo)->findByID($userRoleID);

                    if ($roleObj) {
                        $currentUserRole = strtolower(($roleObj->getTitle())->value);
                    }

                    $eventRepo = new EventRepository();
                    $rawEvents = $eventRepo->findViaCriteria(["club_ID" => $clubID]);

                    /* Logic to automatically update the status of each event */
                    $now = new DateTime();

                    /* Event filtering via at a specific timeline */
                    // Checking if the users are loading the page for the first time
                    $isDefaultLoad = !isset($_GET["start_date"]) && !isset($_GET["end_date"]);

                    $filterStartStr = $_GET["start_date"] ?? "";
                    $filterEndStr = $_GET["end_date"] ?? "";

                    // echo "<div>Initial starting date: {$filterStartStr}</div>";
                    // echo "<div>Initial ending date: {$filterEndStr}</div>";

                    if ($isDefaultLoad) {
                        $filterStartStr = (new Datetime("monday this week"))->format("Y-m-d");
                        $filterEndStr = (new Datetime("sunday this week"))->format("Y-m-d");
                    }
                    
                    // echo "<div>Processing starting date: {$filterStartStr}</div>";
                    // echo "<div>Processing ending date: {$filterEndStr}</div>";

                    $filterStartDate = $filterStartStr ? new DateTime($filterStartStr) : null;
                    $filterEndDate = $filterEndStr ? new DateTime($filterEndStr) : null;
                    // Setting the end date to the very end of the day to ensure full-day coverage
                    if ($filterStartDate) {
                        $filterStartDate->setTime(0, 0, 0);
                    }
                    if ($filterEndDate) {
                        $filterEndDate->setTime(23, 59, 59);
                    }

                    foreach ($rawEvents as $row) {
                        $event = $eventRepo->hydrate($row);
                        // Checking if the current student has already volunteered to the event yet
                        $registration = (($this->eventRegisterRepo)->findViaCriteria([
                            "event_ID" => $event->getID(),
                            "student_ID" => $studentID]));
                        
                        $wasRegistered = empty($registration) ? false : true;
                        
                        if ($event->getStatus() === EventStatus::OPEN) {
                            $datePart = $event->getEventDate()->format("Y-m-d");
                            $timePart = $event->getEndTime()->format("H:i:s");
                            $expirationTime = new DateTime($datePart . ' ' . $timePart);

                            if ($now > $expirationTime) {
                                $event->setStatus(EventStatus::CLOSED);
                                $eventRepo->updateEventStatus($event->getID(), EventStatus::CLOSED);
                            }
                        }
                        $includeEvent = true;
                        $eventDate = $event->getEventDate();

                        $dateStr = $eventDate->format("Y-m-d H:i:s");
                        // echo "<div>Current retrieved event from the table: {$dateStr}</div>";

                        if ($filterStartDate && $eventDate < $filterStartDate) {
                            $includeEvent = false;
                        }

                        if ($filterEndDate && $eventDate > $filterEndDate) {
                            $includeEvent = false;
                        }

                        if ($includeEvent) {
                            $eventLocationID = (int)$event->getLocationID();
                            $eventAddress = ($this->locationRepo->findByID($eventLocationID))->getAddress();
                            // echo "<div>Saving {$eventAddress} as value for location ID: {$eventLocationID}</div>";
                            $eventAddressMapper[$eventLocationID] = $eventAddress;
                            // $events[] = $event;
                            $events[] = [$event, $wasRegistered];
                        }
                        
                    }
                }
            }
            // Fetching all joined members in the chosen club
            $membersList = [];
            $activeMemberships = ($this->membershipRepo)->findAllMembershipsViaStatus($clubID, MembershipStatus::APPROVE);
            if (!empty($activeMemberships)) {
                $studentRepo = new StudentRepository();
                foreach ($activeMemberships as $mRow) {
                    $mStudentID = (string)$mRow->getStudentID();
                    $mRoleID = (int)$mRow->getRoleID();

                    $studentData = $studentRepo->findByID($mStudentID);
                    $roleObj = ($this->roleRepo)->findByID($mRoleID);

                    $membersList[] = [
                        "student" => $studentData,
                        "role"      => $roleObj,
                        "joined_at" => $mRow->getJoinedTimeLine() === null ? new DateTime($mRow["joined_at"]) : null
                    ];
                }
            }

            

            $this->render("clubs/show", [
                "club"                  => $club,
                "isMember"              => $isMember,
                "events"                => $events,
                "members"               => $membersList ?? [],
                "currentUserRole"       => $currentUserRole,
                "eventAddressMapper"    => $eventAddressMapper ?? [],
                "filterStart"           => $filterStartStr ?? "",
                "filterEnd"             => $filterEndStr ?? ""
            ]);
        }
    }
?>
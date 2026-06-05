<?php
    require_once root_dir . "/app/controllers/BaseController.php";
    require_once root_dir . "/models/membership.php";
    require_once root_dir . "/models/role.php";
    require_once root_dir . "/models/student.php";

    class MembershipController extends BaseController {
        private MembershipRepository $membershipRepo;
        private RoleRepository $roleRepo;
        private StudentRepository $studentRepo;

        public function __construct() {
            $this->membershipRepo = new MembershipRepository();
            $this->roleRepo = new RoleRepository();
            $this->studentRepo = new StudentRepository();
        }

        /*
            GET /club/members?clubID=X
            Get all memberships within a specific club 
        */
        // public function clubMembers(): void {
        //     $clubID = (int)($_GET["club_ID"] ?? 0);
        //     if ($clubID < 1) {
        //         $this->render("errors/400", ["message" => "Invalid or missing club ID!"]);
        //         return;
        //     }

        //     $memberships = ($this->membershipRepo)->findAllMembersInAClub($clubID);
        //     $this->render("membership/club_members", [
        //         "clubID" => $clubID,
        //         "memberships" => $memberships
        //     ]);
        // }

        /*
            GET /student/membership?studentID=X 
            Get all club membersip statuses for a single student
        */
        public function studentMemberships(): void {
            $studentID = (int)($_GET["student_ID"] ?? 0);
            if ($studentID < 1) {
                $this->render("errors/400", ["message" => "Invalid or missing student ID!"]);
                return;
            }
            
            $memberships = ($this->membershipRepo)->findAllMembershipFromAStudent($studentID);
            $this->render("membership/all_from_student", [
                "studentID" => $studentID,
                "memberships" => $memberships
            ]);
        }

        /*
            POST /membership/join
            Processes a student's application to join a club 
        */
        public function join(): void {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /clubs');
                exit;
            }
            $studentID = (int)($_POST["student_ID"] ?? 0);
            $clubID = (int)($_POST["club_ID"] ?? 0);
            // All students whom register in the club will initially be member by default
            // They can be promoted later by the club administrator
            $role = ($this->roleRepo)->create(RoleTitle::MEMBER, RolePermission::REGULAR);
            $roleID = $role->getID();

            try {
                $membership = ($this->membershipRepo)->createJoinRequest($studentID, $clubID, $roleID);

                header("Location: /clubs/view?ID={$clubID}&msg=applied_successfully");
            } catch (RuntimeException $ex) {
                $this->render("clubs/view", [
                    "error" => $ex->getMessage(),
                    "clubID" => $clubID
                ]);
            }
        }

        /*
            POST /membership/update-status 
        */
        public function updateStatus(): void {
            if ($_SERVER["REQUEST_METHOD"] !== "POST") {
                header("Location: /");
                exit;
            }

            $membershipID = (int)($_POST["membershipID"] ?? 0);
            $action = trim($_POST["action"] ?? "");

            if ($membershipID < 1) {
                $this->render("errors/400", ["message" => "Missing membership record identifier!"]);
                return;
            }

            $isSuccess = false;

            switch ($action) {
                case "approve":
                    $isSuccess = ($this->membershipRepo)->approveMembership($membershipID);
                    break;
                case "reject":
                    $isSuccess = ($this->membershipRepo)->rejectMembership($membershipID);
                    break;
                case "leave":
                case "quit":
                    $isSuccess = ($this->membershipRepo)->membershipQuit($membershipID);
                    break;
                case "prohibit":
                case "ban":
                    $isSuccess = ($this->membershipRepo)->prohibitMembership($membershipID);
                    break;
                case "pending":
                    $isSuccess = ($this->membershipRepo)->pendingMembership($membershipID);
                    break;
                default:
                    $this->render("errors/400", ["message" => "Unsupported membership update!"]);
                    return;
            }

            if ($isSuccess) {
                // Returning back to the previous screen (dashboard or profile view)
                $fallbackURL = $_SERVER["HTTP_REFERER"] ?? "/clubs";
                header("Location: " . $fallbackURL);
                exit;
            } else {
                $this->render("errors/500", ["message" => "Database execution failed while applying status update!"]);
            }
        }

        public function getMembersJson(): void {
            header("Content-type: application/json");
            $clubID = (int)($_GET["club_ID"] ?? 0);

            if ($clubID < 1) {
                echo json_encode(["error" => "Invalid or missing club ID!"]);
                return;
            }

            try {
                $memberships = ($this->membershipRepo)->findAllMembershipsViaStatus($clubID, MembershipStatus::APPROVE);
                $results = [];
                foreach ($memberships as $m) {
                    $studentID = $m->getStudentID();
                    $student = ($this->studentRepo)->findByID($studentID);
                    $role = ($this->roleRepo)->findByID($m->getRoleID());
                    $results[] = [
                        "firstname" => $student->getFirstname(),
                        "lastname"  => $student->getLastname(),
                        "role"      => ($role->getTitle())->value
                    ];
                }
                echo json_encode(["members" => $results]);
            } catch (Exception $ex) {
                echo json_encode(["error" => $ex->getMessage()]);
            }
            exit;
        }
    }
?>
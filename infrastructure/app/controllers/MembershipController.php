<?php
    require_once root_dir . "/app/controllers/BaseController.php";
    require_once root_dir . "/models/membership.php";
    require_once root_dir . "/models/role.php";

    class MembershipController extends BaseController {
        private MembershipRepository $membershipRepo;
        private RoleRepository $roleRepo;

        public function __construct() {
            $this->membershipRepo = new MembershipRepository();
            $this->roleRepo       = new RoleRepository();
        }

        /**
         * GET /club/members?club_ID=X
         * Danh sách thành viên trong một câu lạc bộ
         */
        public function clubMembers(): void {
            $this->requireAuth();
            $clubID = $this->queryInt("club_ID");
            if ($clubID < 1) {
                $this->render("errors/400", ["message" => "Thiếu hoặc sai club ID!"]);
                return;
            }
            $memberships = $this->membershipRepo->findAllMembersInAClub($clubID);
            $this->render("membership/club_members", [
                "clubID"      => $clubID,
                "memberships" => $memberships
            ]);
        }

        /**
         * GET /student/memberships?student_ID=X
         * Danh sách câu lạc bộ mà sinh viên đã tham gia
         */
        public function studentMemberships(): void {
            $this->requireAuth();
            $studentID = (int)$this->getCurrentUserID();
            $memberships = $this->membershipRepo->findAllMembershipFromAStudent($studentID);
            $this->render("membership/all_from_student", [
                "studentID"   => $studentID,
                "memberships" => $memberships
            ]);
        }

        /**
         * POST /membership/apply
         * Sinh viên gửi đơn xin gia nhập CLB (lấy studentID từ session)
         */
        public function apply(): void {
            $this->requireAuth();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect("/final-project/infrastructure/clubs");
                return;
            }

            // Lấy studentID từ session — không trust POST data
            $studentID = (int)$this->getCurrentUserID();
            $clubID    = $this->postInt("club_ID");

            if ($clubID < 1) {
                $this->render("errors/400", ["message" => "Thiếu hoặc sai club ID!"]);
                return;
            }

            // Kiểm tra đã là thành viên chưa
            $existing = $this->membershipRepo->findMembership($studentID, $clubID);
            if ($existing !== null) {
                $this->redirect("/final-project/infrastructure/clubs?msg=already_member");
                return;
            }

            $role   = $this->roleRepo->create(RoleTitle::MEMBER, RolePermission::REGULAR);
            $roleID = $role->getID();

            try {
                $this->membershipRepo->createJoinRequest($studentID, $clubID, $roleID);
                $this->redirect("/final-project/infrastructure/clubs?msg=applied_successfully");
            } catch (RuntimeException $ex) {
                $this->render("clubs/index", [
                    "error"  => $ex->getMessage(),
                    "clubID" => $clubID
                ]);
            }
        }

        /**
         * POST /membership/join
         * Alias của apply() — xử lý logic giống nhau
         */
        public function join(): void {
            $this->apply();
        }

        /**
         * POST /membership/update
         * Cập nhật trạng thái thành viên (approve/reject/ban/leave/pending)
         */
        public function updateStatus(): void {
            $this->requireAuth();
            if ($_SERVER["REQUEST_METHOD"] !== "POST") {
                $this->redirect("/");
                return;
            }

            $membershipID = $this->postInt("membershipID");
            $action       = $this->post("action");

            if ($membershipID < 1) {
                $this->render("errors/400", ["message" => "Thiếu ID membership!"]);
                return;
            }

            $isSuccess = match ($action) {
                "approve"           => $this->membershipRepo->approveMembership($membershipID),
                "reject"            => $this->membershipRepo->rejectMembership($membershipID),
                "leave", "quit"     => $this->membershipRepo->membershipQuit($membershipID),
                "prohibit", "ban"   => $this->membershipRepo->prohibitMembership($membershipID),
                "pending"           => $this->membershipRepo->pendingMembership($membershipID),
                default             => null
            };

            if ($isSuccess === null) {
                $this->render("errors/400", ["message" => "Hành động không được hỗ trợ: {$action}"]);
                return;
            }

            if ($isSuccess) {
                $fallbackURL = $_SERVER["HTTP_REFERER"] ?? "/final-project/infrastructure/clubs";
                $this->redirect($fallbackURL);
            } else {
                $this->render("errors/500", [
                    "message" => "Cập nhật trạng thái thất bại!"
                ]);
            }
        }
    }
?>
<?php
    require_once root_dir . "/config/database-config.php";
    require_once root_dir . "/models/base.php";
    require_once root_dir . "/models/role.php";

    enum MembershipStatus: string {
        case APPROVE = "approval";
        case REJECT = "rejected";
        case LEAVE = "left";
        case PROHIBIT = "banned";
        case PENDING = "pending";
    }

    class Membership extends BaseModel {
        private ?int $ID;
        private int $studentID;
        private int $clubID;
        private int $roleID;
        private DateTime $joinedAt;
        private MembershipStatus $status;

        public function __construct(int $sID, int $cID, int $rID, DateTime $jAt, ?MembershipStatus $s = null, ?int $id = null) {
            if ($id !== null) {
                $this->setID($id);
            }
            $this->setStudentID($sID);
            $this->setClubID($cID);
            $this->setRoleID($rID);
            $this->setJoinedTimeline($jAt);
            $this->setStatus($s);
        }

        private function setID(int $id): void {
            $this->setIDForAutoIncrementType($this->ID, $id);
        }

        private function setStudentID(int $id): void {
            $this->setIDForAutoIncrementType($this->studentID, $id);
        }

        private function setClubID(int $id): void {
            $this->setIDForAutoIncrementType($this->clubID, $id);
        }

        private function setRoleID(int $id): void {
            $this->setIDForAutoIncrementType($this->roleID, $id);
        }

        private function setJoinedTimeline(DateTime $x): void {
            $this->joinedAt = $x;
        }

        private function markApprove(): void {
            $this->status = MembershipStatus::APPROVE;
        }

        private function markReject(): void {
            $this->status = MembershipStatus::REJECT;
        }

        private function markLeave(): void {
            $this->status = MembershipStatus::LEAVE;
        }

        private function markProhibit(): void {
            $this->status = MembershipStatus::PROHIBIT;
        }

        private function markPending(): void {
            $this->status = MembershipStatus::PENDING;
        }

        public function setStatus(?string $s): void {
            switch ($s) {
                case "approve":
                    $this->markApprove();
                    break;
                case "reject":
                    $this->markReject();
                    break;
                case "prohibit":
                    $this->markProhibit();
                    break;
                case "leave":
                    $this->markLeave();
                    break;
                default:
                    $this->markPending();
                    break;
            }
        }
        
        public function getID(): int {return $this->ID;}
        public function getStudentID(): int {return $this->studentID;}
        public function getClubID(): int {return $this->clubID;}
        public function getRoleID(): int {return $this->roleID;}
        public function getJoinedTimeline(): DateTime {return $this->joinedAt;}
        public function getStatus(): MembershipStatus {return $this->status;}
    }

    class MembershipRepository extends BaseRepository {
        private RoleRepository $repo;

        public function __construct() {
            parent::__construct("membership");
        }

        public function createJoinRequest(int $sID, int $cID, int $rID): Membership {
            $existing = $this->findViaCriteria([
                "student_ID" => $sID,
                "club_ID" => $cID,
                "role_ID" => $rID
            ]);
            if (!empty($existing)) {
                throw new RuntimeException("Membership already exists!");
            }

            $membership = new Membership(
                $sID,
                $cID,
                $rID,
                new DateTime()
            );
            $isSucess = $this->add([
                "student_ID" => $membership->getStudentID(),
                "club_ID" => $membership->getClubID(),
                "joined_at" => $membership->getJoinedTimeline()->format("Y-m-d H:i:s"),
                "status" => $membership->getStatus(),
                "role_ID" => $membership->getRoleID()
            ]);

            if (!$isSucess) throw new RuntimeException("Failed to create membership request");

            return $membership;
        }

        protected function hydrate(array $row): Membership {
            if (empty($row)) throw new RuntimeException("Empty row!");

            try {
                $jAt = new DateTime($row["joined_at"]);
                $membership = new Membership(
                    (int)$row["student_ID"],
                    (int)$row["club_ID"],
                    (int)$row["role_ID"],
                    $jAt,
                    MembershipStatus::from($row["membership_status"]),
                    (int)$row["ID"]
                );
                return $membership;
            } catch (PDOException $ex) {
                error_log($ex->getMessage());
                throw new RuntimeException("Invalid founded date!");
            }
        }

        public function findByID(int $id): ?Membership {
            $rows = $this->findViaCriteria(["ID" => $id]);
            if (empty($rows)) return null;

            return $rows[0];
        }

        public function findMembership(int $sID, int $cID): ?Membership {
            $rows = $this->findViaCriteria([
                "student_ID" => $sID,
                "club_ID" => $cID
            ]);
            if (empty($rows)) return null;
            return $rows[0];
        }

        public function findAllMembersInAClub(int $cID): array {
            $rows = $this->findViaCriteria(["club_ID" => $cID]);
            return array_map(
                fn ($row) => $row, $rows
            );
        }

        public function findAllMembershipFromAStudent(int $sID): array {
            $rows = $this->findViaCriteria(["student_ID" => $sID]);
            return array_map(
                fn ($row) => $row, $rows
            );
        }

        public function approveMembership(int $id): bool {
            return $this->updateViaCriteria([
                "status" => (MembershipStatus::APPROVE)->value
            ], ["ID" => $id]);
        }

        public function rejectMembership(int $id): bool {
            return $this->updateViaCriteria(["status" => (MembershipStatus::REJECT)->value], ["ID" => $id]);
        }

        public function pendingMembership(int $id): bool {
            return $this->updateViaCriteria(["status" => (MembershipStatus::PENDING)->value], ["ID" => $id]);
        }

        public function membershipQuit(int $id): bool {
            return $this->updateViaCriteria(["status" => (MembershipStatus::LEAVE)->value], ["ID" => $id]);
        }

        public function prohibitMembership(int $id): bool {
            return $this->updateViaCriteria(["status" => (MembershipStatus::PROHIBIT)->value], ["ID" => $id]);
        }

        public function promoteMembershipRole(int $id, RoleTitle $t): bool {
            return ($this->repo)->promoteRole($id, $t);
        }

        public function promoteMembershipPermission(int $id, RolePermission $rP): bool {
            $isSuccess = ($this->repo)->promotePermission($id, $rP);
            return $isSuccess;
        }

        public function findByRole(RoleTitle $title): array {
            $roleIDs = $this->getAllFromColumn(["role_ID"]);
            $roleIDs = array_map(
                fn ($row) => (int)$row, $roleIDs
            );
            $equivalent = [];
            for ($i = 0; $i < count($roleIDs); $i++) {
                $curr = ($this->repo)->findByID($roleIDs[$i]);
                if ($curr->getTitle() === $title) {
                    $equivalent[] = $curr;
                }
            }
            return $equivalent;
        }
    }
?>
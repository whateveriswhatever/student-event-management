<?php
    require_once root_dir . "/config/database-config.php";
    require_once root_dir . "/models/base.php";

    enum RoleTitle: string {
        case PRESIDENT = "president";
        case VP = "vice president";
        case SECRETARY = "secretary";
        case MEMBER = "member";
    };

    enum RolePermission: string {
        case REGULAR = "regular";
        case MODERATOR = "moderator";
        case MANAGER = "manager";
    };

    class Role extends BaseModel {
        private ?int $ID;
        private RoleTitle $roleTitle;
        private RolePermission $permission;

        public function __construct(RoleTitle $rT, RolePermission $rP, ?int $id = null) {
            $this->setID($id);
            $this->setTitle($rT);
            $this->setPermission($rP);
        }

        private function setID(?int $id): void {
            if ($id !== null) {
                if ($id <= 0) {
                    throw new InvalidArgumentException("Invalid ID: must be greater than 0!");
                }
                $this->ID = $id;
            }
        }
        private function setTitle(RoleTitle $x): void {
            $this->roleTitle = $x;
        }
        private function setPermission(RolePermission $x): void {$this->permission = $x;}

        public function getID(): ?int {return $this->ID;}
        public function getTitle(): RoleTitle {return $this->roleTitle;}
        public function getPermission(): RolePermission {return $this->permission;}

        public function promoteToVP(): void {
            $this->setTitle(RoleTitle::VP);
        }

        public function promoteToPresident(): void {
            $this->setTitle(RoleTitle::PRESIDENT);
        }

        public function promoteToSecretary(): void {
            $this->setTitle(RoleTitle::SECRETARY);
        }

        public function promotePermission(): void {
            $curr = $this->getPermission();
            if ($curr === RolePermission::REGULAR) {
                $this->setPermission(RolePermission::MODERATOR);
            } else if ($curr === RolePermission::MODERATOR) {
                $this->setPermission(RolePermission::MANAGER);
            } else {
                throw new RuntimeException("User is having the ultimate permission!");
            }      
        }
    }


    class RoleRepository extends BaseRepository {
        public function __construct() {
            parent::__construct("role");
        }

        public function promotePermission(int $id, RolePermission $rP): bool {
            $isSuccess = $this->updateViaCriteria(["permission" => $rP->value], ["ID" => $id]);
            return $isSuccess;
        }

        public function promoteRole(int $id, RoleTitle $rT): bool {
            $isSuccess = $this->updateViaCriteria(["role_title" => $rT->value], ["ID" => $id]);
            return $isSuccess;
        }

        protected function hydrate(array $row): Role {
            return new Role(
                RoleTitle::from($row["role_title"]),
                RolePermission::from($row["permission"]),
                (int)$row["ID"]
            );
        }
        
        public function findByID(int $id): ?Role {
            $res = $this->findViaCriteria(["ID" => $id]);
            if (empty($res)) return null;
            return $this->hydrate($res[0]);
        }

        public function save(Role $role): bool {
            return $this->updateViaCriteria([
                "role_title" => $role->getTitle()->value,
                "permission" => $role->getPermission()->value
            ], [
                "ID" => $role->getID()
            ]);
        }

        public function create(RoleTitle $rT, RolePermission $rP): ?Role {
            $role = new Role($rT, $rP);
            $isSuccess = $this->add(["role_title" => ($role->getTitle())->value, "permission" => ($role->getPermission())->value]);
            if (!$isSuccess) throw new RuntimeException("Failed to create a new role!");
            $generatedID = $this->getLatestID();
            return new Role($rT, $rP, $generatedID);
        }

        public function findByTitle(RoleTitle $rT): array {
            $rows = $this->findViaCriteria(["role_title" => $rT->value]);
            $objects = array_map(
                fn ($row) => $this->hydrate($row), $rows
            );
            return $objects;
        }

        public function findByPermission(RolePermission $rP): array {
            $rows = $this->findViaCriteria(["permission" => $rP->value]);
            $objs = array_map(fn($row) => $this->hydrate($row), $rows);
            return $objs;
        }
    }
?>
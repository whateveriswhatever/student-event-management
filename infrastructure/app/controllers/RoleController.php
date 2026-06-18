<?php

    require_once root_dir . "/app/controllers/BaseController.php";
    require_once root_dir . "/models/role.php";

    class RoleController extends BaseController {
        private RoleRepository $roleRepository;

        public function __construct() {
            $this->roleRepository = new RoleRepository();
        }

        // POST /admin/roles/create
        public function store(): void {
            $this->requireAdmin();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header("Location: /admin/roles");
                exit;
            }
            try {
                $title = RoleTitle::from($_POST['title'] ?? 'member');
                $permission = RolePermission::from($_POST['permission'] ?? 'regular');

                $this->roleRepository->create($title, $permission);
                header("Location: /admin/roles?success=1");
                exit;
            } catch (Exception $e) {
                $this->json(['error' => $e->getMessage()], 400);
            }
    }
}
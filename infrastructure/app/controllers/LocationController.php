<?php

    require_once root_dir . "/app/controllers/BaseController.php";
    require_once root_dir . "/models/location.php";

    class LocationController extends BaseController {
        private LocationRepository $locationRepository;

        public function __construct() {
            $this->locationRepository = new LocationRepository();
        }

        // GET /locations
        public function index(): void {
            $locations = $this->locationRepository->all();
            $this->render('admin/locations', ['locations' => $locations]);
        }

        // POST /locations/create
        public function store(): void {
            $building = trim($_POST['building'] ?? '');
            $room = trim($_POST['room'] ?? '');
            $capacity = (int)($_POST['capacity'] ?? 0);

            try {
                // This utilizes your custom logic throwing exceptions on special text patterns
                $this->locationRepository->create($building, $room, $capacity);
                header("Location: /locations?success=1");
                exit;
            } catch (Exception $e) {
                $this->render('admin/locations', ['error' => $e->getMessage(), 'locations' => $this->locationRepository->all()]);
            }
        }
}
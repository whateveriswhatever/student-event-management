<?php
    require_once root_dir . "/app/controllers/BaseController.php";
    require_once root_dir . "/models/location.php";

    class LocationController extends BaseController {
        private LocationRepository $locationRepository;

        public function __construct() {
            $this->locationRepository = new LocationRepository();
        }

        /** GET /locations — Danh sách tất cả địa điểm */
        public function index(): void {
            $this->requireAuth();
            $locations = $this->locationRepository->all();
            $this->render("admin/locations", ["locations" => $locations]);
        }

        /** POST /locations/create — Tạo địa điểm mới */
        public function store(): void {
            $this->requireAuth();
            $building = $this->post("building");
            $room     = $this->post("room");
            $capacity = $this->postInt("capacity");

            if (empty($building) || empty($room)) {
                $this->render("admin/locations", [
                    "error"     => "Tên tòa nhà và phòng không được để trống!",
                    "locations" => $this->locationRepository->all()
                ]);
                return;
            }

            try {
                $this->locationRepository->create($building, $room, $capacity);
                $this->redirect(BASE_URL . "/locations?success=1");
            } catch (Exception $e) {
                $this->render("admin/locations", [
                    "error"     => $e->getMessage(),
                    "locations" => $this->locationRepository->all()
                ]);
            }
        }
    }
?>
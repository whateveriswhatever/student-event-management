<?php
// controllers/AttendanceController.php
require_once root_dir . "/app/controllers/BaseController.php";
require_once root_dir . "/models/attendance.php";

class AttendanceController extends BaseController {
    private AttendanceRepository $attendanceRepository;

    public function __construct() {
        $this->attendanceRepository = new AttendanceRepository();
    }

    // POST /attendance/checkin
    public function checkIn(): void {
        $registrationId = (int)($_POST['registered_ID'] ?? 0);

        try {
            $payload = [
                "registered_ID" => $registrationId,
                "check_in_time" => (new DateTime())->format('Y-m-d H:i:s'),
                "attendance_status" => AttendanceStatus::CHECKIN->value
            ];

            if ($this->attendanceRepository->add($payload)) {
                header("Location: " . $_SERVER['HTTP_REFERER'] . "&checkin=success");
                exit;
            }
        } catch (Exception $e) {
            $this->render('events/dashboard', ['error' => $e->getMessage()]);
        }
    }
}
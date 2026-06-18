<?php
    require_once root_dir . "/app/controllers/BaseController.php";
    require_once root_dir . "/models/attendance.php";

    class AttendanceController extends BaseController {
        private AttendanceRepository $attendanceRepository;

        public function __construct() {
            $this->attendanceRepository = new AttendanceRepository();
        }

        /** POST /attendance/checkin — Điểm danh vào sự kiện */
        public function checkIn(): void {
            $this->requireAuth();
            $registrationId = $this->postInt("registered_ID");

            if ($registrationId < 1) {
                $this->jsonError("ID đăng ký không hợp lệ!", 400);
                return;
            }

            // Kiểm tra đã check-in chưa (tránh check-in trùng)
            $alreadyCheckedIn = $this->attendanceRepository->exists([
                "registration_ID"   => $registrationId,
                "attendance_status" => AttendanceStatus::CHECKIN->value
            ]);
            if ($alreadyCheckedIn) {
                $this->jsonError("Bạn đã điểm danh cho sự kiện này rồi!", 409);
                return;
            }

            try {
                $payload = [
                    "registration_ID"   => $registrationId,
                    "checkin_time"      => (new DateTime())->format("Y-m-d H:i:s"),
                    "attendance_status" => AttendanceStatus::CHECKIN->value
                ];
                if ($this->attendanceRepository->add($payload)) {
                    $referer = $_SERVER["HTTP_REFERER"] ?? BASE_URL . "/";
                    if (!str_starts_with($referer, BASE_URL)) {
                        $referer = BASE_URL . "/";
                    }
                    $separator = parse_url($referer, PHP_URL_QUERY) ? '&' : '?';
                    $this->redirect($referer . $separator . "checkin=success");
                }
            } catch (Exception $e) {
                $this->jsonError($e->getMessage(), 500);
            }
        }
    }
?>
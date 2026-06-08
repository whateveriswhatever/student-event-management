<?php
    require_once root_dir . "/app/controllers/BaseController.php";
    require_once root_dir . "/models/event.php";

    class EventController extends BaseController {
        private EventRepository $eventRepo;
        private EventRegistrationRepository $registrationRepo;

        public function __construct() {
            $this->eventRepo        = new EventRepository();
            $this->registrationRepo = new EventRegistrationRepository();
        }

        /** GET /events — Danh sách tất cả sự kiện */
        public function index(): void {
            $events = $this->eventRepo->all();
            $this->render("events/index", ["events" => $events]);
        }

        /** POST /events/register — Đăng ký tham gia sự kiện */
        public function registerForEvent(): void {
            $this->requireAuth();
            if ($_SERVER["REQUEST_METHOD"] !== "POST") {
                $this->jsonError("Invalid request method", 405);
                return;
            }
            try {
                $eventID   = $this->postInt("event_ID");
                // Lấy studentID từ session thay vì tin tưởng POST data
                $studentID = (int)$this->getCurrentUserID();

                if ($eventID < 1 || $studentID < 1) {
                    $this->jsonError("Thiếu thông tin sự kiện hoặc sinh viên!", 400);
                    return;
                }

                // Kiểm tra đã đăng ký chưa
                $alreadyRegistered = $this->registrationRepo->exists([
                    "event_ID"   => $eventID,
                    "student_ID" => $studentID
                ]);
                if ($alreadyRegistered) {
                    $this->jsonError("Bạn đã đăng ký sự kiện này rồi!", 409);
                    return;
                }

                $registration = $this->registrationRepo->register(
                    $eventID, $studentID, new DateTime(), "pending"
                );
                $this->jsonSuccess(
                    "Đăng ký sự kiện thành công!",
                    ["ID" => $registration->getID()]
                );
            } catch (Exception $ex) {
                $this->jsonError($ex->getMessage(), 500);
            }
        }
    }
?>
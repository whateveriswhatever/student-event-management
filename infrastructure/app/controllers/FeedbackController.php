<?php
    require_once root_dir . "/app/controllers/BaseController.php";
    require_once root_dir . "/models/feedback.php";

    class FeedbackController extends BaseController {
        private FeedBackRepository $feedbackRepository;

        public function __construct() {
            $this->feedbackRepository = new FeedBackRepository();
        }

        /** POST /feedback/submit — Gửi đánh giá về sự kiện */
        public function store(): void {
            $this->requireAuth();

            // Lấy fromUserID từ session — không trust POST data
            $fromUserID = (int)$this->getCurrentUserID();
            $toUserID   = $this->postInt("to_user_ID");
            $eventID    = $this->postInt("event_ID");
            $content    = $this->post("content");

            if ($toUserID < 1 || $eventID < 1) {
                $this->jsonError("Thông tin người nhận hoặc sự kiện không hợp lệ!", 400);
                return;
            }
            if (empty($content)) {
                $this->jsonError("Nội dung đánh giá không được để trống!", 400);
                return;
            }
            if ($fromUserID === $toUserID) {
                $this->jsonError("Không thể gửi đánh giá cho chính mình!", 400);
                return;
            }

            try {
                $this->feedbackRepository->create($fromUserID, $toUserID, $eventID, $content);
                $this->redirect(BASE_URL . "/events?id={$eventID}&feedback=submitted");
            } catch (Exception $e) {
                $this->jsonError($e->getMessage(), 400);
            }
        }
    }
?>
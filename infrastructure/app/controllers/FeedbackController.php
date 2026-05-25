<?php
    // Allows students to submit qualitative feedback or evaluations for an event they attend
    require_once root_dir . "/app/controllers/BaseController.php";
    require_once root_dir . "/models/feedback.php";

    class FeedbackController extends BaseController {
        private FeedbackRepository $feedbackRepository;

        public function __construct() {
            $this->feedbackRepository = new FeedbackRepository();
        }

        // POST /feedback/submit
        public function store(): void {
            $fromUserID = (int)($_POST['from_user_ID'] ?? 0);
            $toUserID = (int)($_POST['to_user_ID'] ?? 0); // Target club coordinator/officer
            $eventID = (int)($_POST['event_ID'] ?? 0);
            $content = trim($_POST['content'] ?? '');

            try {
                $payload = [
                    "from_user_ID" => $fromUserID,
                    "to_user_ID" => $toUserID,
                    "to_event_ID" => $eventID,
                    "content" => $content,
                    "timestamp" => (new DateTime())->format('Y-m-d H:i:s')
                ];

                if ($this->feedbackRepository->add($payload)) {
                    header("Location: /events?id={$eventID}&feedback=submitted");
                    exit;
                }
            } catch (Exception $e) {
                $this->json(['error' => $e->getMessage()], 400);
            }
    }
}
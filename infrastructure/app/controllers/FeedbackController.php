<?php
    // Allows students to submit qualitative feedback or evaluations for an event they attend
    require_once root_dir . "/app/controllers/BaseController.php";
    require_once root_dir . "/models/feedback.php";
    require_once root_dir . "/models/event.php";

    class FeedbackController extends BaseController {
        private FeedbackRepository $feedbackRepo;
        private EventRepository $eventRepo;

        public function __construct() {
            $this->feedbackRepo = new FeedbackRepository();
            $this->eventRepo = new EventRepository();
        }

        // POST /feedbacks/submit
        public function store(): void {
            $data = json_decode(file_get_contents("php://input"), true);
            $fromUserID = (string)($_POST['from_user_ID'] ?? "");
            $toUserID = (string)($_POST['to_user_ID'] ?? ""); // Target club coordinator/officer
            $eventID = (int)($_POST['event_ID'] ?? 0);
            $content = trim($_POST['content'] ?? '');
            echo "</br></br>";
            echo "<div>From User ID: {$fromUserID}</div>";
            echo "<div>To User ID: {$toUserID}</div>";
            echo "<div>On Event ID: {$eventID}</div>";
            echo "<div>Content: {$content}</div>";

            try {
                $newOne = ($this->feedbackRepo)->create($fromUserID, $eventID, $content, $toUserID);
                if ($newOne) {
                    $this->json([
                        "status" => "success",
                        "content" => "created a new feedback!"
                    ]);
                } else {
                    $this->json([
                        "status"    => "failed",
                        "content"   => "failed to create a new feedback!"
                    ]);
                }
            } catch (Exception $e) {
                $this->json(['error' => $e->getMessage()], 400);
            }    
        }

        // GET /feedbacks/chat-history
        public function chatHistory(): void {
            $data = json_decode(file_get_contents("php://input"), true);
            $eventID = (int)($data["event_ID"] ?? 0);
            // echo "<div>Event ID: {$eventID}</div>";

            try {
                $event = ($this->eventRepo)->findByID($eventID);
                if (!$event) {
                    $this->json(["error" => "event doesn't exist!"], 404);
                    return;
                }

                $feedbacks = ($this->feedbackRepo)->findAllFromEvent($event->getID());
                $objArrs = array_map(
                    fn (Feedback $f) => ($this->feedbackRepo)->toArray($f), $feedbacks
                );
                $size = count($feedbacks);
                // echo "<div>Total feedbacks: {$size}</div>";
                $this->json(
                    ["data" => $objArrs]
                );
            } catch (Exception $ex) {
                $this->json(["error" => $ex->getMessage()], 404);
            }
        }
    }
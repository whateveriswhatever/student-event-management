<?php
    require_once root_dir . "/app/controllers/BaseController.php";
    require_once root_dir . "/models/event.php";

    class EventController extends BaseController {
        private EventRepository $eventRepo;
        private EventRegistrationRepository$registrationRepo;

        public function __construct() {
            $this->eventRepo = new EventRepository();
            $this->registrationRepo = new EventRegistrationRepository();
        }

        // POST /events/register
        public function registerForEvent(): void {
            if ($_SERVER["REQUEST_METHOD"] !== "POST") {
                $this->json(["error" => "Invalid request method"], 405);
                return;
            }

            try {
                $eventID = (int)($_POST["event_ID"] ?? null);
                $studentID = (int)($_POST["student_ID"] ?? null);
                $now = new DateTime();

                $registration = ($this->registrationRepo)->register($eventID, $studentID, $now, "pending");

                if ($registration) {
                    $this->json(["message" => "Successfully reigstered for the new event!", "ID" => $registration->getID()]);
                } else {
                    $this->json(["error" => "Registration failed!"], 400);
                }
            } catch (Exception $ex) {
                $this->json(["error" => $ex->getMessage()], 500);
            }
        }
    }
?>
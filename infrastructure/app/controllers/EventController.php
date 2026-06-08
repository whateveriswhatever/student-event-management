<?php
    require_once root_dir . "/app/controllers/BaseController.php";
    require_once root_dir . "/models/event.php";
    require_once root_dir . "/models/location.php";

    class EventController extends BaseController {
        private EventRepository $eventRepo;
        private EventRegistrationRepository $registrationRepo;
        private LocationRepository $locationRepo;

        public function __construct() {
            $this->eventRepo = new EventRepository();
            $this->registrationRepo = new EventRegistrationRepository();
            $this->locationRepo = new LocationRepository();
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

                $event = ($this->eventRepo)->findByID($eventID);
                $currParticipants = $event->getCurrParticipants();
                $maxParticipants = $event->getMaxParticipants();

                if ($currParticipants === $maxParticipants) {
                    $this->json(["message" => "Failed to assign for the event owing to exceeding on maximum participants!"]);
                }

                $registration = ($this->registrationRepo)->register($eventID, $studentID, $now, "success");

                if ($registration) {
                    $this->json(["message" => "Successfully reigstered for the new event!", "ID" => $registration->getID()]);
                    // Updating the current participants and displaying it to the UI
                    
                } else {
                    $this->json(["error" => "Registration failed!"], 400);
                }
            } catch (Exception $ex) {
                $this->json(["error" => $ex->getMessage()], 500);
            }
        }

        /* POST /events/create */
        public function store(): void {
            if ($_SERVER["REQUEST_METHOD"] !== "POST") {
                $this->json(["error" => "Method is not allowed!"], 405);
                return;
            }

            try {
                $clubID = (int)$_POST["club_ID"];
                $title = trim($_POST["title"]);
                $description = trim($_POST["description"]);
                $eventDate = new DateTime($_POST["event_date"]);
                $startTime = new DateTime($_POST["start_time"]);
                $endTime = new DateTime($_POST["end_time"]);
                $maxParticipants = (int)$_POST["max_participants"];

                $building = trim($_POST["location_building"]);
                $room = trim($_POST["location_room"]);
                $locationCapacity = (int)$_POST["location_capacity"];

                // Validation: Event participants can not exceed the room capacity for the event
                if ($maxParticipants > $locationCapacity) {
                    throw new Exception("Event max participants cannot exceed the room's capacity!");
                }

                $newLocation = ($this->locationRepo)->create($building, $room, $locationCapacity);
                if (!$newLocation || $newLocation->getID() === null) {
                    throw new Exception("Failed to save the new location into the database!");
                }

                $locationID = $newLocation->getID();
                $newEvent = ($this->eventRepo)->create(
                    $clubID,
                    $title,
                    $description,
                    $eventDate,
                    $startTime,
                    $endTime,
                    $locationID,
                    $maxParticipants,
                    0,
                    EventStatus::OPEN
                );

                if ($newEvent) {
                    $this->redirect(base_folder_path . "/clubs/show?id=" . $clubID . "&success=event_created");
                } else {
                    throw new Exception("Failed to save the event into the database!");
                }
            } catch (Exception $ex) {
                // Log and fallback
                error_log("Event Creation Error: " . $ex->getMessage());
                // Redirecting back with an error URL parameter so we can show a popup/alert on the frontend
                $fallbackClubID = (int)($_POST["club_ID"] ?? 0);
                if ($fallbackClubID > 0) {
                    $this->redirect(base_folder_path . "/clubs/show?id=" . $fallbackClubID . "&error=" . urlencode($ex->getMessage()));
                } else {
                    $this->redirect(base_folder_path . "/clubs");
                }
            }
        }
    }
?>
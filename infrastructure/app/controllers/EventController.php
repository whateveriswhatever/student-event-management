<?php
    require_once root_dir . "/app/controllers/BaseController.php";
    require_once root_dir . "/models/event.php";
    require_once root_dir . "/models/location.php";
    require_once root_dir . "/models/membership.php";
    require_once root_dir . "/models/feedback.php";
    require_once root_dir . "/models/student.php";

    class EventController extends BaseController {
        private EventRepository $eventRepo;
        private EventRegistrationRepository $registrationRepo;
        private LocationRepository $locationRepo;
        private MembershipRepository $membershipRepo;
        private FeedBackRepository $feedbackRepo;
        private StudentRepository $studentRepo;

        public function __construct() {
            $this->eventRepo = new EventRepository();
            $this->registrationRepo = new EventRegistrationRepository();
            $this->locationRepo = new LocationRepository();
            $this->membershipRepo = new MembershipRepository();
            $this->feedbackRepo = new FeedBackRepository();
            $this->studentRepo = new StudentRepository();
        }

        public function index(): void {
            $searchQuery = $_GET["search"] ?? '';
            $studentID = $_SESSION["user_ID"] ?? null;

            if (!empty($searchQuery)) {
                $events = ($this->eventRepo)->searchByName($searchQuery);
            } else {
                $events = ($this->eventRepo)->findAll();
            }

            // Checking if logged in user has already registered in the event yet
            for ($i = 0; $i < count($events); $i++) {
                $curr = $events[$i];
                $eventID = $curr->getID();
                $wasRegistered = ($this->registrationRepo)->checkRegistration($studentID, $eventID);
                $events[$i] = [$curr, $wasRegistered];
            }

            // Determining user's club memberships for being able to see the private events
            $userJoinedClubIDs = [];
            if ($studentID) {
                $memberships = ($this->membershipRepo)->findViaCriteria([
                    "student_ID"        => $studentID,
                    "membership_status" => "active"
                ]);
                foreach ($memberships as $m) {
                    $userJoinedClubIDs[] = (int)$m["club_ID"];
                }       
            }

            $this->render("events/index", [
                "events"            => $events,
                "userJoinedClubIDs" => $userJoinedClubIDs,
                "searchQuery"       => $searchQuery,
                "studentID"         => $studentID
            ]);
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
                $clubID = (int)($_POST["club_ID"] ?? null);
                $now = new DateTime();

                $event = ($this->eventRepo)->findByID($eventID);
                $currParticipants = $event->getCurrParticipants();
                $maxParticipants = $event->getMaxParticipants();

                if ($currParticipants === $maxParticipants) {
                    $this->json(["message" => "Failed to assign for the event owing to exceeding on maximum participants!"]);
                }

                $registration = ($this->registrationRepo)->register($eventID, $studentID, $now, "success");

                if ($registration) {
                    // $this->json(["message" => "Successfully reigstered for the new event!", "ID" => $registration->getID()]);
                    // Updating the current participants and displaying it to the UI
                    $this->redirect(base_folder_path . "/clubs/show?id={$clubID}&status=registered_successfully");
                    
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
                $dateInput = $_POST["event_date"];
                $startTimeInput = $_POST["start_time"];
                $endTimeInput = $_POST["end_time"];
                $eventDate = new DateTime($dateInput);
                $startTime = new DateTime($dateInput . " " . $startTimeInput);
                $endTime = new DateTime($dateInput . " " . $endTimeInput);

                if ($endTime <= $startTime) {
                    throw new Exception("The event's ending time must be after its starting time!");
                }

                $maxParticipants = (int)$_POST["max_participants"];
                $isPrivate = (bool)$_POST["is_private"];

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
                    EventStatus::OPEN,
                    $isPrivate
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

        /* GET /events/comments */
        public function getEventComments(): void {
            header("Content-type: application/json");
            $eventID = (int)($_GET["event_ID"] ?? 0);

            if ($eventID < 1) {
                echo json_encode(["error" => "Invalid event ID"]);
                return;
            }

            try {
                $messages = ($this->feedbackRepo)->findAllFromEvent($eventID);
                $results = [];

                foreach ($messages as $msg) {
                    $sender = ($this->studentRepo)->findByID($msg->getFromID());
                    $results[] = [
                        "senderID"      => $sender->getID(),
                        "senderName"    => $sender->getFirstname() . " " . $sender->getLastname(),
                        "inital"        => mb_substr($sender->getFirstname(), 0, 1),
                        "content"       => $msg->getContent(),
                        "timeAgo"       => ($this->feedbackRepo)->timeElapsedString($msg->getEventDate())   // Helper function to show "2 hours ago"
                    ];
                }
                echo json_encode(["comments" => $results]);
            } catch (Exception $ex) {
                echo json_encode(["error" => $ex->getMessage()]);
            }
            exit;
        }

        /* POST /events/comments */
        public function postEventComment(): void {
            header("Content-type: application/json");
            $data = json_decode(file_get_contents("php://input"), true);

            $eventID = (int)($data["event_ID"] ?? 0);
            $content = trim($data["content"] ?? "");
            $senderID = (string)($_SESSION["user_ID"] ?? 0);
            $receiverID = (string)($_SESSION["receiver_ID"] ?? null);

            if ($senderID === null) {
                echo json_encode([
                    "success"   => false,
                    "error"     => "You must be logged in to comment."
                ]);
                return;
            }

            if ($eventID < 1 || empty($content)) {
                echo json_encode([
                    "success"   => false,
                    "error"     => "Invalid data"
                ]);
                return;
            }

            try {
                // null is passed for the receiver ID
                $feedback = ($this->feedbackRepo)->create($senderID,  $eventID, $content, $receiverID);
                echo json_encode([
                    "success"   => true
                ]);

            } catch (Exception $ex) {
                echo json_encode(
                    [
                        "success"   => false,
                        "error"     => $ex->getMessage()
                    ]
                );
            }
        }

    }
?>
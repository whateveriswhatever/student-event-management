<?php
    require_once root_dir . "/app/controllers/BaseController.php";
    require_once root_dir . "/models/friendship.php";

    class FriendshipController extends BaseController {
        private FriendshipRepository $friendshipRepo;
        private StudentRepository $studentRepo;
        private FriendshipRecommendationService $recommendationService;

        public function __construct() {
            $this->friendshipRepo = new FriendshipRepository();
            $this->studentRepo = new StudentRepository();
            $this->recommendationService = new FriendshipRecommendationService();
        }

        /*
            GET /friends 
        */
        public function index(): void {
            $currUserID = (string)($_SESSION["user_ID"] ?? '');

            if (empty($currUserID)) {
                $this->redirect(base_folder_path . "/login");
            }

            // Fetching pending requests
            // Only display requests those at `sender_ID` column isn't the logged in user's ID
            $pendingRequests = ($this->friendshipRepo)->getAllPendingRequestsFromUserID($currUserID);
            $storage = [];
            foreach ($pendingRequests as $request) {
                $senderID = $request->getSenderID();
                $senderData = ($this->studentRepo)->findByID($senderID);
                if ($senderData) {
                    $storage[] = [
                        "ID"        => $senderData->getID(),
                        "firstname" => $senderData->getFirstname(),
                        "lastname"  => $senderData->getLastname() 
                    ];
                }
            }

            // Fetch graph recommendation
            $displayUsers = ($this->recommendationService)->getRecommendations($currUserID);

            // Render View using standard 'displayUsers' variable
            $this->render("friends/index", [
                "currUserID"           => $currUserID,
                "pendingRequests"   => $storage,
                "displayUsers"      => $displayUsers,
                "isSearching"       => false
            ]);
        }

        /*
            POST /friends/add 
        */
        public function addFriend(): void {
            $currUserID = (string)($_SESSION["user_ID"] ?? '');
            $receiverID = $_POST["receiver_ID"] ?? '';

            // echo "<div>Sender: {$currUserID}</div>";
            // echo "<div>Receiver: {$receiverID}</div>";
            
            if (!empty($currUserID) && !empty($receiverID)
                && $currUserID != $receiverID) {
                // Preventing reverse duplicates: checking if any relationship already exists
                $existingA = ($this->friendshipRepo)->findRelationship($currUserID, $receiverID);
                $existingB = ($this->friendshipRepo)->findRelationship($receiverID, $currUserID);

                // var_dump("3. Existing A (Should be NULL): ");
                // var_dump($existingA);
    
                // var_dump("4. Existing B (Should be NULL): ");
                // var_dump($existingB);

                // Only insterting if no relationship exists in either direction
                if ($existingA === null && $existingB === null) {
                    $isSuccess = ($this->friendshipRepo)->sendRequest($currUserID, $receiverID);
                    if ($isSuccess) {
                        $this->redirect(base_folder_path . "/friends?success=request_sent");
                    } else {
                        $this->redirect(base_folder_path . "/friends?error=failed_to_send_request");
                    }
                } else {
                    $senderID = $currUserID;
                    $isExistingA = $existingA ? 'yes' : 'no';
                    $isExistingB = $existingB ? 'yes' : 'no';
                    $this->redirect(base_folder_path . "/friends?error=you-two-are-already-friends&senderID={$senderID}-receiverID={$receiverID}&isExistingA={$isExistingA}-isExistingB={$isExistingB}");
                }
            }
        }

        /* POST /friends/accept */
        public function acceptFriend(): void {
            $currUserID = (string)($_SESSION["user_ID"] ?? '');
            $senderID = $_POST["sender_id"] ?? '';

            if (!empty($currUserID) && !empty($senderID)) {
                // Updating the exact row where the sender sent it to the current user
                $isSuccess = ($this->friendshipRepo)->acceptRequest($senderID, $currUserID);
                if ($isSuccess) {
                    $this->redirect(base_folder_path . "/friends?success=accepted");
                } else {
                    $this->redirect(base_folder_path . "/friends/error=failed_to_accept_request");
                }
            } else {
                if (empty($currUserID)) {
                    $this->redirect(base_folder_path . "/friends?error=Please login to use the software");
                }
                if (empty($senderID)) {
                    $this->redirect(base_folder_path . "/friends?error=sender_ID_not_found");
                }
            }
        }

        /* POST /friends/decline */
        public function declineFriend(): void {
            $currUserID = (string)($_SESSION["user_ID"] ?? '');
            $senderID = (string)$_POST["sender_id"];

            if (!empty($currUserID) && !empty($senderID)) {
                // Updating the exact row where the sender sent it to the current user
                $isSuccess = ($this->friendshipRepo)->rejectRequest($senderID, $currUserID);
                if ($isSuccess) {
                    $this->redirect(base_folder_path . "/friends?success=declined");
                } else {
                    $this->redirect(base_folder_path . "/friends/error=failed_to_reject_request");
                }
            } else {
                if (empty($currUserID)) {
                    $this->redirect(base_folder_path . "/friends?error=Please login to use the software");
                }
                if (empty($senderID)) {
                    $this->redirect(base_folder_path . "/friends?error=sender_ID_not_found");
                }
            }
        }

        /* GET /friends/search */
        public function searchFriend(): void {
            $currUserID = (string)($_SESSION["user_ID"] ?? '');
            if (empty($currUserID)) $this->redirect(base_folder_path . "/login");

            $targetName = trim($_GET["name"] ?? '');
            $targetID = trim($_GET["id"] ?? '');

            // If user submit an empty search, redirect to default index
            if (empty($targetName) && empty($targetID)) {
                $this->redirect(base_folder_path . "/friends");
            }

            $matchedPeople = [];

            if (!empty($targetID)) {
                $studentData = ($this->studentRepo)->findByID($targetID);
                
                if ($studentData !== null) {
                    $matchedPeople = [$studentData];
                } else {
                    $this->redirect(base_folder_path . "/friends?error=no_matched_input_ID");
                }
            }

            if (!empty($targetName)) {
                $foundPeople = ($this->studentRepo)->findByName($targetName, $currUserID);
                if (!empty($foundPeople)) {
                    $matchedPeople = $foundPeople;
                } else {
                    $this->redirect(base_folder_path . "/friends?error=no_matched_input_name");
                }
            }

            $displayUsers = [];
            if (!empty($matchedPeople)) {
                $displayUsers = ($this->recommendationService)->evaluateSearchedUsers($currUserID, $matchedPeople);
                // echo "<div>displayUsers from evaluateSearchedUsers:</div>";
                // var_dump($displayUsers);
            }
            
            // Fetch pending requests again so the UI doesn't crash when rendering the top section
            $pendingRequests = ($this->friendshipRepo)->getAllPendingRequestsFromUserID($currUserID);
            $storage = [];
            foreach ($pendingRequests as $request) {
                $senderID = $request->getFromID();
                $senderData = ($this->studentRepo)->findByID($senderID);
                if ($senderData) {
                    $storage[] = [
                        "ID"        => $senderData->getID(),
                        "firstname" => $senderData->getFirstname(),
                        "lastname"  => $senderData->getLastname() 
                    ];
                }
            }

            $this->render("friends/index", [
                "pendingRequests" => $storage,
                "displayUsers"    => $displayUsers,
                "isSearching"     => true
            ]);

        }

        /* POST: /friends/unfriend */
        public function unfriend(): void {
            $currUserID = (string)($_SESSION["user_ID"] ?? '');
            $friendID = (string)($_POST["friend_ID"] ?? '');

            $fromID = ($currUserID < $friendID) ? $currUserID : $friendID;
            $toID = ($currUserID > $friendID) ? $currUserID : $friendID;

            if (empty($currUserID) || empty($friendID)) {
                $this->redirect(base_folder_path . "/friends?error=missing-data");
            }

            // Check both directions since either person could have sent the original request
            $existing = ($this->friendshipRepo)->findRelationship($fromID, $toID);
            if ($existing === null) {
                $this->redirect(base_folder_path . "/friends?error=no-friendship-found");
            }

            $isDeleted = ($this->friendshipRepo)->deleteFriendship($currUserID, $friendID);
            if ($isDeleted) {
                $this->redirect(base_folder_path . "/friends?success=unfriend");
            } else {
                $this->redirect(base_folder_path . "/friends?error=failed-to-unfriend");
            }
        }

        /* POST: /friendship/withdraw */
        public function withdrawRequest(): void {
            $currUserID = (string)($_SESSION["user_ID"] ?? '');
            $receiverID = (string)($_POST["receiver_ID"] ?? "");

            if (empty($currUserID) || empty($receiverID)) {
                $this->redirect(base_folder_path . "/friends?error=missing-data&currUserID={$currUserID}-receiverID={$receiverID}");
            }

            // The current user is always the sender here, so direction is known
            $existing = ($this->friendshipRepo)->findRelationship($currUserID, $receiverID);
            if ($existing === null) {
                $this->redirect(base_folder_path . "/friends?error=no-sent-request-found");
            }

            $isDeleted = ($this->friendshipRepo)->deleteFriendship($currUserID, $receiverID);
            if ($isDeleted) {
                $this->redirect(base_folder_path . "/friends?success=request-withdrawn");
            } else {
                $this->redirect(base_folder_path . "/friends?error=failed-to-withdraw");
            }
        }

        /* GET: testing API for getting recommendation */
        public function friendRecommendedTestingAPI(): void {
            $currID = trim($_GET["curr_ID"] ?? '');
            echo "<div>Current ID: {$currID}</div>";
            // Fetch graph recommendation
            $displayUsers = ($this->recommendationService)->getRecommendations($currID);
            

            $this->json(
                $displayUsers
            );
        }


    }
?>
<?php
    require_once root_dir . "/config/database-config.php";
    require_once root_dir . "/models/base.php";
    require_once root_dir . "/models/membership.php";
    require_once root_dir . "/models/profile.php";
    require_once root_dir . "/models/student.php";

    enum FriendshipStatus: string {
        case ACCEPT = "accepted";
        case REJECT = "rejected";
        case BLOCK = "blocked";
        case PENDING = "pending";
    };

    class Friendship extends BaseModel {
        private ?int $ID;
        private string $fromUserID;
        private string $toUserID;
        private string $senderID;
        private FriendshipStatus $status;
        private DateTime $createdAt;

        public function __construct(string $fID, string $tID, string $sID, DateTime $cAt, ?FriendshipStatus $s = FriendshipStatus::PENDING, ?int $id = null) {
            $this->setID($id);
            $this->setFromID($fID);
            $this->setToID($tID);
            $this->setSenderID($sID);
            $this->setCreateAt($cAt);
            $this->setStatus($s);
        }

        private function setID(?int $id): void {
            $this->ID = $id;
        }
        private function setFromID(string $id): void {
            $this->fromUserID = $id;
        }
        private function setToID(string $id): void {
            $this->toUserID = $id;
        }
        private function setSenderID(string $id): void {
            $this->senderID = $id;
        }
        private function setCreateAt(DateTime $x): void {
            $this->createdAt = $x;
        }
        private function setStatus(FriendshipStatus $s): void {
            $this->status = $s;
        }

        public function getID(): int {return $this->ID;}
        public function getFromID(): string {return $this->fromUserID;}
        public function getToID(): string {return $this->toUserID;}
        public function getSenderID(): string {return $this->senderID;}
        public function getStatus(): FriendshipStatus {return $this->status;}
        public function getCreatedAt(): Datetime {return $this->createdAt;}
    }

    class FriendshipRepository extends BaseRepository {
        public function __construct() {
            parent::__construct("friendship");
        }

        public function findRelationship(string $fromID, string $toID): ?Friendship {
            // Always store the smaller ID in from_user_ID column and bigger ID in to_user_ID column to ensure uniqueness constraint
            $fID = ($fromID < $toID) ? $fromID : $toID;
            $tID = ($fromID > $toID) ? $fromID : $toID;
            $data = $this->findViaCriteria(
                [
                    "from_user_ID"  => $fID,
                    "to_user_ID"    => $tID
                ]
            );
            if (empty($data)) return null;
            return $this->hydrate($data[0]);
        }

        #[Override]
        public function hydrate(array $row): Friendship
        {
            if (empty($row)) throw new RuntimeException("Empty row!");
            try {
                $cAt = new DateTime($row["created_at"]);
            } catch (PDOException $ex) {
                throw new RuntimeException("Invalid datetime!");
            }
            
            $friendship = new Friendship(
                (string)$row["from_user_ID"],
                (string)$row["to_user_ID"],
                (string)$row["sender_ID"],
                $cAt,
                FriendshipStatus::from($row["status"]),
                (int)$row["ID"]
            );
            return $friendship;
        }

        public function sendRequest(string $senderID, string $receiverID): ?Friendship {
            // Always store the smaller ID in from_user_ID column to ensure uniqueness constraint
            $fID = ($senderID < $receiverID) ? $senderID : $receiverID;
            $tID = ($senderID > $receiverID) ? $senderID : $receiverID;
            // echo "<div>{$fID}</div>";
            // echo "<div>{$tID}</div>";
            $now = new DateTime("now");
            
            $friendship = new Friendship($fID, $tID, $senderID, $now);
            var_dump($friendship);

            $isSuccess =  $this->add([
                "from_user_ID"  => $friendship->getFromID(),
                "to_user_ID"    => $friendship->getToID(),
                "sender_ID"     => $friendship->getSenderID(),
                "status"        => ($friendship->getStatus())->value,
                "created_at"    => ($friendship->getCreatedAt())->format("Y-m-d"),
                "sender_ID"     => $senderID
            ]);

            if ($isSuccess) {
                $generatedID = $this->getLatestID();
                return new Friendship(
                    $friendship->getFromID(),
                    $friendship->getToID(),
                    $friendship->getSenderID(),
                    $friendship->getCreatedAt(),
                    $friendship->getStatus(),
                    $generatedID
                );
            } else {
                return null;
            }
        }

        // Accepting a friend request
        public function acceptRequest(string $sID, string $tID): bool {
            $fID = ($sID < $tID) ? $sID : $tID;
            $tID = ($sID > $tID) ? $sID : $tID;

            return $this->updateViaCriteria([
                "status" => (FriendshipStatus::ACCEPT)->value
            ], [
                "from_user_ID"  => $fID,
                "to_user_ID"    => $tID
            ]);
        }

        public function rejectRequest(string $sID, string $tID): bool {
            $fID = ($sID < $tID) ? $sID : $tID;
            $tID = ($sID > $tID) ? $sID : $tID;
            return $this->updateViaCriteria(
                ["status" => (FriendshipStatus::REJECT)->value],
                [
                    "from_user_ID"  => $fID,
                    "to_user_ID"    => $tID
                ]
            );
        }

        // Fetching all accepted friendships to load into the graph
        public function findAllAcceptedFriendships(): array {
            $data =  $this->findViaCriteria(["status" => (FriendshipStatus::ACCEPT)->value]);
            if (empty($data)) return [];
            return array_map(
                fn ($row) => $this->hydrate($row), $data
            );
        }

        public function getAllPendingRequestsFromUserID(string $id): array {
            // Because the sender is the one who will see the request under 'pending' status and be able to accept or reject it.
            // On the opposite, the sender will see the request under 'sent a request' status and be able to withdraw the request.
            $stmt = ($this->dbConnection)->prepare("
                select
                    *
                from {$this->tableName}
                where (
                    from_user_ID = :fID
                    or to_user_ID = :tID
                ) and (
                    sender_ID != :sID
                ) and (
                    status = :s
                )
            ");
            $params = [
                ":fID"  => $id,
                ":tID"  => $id,
                ":sID"  => $id,
                ":s"    => (FriendshipStatus::PENDING)->value   
            ];
            $stmt->execute($params);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($data)) return [];
            return array_map(
                fn ($row) => $this->hydrate($row), $data
            );
        }

        public function getAllSentRequestFromUserID(string $id): array {
            $data = $this->findViaCriteria(
                [
                    "sender_ID" => $id,
                    "status"    => (FriendshipStatus::PENDING)->value
                ]
            );
            if (empty($data)) return [];
            return array_map(
                fn ($row)   => $this->hydrate($row), $data
            );
        }

        public function getAllAcceptedRequestsFromUserID(string $id): array {
            $stmt = ($this->dbConnection)->prepare("
                select
                    *
                from {$this->tableName}
                where status = :s
                and (
                    from_user_ID = :id1
                    or to_user_ID = :id2
                )
            ");
            $params = [
                ":s"    => (FriendshipStatus::ACCEPT)->value,
                ":id1"  => $id,
                ":id2"  => $id
            ];
            $stmt->execute($params);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($data)) {
                return array_map(
                    fn ($row) => $this->hydrate($row), $data
                );
            } else {
                return [];
            }
        }

        public function getAllFriendsFromUserID(string $id): array {
            $stmt = ($this->dbConnection)->prepare("
                select
                    *
                from {$this->tableName}
                where (
                    from_user_ID = :fID
                    or to_user_ID = :tID
                ) and (
                    status = :s
                )
            ");
            $params = [
                ":fID"  => $id,
                ":tID"  => $id,
                ":s"    => (FriendshipStatus::ACCEPT)->value   
            ];
            $stmt->execute($params);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($data)) return [];
            return array_map(
                fn ($row) => $this->hydrate($row), $data
            );
        }
    }

    class SocialGraph {
        private array $adjacencyList = [];

        /*
            Building the social network from rows fetched out of the database 
        */
        public function buildGraph(array $friendships): void {
            foreach ($friendships as $friendship) {
                $fromUserID = $friendship->getFromID();
                $toUserID = $friendship->getToID();

                // Initializing node arrays if they don't exist yet
                if (!isset($this->adjacencyList[$fromUserID])) $this->adjacencyList[$fromUserID] = [];
                if (!isset($this->adjacencyList[$toUserID])) $this->adjacencyList[$toUserID] = [];

                // Adding the undirected bidirectional edge
                $this->adjacencyList[$fromUserID][] = $toUserID;
                $this->adjacencyList[$toUserID][] = $fromUserID;
            }
        }

        /* Running a 2-step BFS tranversal to discover friends-of-friends */
        public function getFriendsOfFriends(string $currUserID): array {
            // If the user has no friend at all, they ain't in the graph yet
            if (!isset($this->adjacencyList[$currUserID])) return [];

            $directFriends = $this->adjacencyList[$currUserID];
            $candidates = [];

            // Tranversing to neighbors (degree 1)
            foreach ($directFriends as $friendID) {
                if (!isset($this->adjacencyList[$friendID])) continue;

                // Tranversing to neighbor's neighbors (degree 2)
                foreach ($this->adjacencyList[$friendID] as $friendOfFriendID) {
                    /*
                        Rules to avert invalid recommendation:
                            - Can't recommend yourself
                            - Can't recommend someone who is already your direct friend 
                    */
                    if ($friendOfFriendID === $currUserID) continue;
                    if (in_array($friendOfFriendID, $directFriends)) continue;

                    // If valid, increment their mutual friend index
                    if (!isset($candidates[$friendOfFriendID])) {
                        $candidates[$friendOfFriendID] = 0;
                    }
                    $candidates[$friendOfFriendID]++;
                }
            }
            return $candidates;     // format: ["22071044" => 5 mutual friends]
        }
    }

    class FriendshipRecommendationService {
        private FriendshipRepository $friendshipRepo;
        private ProfileRepository $profileRepo;
        private MembershipRepository $membershipRepo;
        private StudentRepository $studentRepo;

        public function __construct() {
            $this->friendshipRepo = new FriendshipRepository();
            $this->studentRepo = new StudentRepository();
            $this->profileRepo = new ProfileRepository();
            $this->membershipRepo = new MembershipRepository();
        }

        public function getRecommendations(string $currUserID): array {
            $currUserFriends = ($this->friendshipRepo)->getAllAcceptedRequestsFromUserID($currUserID);
            if (empty($currUserFriends) || count($currUserFriends) < 10) {
                return $this->getColdStartRecommendations($currUserID);
            }

            // Initializing the building the graph network
            $graph = new SocialGraph();
            $graph->buildGraph($currUserFriends);

            // Getting friends-of-friends list via graph tranversal
            $candidates = $graph->getFriendsOfFriends($currUserID);
            // Fetching current user metadata for scoring comparisions
            $currUserProfile = ($this->profileRepo)->findByStudentID($currUserID);
            $currUserClubs = ($this->membershipRepo)->getAllJoinedClubIDsViaStudentID($currUserID);

            $scoredRecommendations = [];

            // Calculating recommendation weights
            foreach ($candidates as $candidateID => $mutualCount) {
                $score = 0;
                // Rule A: network weight (+10pts per mutual friend)
                $score += ($mutualCount * 10);

                // Rule B: common academic background (+15pts if sharing the same major)
                $candidateProfile = ($this->profileRepo)->findByID($candidateID);
                if ($currUserProfile && $candidateProfile) {
                    if ($currUserProfile->getMajor() === $candidateProfile->getMajor()) {
                        $score += 15;
                    }
                }

                // Rule C: social circles (+12pts per shared student club)
                $candidateClubs = ($this->membershipRepo)->getAllJoinedClubIDsViaStudentID($candidateID);
                $sharedClubs = array_intersect($currUserClubs, $candidateClubs);
                $score += (count($sharedClubs) * 12);

                // Populating recommendations with hydrated student objects or basic information arrays
                $candidateData = ($this->studentRepo)->findByID($candidateID);
                if ($candidateData) {
                    $scoredRecommendations[] = [
                        "student"       => $candidateData,
                        "score"         => $score,
                        "mutual_count"  => $mutualCount,
                        "shared_clubs"  => count($sharedClubs)
                    ];
                }
            }

            // Sorting recommendations so the highest score come first
            usort($scoredRecommendations, function ($a, $b) {
                return $b["score"] <=> $a["score"];
            });
            return $scoredRecommendations;
        }

        public function evaluateSearchedUsers(string $currUserID, array $searchedUsers): array {
            $currUserProfile = ($this->profileRepo)->findByStudentID($currUserID);
            $currUserClubs = ($this->membershipRepo)->getAllJoinedClubIDsViaStudentID($currUserID);

            // Fetching all accpeted friendships to find mutual connections manually
            $myFriends = ($this->friendshipRepo)->getAllFriendsFromUserID($currUserID);
            // var_dump($myFriends);
            $results = [];
            
            foreach ($searchedUsers as $user) {
                $candidateID = (string)$user->getID();
                if ($candidateID === $currUserID) continue;     // Don't show yourself in search results

                // Calculating mutual friends
                $theirFriendsID = [];
                $myFriendsID = [];

                foreach ($myFriends as $f) {
                    if ($f->getFromID() !== $currUserID) $myFriendsID[] = $f->getFromID();
                    if ($f->getToID() !== $currUserID) $myFriendsID[] = $f->getToID();
                }
        
                foreach ($myFriends as $f) {
                    if ($f->getFromID() === $candidateID) $theirFriendsID[] = $f->getFromID();
                    if ($f->getToID() === $candidateID) $theirFriendsID[] = $f->getToID();
                }
                // echo "<br/><br/>";
                // var_dump($theirFriends);
                
                $mutualCount = count(array_intersect($myFriendsID, $theirFriendsID));

                // Checking for shared major
                $candidateProfile = ($this->profileRepo)->findByStudentID($candidateID);
                $sameMajor = false;
                if ($currUserProfile && $candidateProfile) {
                    $currUserMajor = $currUserProfile->getMajor();
                    // echo "<div>Current user major: {$currUserMajor}</div>";
                    if ($currUserProfile->getMajor() === $candidateProfile->getMajor()) {
                        $sameMajor = true;
                    }
                }

                // Checking shared clubs
                $candidateClubs = ($this->membershipRepo)->getAllJoinedClubIDsViaStudentID($candidateID);
                $sharedClubs = array_intersect($currUserClubs, $candidateClubs);

                // Fetching the relationship to see if they are already friends or just in `pending` status
                $friendship = ($this->friendshipRepo)->findRelationship($currUserID, $candidateID);
                $isFriend = false;
                $friendshipStatus = null;
                $senderID = null;

                if ($friendship !== null) {
                    $friendshipStatus = $friendship->getStatus();
                    $isFriend = $friendshipStatus === FriendshipStatus::ACCEPT;
                    $senderID = $friendship->getSenderID();
                }

                $results[] = [
                    "student"           => $user,
                    "profile"           => $candidateProfile,
                    "friendship"        => $friendship,
                    "mutual_count"      => $mutualCount,
                    "shared_clubs"      => count($sharedClubs),
                    "same_major"        => $sameMajor,
                    "is_friend"         => $isFriend,
                    "friendship_status" => $friendshipStatus ? $friendshipStatus->value : '',
                    "sender_ID"         => $senderID
                ];
            }

            return $results;
        }

        private function getColdStartRecommendations(string $currUserID): array {
            $results = [];

            // Getting the current user's profile to know their major
            $currUserProfile = ($this->profileRepo)->findByStudentID($currUserID);
            if ($currUserProfile) {
                $userMajor = $currUserProfile->getMajor();

                // Fetching a list of students who share the same major
                $peersInMajor = ($this->profileRepo)->filterByMajor($userMajor, $currUserID);

                foreach ($peersInMajor as $peerProfile) {
                    $studentData = ($this->studentRepo)->findByID($peerProfile->getStudentID());
                    $friendship = ($this->friendshipRepo)->findRelationship($currUserID, $studentData->getID());
                    // var_dump($friendship);
                    $isFriend = false;
                    $friendshipStatus = null;
                    $senderID = null;
                    if ($friendship !== null) {
                        $friendshipStatus = $friendship->getStatus();
                        $isFriend = $friendshipStatus === FriendshipStatus::ACCEPT;
                        $senderID = $friendship->getSenderID();
                    }
                    if ($studentData) {
                        $results[] = [
                            "student"           => $studentData,
                            "profile"           => $peerProfile,
                            "friendship"        => $friendship,
                            "mutual_count"      => 0,
                            "shared_clubs"      => 0,
                            "same_major"        => true,
                            "is_friend"         => $isFriend,
                            "friendship_status" => $friendshipStatus ? $friendshipStatus->value : '',
                            "sender_ID"         => $senderID
                        ];
                    }
                }
            }
            return $results;
        }

    }
?>
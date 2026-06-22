<?php
    // Starting the session to track logged-in users globally
    session_start();

    // Front router
    define("root_dir", dirname(__DIR__));

    // CORS config
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");

    require_once root_dir . "/config/env-config.php";

    $requestURI = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $requestMethod = $_SERVER["REQUEST_METHOD"];

    $envLoader = new EnvLoader(root_dir . "/config/.env");

    $folderName = $envLoader->get("PROJECT_FOLDER_NAME");
    $baseFolderPath = "/{$folderName}/infrastructure";
    define("base_folder_path", $baseFolderPath);

    // Strip the base folder from the URI
    if (str_starts_with($requestURI, $baseFolderPath)) {
        $requestURI = substr($requestURI, strlen($baseFolderPath));
    }

    // Strip "/public" in case the internal Apache redirect leaks it into the URI
    if (str_starts_with($requestURI, "/public")) {
        $requestURI = substr($requestURI, 7);
    }

    // If the URI is completely empty after stripping, default it to the homepage
    if ($requestURI === "") {
        $requestURI = "/";
    }

    // echo "<div>Current request URI: {$requestURI}</div>";

    // Route registry [Method][Path] -> [ControllerClass, ControllerAction]
    $routes = [
        "GET" => [
            "/"                         => ["ClubController", "index"],
            "/clubs"                    => ["ClubController", "index"],
            "/clubs/create"             => ["ClubController", "showCreateForm"],
            "/club/members"             => ["MembershipController", "clubMembers"],
            "/clubs/show"               => ["ClubController", "show"],
            "/clubs/my-clubs"           => ["ClubController", "myClubs"],
            "/clubs/admin-stats"        => ["ClubController", "adminStats"],
            "/events"                   => ["EventController", "index"],
            "/events/comments"          => ["EventController", "getEventComments"],
            "/announcements"            => ["AnnouncementController", "index"],
            "/locations"                => ["LocationController", "index"],
            "/student/memberships"      => ["MembershipController", "studentMemberships"],
            "/login"                    => ["StudentController", "showAuthPage"],
            "/signout"                  => ["StudentController", "signout"],
            "/profile"                  => ["StudentController", "showProfile"],
            "/admin/create/club"        => ["ClubController", "showCreateForm"],
            "/memberships/all-members"  => ["MembershipController", "getMembersJson"],
            "/feedbacks/chat-history"   => ["FeedbackController", "chatHistory"],
            "/friends"                  => ["FriendshipController", "index"],
            "/friends/search"           => ["FriendshipController", "searchFriend"],
            "/friends/recommended-service-testing"  => ["FriendshipController", "friendRecommendedTestingAPI"]
        ],

        "POST" => [
            "/clubs/create"             => ["ClubController", "store"],
            "/clubs/register"           => ["ClubController", "register"],
            "/clubs/process-request"    => ["ClubController", "processJoiningRequest"],
            "/clubs/member-kick"        => ["ClubController", "kickMember"],
            "/events/register"          => ["EventController", "registerForEvent"],
            "/events/create"            => ["EventController", "store"],
            "/events/comments"          => ["EventController", "postEventComment"],
            "/announcements/create"     => ["AnnouncementController", "store"],
            "/membership/apply"         => ["MembershipController", "apply"],
            "/membership/update"        => ["MembershipController", "updateStatus"],
            "/membership/join"          => ["MembershipController", "join"],
            "/attendance/checkin"       => ["AttendanceController", "checkIn"],
            "/locations/create"         => ["LocationController", "store"],
            "/auth/login"               => ["StudentController", "login"],
            "/auth/signup"              => ["StudentController", "register"],
            "/admin/create/club"        => ["ClubController", "store"],
            "/feedbacks/submit"         => ["FeedbackController", "store"],
            "/friends/add"              => ["FriendshipController", "addFriend"],
            "/friends/accept"           => ["FriendshipController", "acceptFriend"],
            "/friends/decline"          => ["FriendshipController", "declineFriend"]
        ]
    ];

    $handle404 = function (string $message = "Page Not Found"): void {
        http_response_code(404);
        echo "<div>
                <h1 style='text-color: crimson;'>404 Not Found!</h1>
            </div>";
        echo "<div>" . "<p>" . htmlspecialchars($message) . "</p></div>";
        exit;
    };

    if ($requestMethod === "OPTIONS") {
        http_response_code(200);
        exit();
    }


    // Check if the requested route exists
    if (isset($routes[$requestMethod][$requestURI])) {
        $target = $routes[$requestMethod][$requestURI];
        $controllerName = $target[0];
        $actionName = $target[1];

        $controllerFile = root_dir . "/app/controllers/{$controllerName}.php";
        // echo "<div>Trying to find file at: {$controllerFile}</div>";

        if (file_exists($controllerFile)) {
            require_once $controllerFile;

            $controllerInstance = new $controllerName();
            $controllerInstance->$actionName();
        } else {
            $handle404("Controller file missing: {$controllerName}.php!");
        }
    } else {
        $handle404("Route not found for path: {$requestURI}");
    }
?>
<?php
    // Front router
    define("root_dir", dirname(__DIR__));

    $requestURI = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $requestMethod = $_SERVER["REQUEST_METHOD"];

    // Route registry [Method][Path] -> [ControllerClass, ControllerAction]
    $routes = [
        "GET" => [
            "/"                         => ["ClubController", "index"],
            "/clubs"                    => ["ClubController", "index"],
            "/clubs/create"             => ["ClubController", "showCreateForm"],
            "club/members"              => ["MembershipController", "clubMembers"],
            "/events"                   => ["EventController", "index"],
            "/announcements"            => ["AnnouncementController", "index"],
            "/locations"                => ["LocationController", "index"],
            "/student/memberships"      => ["MembershipController", "studentMemberships"]
        ],

        "POST" => [
            "/clubs/create"             => ["ClubController", "store"],
            "/events/register"          => ["EventController", "registerForEvent"],
            "/announcements/create"     => ["AnnouncementController", "store"],
            "/membership/apply"         => ["MembershipController", "apply"],
            "/membership/update"        => ["MembershipController", "updateStatus"],
            "/membership/join"          => ["MembershipController", "join"],
            "/attendance/checkin"       => ["AttendanceController", "checkIn"],
            "/feedback/submit"          => ["FeedbackController", "store"],
            "/locations/create"         => ["LocationController", "store"]
            
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


    // Check if the requested route exists
    if (isset($routes[$requestMethod][$requestURI])) {
        $target = $routes[$requestMethod][$requestURI];
        $controllerName = $target[0];
        $actionName = $target[1];

        $controllerFile = root_dir . "/controllers/{$controllerName}.php";

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
<?php
    // Front router
    define("root_dir", dirname(__DIR__));

    // CORS config
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");

    $requestURI = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $requestMethod = $_SERVER["REQUEST_METHOD"];

    $folderName = "final-project";
    $baseFolder = "/{$folderName}/infrastructure";

    // Strip the base folder from the URI
    if (str_starts_with($requestURI, $baseFolder)) {
        $requestURI = substr($requestURI, strlen($baseFolder));
    }

    // Strip "/public" in case the internal Apache redirect leaks it into the URI
    if (str_starts_with($requestURI, "/public")) {
        $requestURI = substr($requestURI, 7);
    }

    // If the URI is completely empty after stripping, default it to the homepage
    if ($requestURI === "") {
        $requestURI = "/";
    }

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
        echo "<div>Trying to find file at: {$controllerFile}</div>";

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
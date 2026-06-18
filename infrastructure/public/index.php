<?php
    // Starting the session to track logged-in users globally
    session_start();

    // Front router
    define("root_dir", dirname(__DIR__));

    // Load env sớm để đọc cấu hình folder từ .env
    require_once root_dir . "/config/env-config.php";

    // CORS config
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
    $allowed_origins = [
        "http://localhost",
        "http://127.0.0.1",
        "http://localhost:8080"
    ];
    if (in_array($origin, $allowed_origins)) {
        header("Access-Control-Allow-Origin: $origin");
    }
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");

    $requestURI = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $requestMethod = $_SERVER["REQUEST_METHOD"];

    // Đọc tên folder từ .env thay vì hard-code
    $folderName = getProjectFolderName();
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
            "/club/members"             => ["MembershipController", "clubMembers"],
            "/events"                   => ["EventController", "index"],
            "/announcements"            => ["AnnouncementController", "index"],
            "/locations"                => ["LocationController", "index"],
            "/student/memberships"      => ["MembershipController", "studentMemberships"],
            "/login"                    => ["StudentController", "showAuthPage"]
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
            "/locations/create"         => ["LocationController", "store"],
            "/signout"                  => ["StudentController", "signout"],
            "/auth/login"               => ["StudentController", "login"],
            "/auth/signup"              => ["StudentController", "register"]
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
<?php
    class BaseController {
        // protected function view(string $view, array $data = []): void {
        //     extract($data);
        //     $content = root_dir . "/app/views/{$view}.php";
        //     require root_dir . "/app/views/layouts/main.php";
        // }

        protected function redirect(string $url): void {
            header("Location: {$url}");
            exit;
        }

        // Rendering a traditional PHP/HTML template
        protected function render(string $view, array $data = []): void {
            extract($data);
            $viewPath = root_dir . "/views/{$view}.php";

            if (file_exists($viewPath)) {
                require_once $viewPath;
            } else {
                throw new RuntimeException("View file not found: {$view}");
            }
        }

        // Sending a JSON response (ideal for APIs or AJAX requests)
        protected function json(mixed $data, int $statusCode = 200): void {
            header("Content-type: application/json");
            http_response_code($statusCode);
            echo json_encode($data);
            exit;
        }
    }
?>
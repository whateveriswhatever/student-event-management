<?php
    class BaseController {

        // ────────────────────────────────────────────────
        // AUTH
        // ────────────────────────────────────────────────

        /**
         * Bắt buộc người dùng phải đăng nhập.
         * Nếu chưa có session → redirect về trang login và dừng lại.
         */
        protected function requireAuth(): void {
            if (!isset($_SESSION["user_ID"]) || empty($_SESSION["user_ID"])) {
                $this->redirect("/final-project/infrastructure/login");
            }
        }

        /** Lấy ID của người dùng đang đăng nhập từ session */
        protected function getCurrentUserID(): ?string {
            return $_SESSION["user_ID"] ?? null;
        }

        /** Lấy tên của người dùng đang đăng nhập từ session */
        protected function getCurrentUserName(): ?string {
            return $_SESSION["userLastname"] ?? null;
        }

        /** Kiểm tra người dùng đã đăng nhập chưa (không redirect) */
        protected function isAuthenticated(): bool {
            return isset($_SESSION["user_ID"]) && !empty($_SESSION["user_ID"]);
        }

        // ────────────────────────────────────────────────
        // INPUT HELPERS — đọc request data an toàn
        // ────────────────────────────────────────────────

        /** Đọc giá trị POST, trim khoảng trắng, trả default nếu không có */
        protected function post(string $key, mixed $default = ""): mixed {
            return isset($_POST[$key]) ? trim((string)$_POST[$key]) : $default;
        }

        /** Đọc giá trị GET, trim khoảng trắng, trả default nếu không có */
        protected function query(string $key, mixed $default = null): mixed {
            return isset($_GET[$key]) ? trim((string)$_GET[$key]) : $default;
        }

        /** Đọc giá trị POST dạng int */
        protected function postInt(string $key, int $default = 0): int {
            return isset($_POST[$key]) ? (int)$_POST[$key] : $default;
        }

        /** Đọc giá trị GET dạng int */
        protected function queryInt(string $key, int $default = 0): int {
            return isset($_GET[$key]) ? (int)$_GET[$key] : $default;
        }

        /**
         * Sanitize string: trim + chuyển ký tự HTML thành entity.
         * Dùng trước khi đưa dữ liệu vào view để tránh XSS.
         */
        protected function sanitize(string $input): string {
            return htmlspecialchars(trim($input), ENT_QUOTES, "UTF-8");
        }

        // ────────────────────────────────────────────────
        // RESPONSE HELPERS
        // ────────────────────────────────────────────────

        /** Redirect tới URL khác */
        protected function redirect(string $url): void {
            header("Location: {$url}");
            exit;
        }

        /** Render PHP/HTML template, truyền data vào view qua extract() */
        protected function render(string $view, array $data = []): void {
            extract($data);
            $viewPath = root_dir . "/app/views/{$view}.php";
            if (file_exists($viewPath)) {
                require_once $viewPath;
            } else {
                throw new RuntimeException("View file not found: {$view}");
            }
        }

        /** Trả về JSON response với HTTP status code */
        protected function json(mixed $data, int $statusCode = 200): void {
            header("Content-Type: application/json; charset=UTF-8");
            http_response_code($statusCode);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            exit;
        }

        /** Trả về JSON lỗi — shorthand cho json([error => ...], 4xx/5xx) */
        protected function jsonError(string $message, int $statusCode = 400): void {
            $this->json(["error" => $message], $statusCode);
        }

        /** Trả về JSON thành công */
        protected function jsonSuccess(string $message, array $extra = []): void {
            $this->json(array_merge(["message" => $message], $extra), 200);
        }
    }
?>
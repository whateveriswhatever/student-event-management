<?php
    function loadEnvFile(string $filePath): void {
		if (!file_exists($filePath)) {
			throw new Exception ("The file at {$filePath} doesn't exist!");
		}
		$lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		foreach ($lines as $line) {
			// Skip comments
			if (strpos(trim($line), '#') === 0) {
				continue;
			}
			// Split into key and value pair 
			if (strpos($line, '=') !== false) {
				list($key, $value) = explode('=', $line, 2);
				$key = trim($key);
				$value = trim($value);

				// Remove surrounding quotes if present
				$value = trim($value, "\"'");

				// Set the environment variable
				putenv("$key=$value");
				$_ENV[$key] = $value;
			}
		}
	}

	loadEnvFile(__DIR__ . "/.env");

	function getProjectFolderName(): string {
		return $_ENV["PROJECT_FOLDER_NAME"];
	}

	class EnvLoader {
		private array $env = [];

		public function __construct(string $filePath) {
			if (!file_exists($filePath)) {
				throw new Exception("ENV file not found at: {$filePath}!");
			}

			$lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

			foreach ($lines as $line) {
				$line = trim($line);
				// Skipping comments
				if ($line === '' || str_starts_with($line, '#')) {
					continue;
				}

				if (strpos($line, '=') !== false) {
					[$key, $value] = explode('=', $line, 2);
					$key = trim($key);
					$value = trim($value);
					$value = trim($value, "\"'");

					$this->env[$key] = $value;
				}
			}
		}

		public function get(string $key, mixed $default = null): mixed {
			return $this->env[$key] ?? $default;
		}

		public function has(string $key): bool {
			return array_key_exists($key, $this->env);
		}
	}
?>
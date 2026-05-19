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

	class DatabaseConfig {
		private static ?DatabaseConfig $instance = null;
		private ?PDO $connection; 
		
		private function __construct() {
			$ip = $_ENV["DB_HOST"];
			$dbName = $_ENV["DB_NAME"];
			$username = $_ENV["DB_USERNAME"];
			$password = $_ENV["DB_PASSWORD"];
			$dsn = "mysql:host={$ip};dbname={$dbName};charset=utf8mb4";
			$options = [
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_EMULATE_PREPARES => false
			];
			$this->connection = new PDO($dsn, $username, $password, $options);
		}
		
		public static function getInstance(): DatabaseConfig {
			if (self::$instance === null) {
				self::$instance = new DatabaseConfig();
			}
			return self::$instance;
		}

		public function getConnection(): PDO {
			return $this->connection;
		}
	}
?>

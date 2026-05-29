<?php
	require_once root_dir . "/config/env-config.php";

	class DatabaseConfig {
		private static ?DatabaseConfig $instance = null;
		private ?PDO $connection; 
		private EnvLoader $env;
		private function __construct() {
			$this->env = new EnvLoader(__DIR__ . "/.env");
			$ip = ($this->env)->get("DB_HOST");
			$dbName = ($this->env)->get("DB_NAME");
			$username = ($this->env)->get("DB_USERNAME");
			$password = ($this->env)->get("DB_PASSWORD");
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

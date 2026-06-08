<?php
    require_once root_dir . "/config/database-config.php";

    abstract class BaseRepository {
        protected string $tableName;
        protected PDO $dbConnection;

        public function __construct(string $table_name) {
            $this->dbConnection = DatabaseConfig::getInstance()->getConnection();
            $this->tableName    = $table_name;
        }

        // ────────────────────────────────────────────────
        // READ
        // ────────────────────────────────────────────────

        /** Lấy toàn bộ bản ghi của bảng */
        public function all(): array {
            try {
                $stmt = $this->dbConnection->prepare("SELECT * FROM {$this->tableName}");
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $ex) {
                error_log("[BaseRepository::all] " . $ex->getMessage());
                throw $ex;
            }
        }

        /** Tìm nhiều bản ghi theo điều kiện AND */
        public function findViaCriteria(array $criteria): array {
            try {
                $keys   = array_keys($criteria);
                $vals   = array_values($criteria);
                $where  = implode(" AND ", array_map(fn($k) => "{$k} = :{$k}", $keys));
                $params = [];
                for ($i = 0; $i < count($keys); $i++) {
                    $params[":{$keys[$i]}"] = $vals[$i];
                }
                $stmt = $this->dbConnection->prepare(
                    "SELECT * FROM {$this->tableName} WHERE {$where}"
                );
                $stmt->execute($params);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $ex) {
                error_log("[BaseRepository::findViaCriteria] " . $ex->getMessage());
                throw $ex;
            }
        }

        /** Tìm đúng 1 bản ghi theo điều kiện — trả null nếu không có */
        public function findOne(array $criteria): ?array {
            $data = $this->findViaCriteria($criteria);
            return empty($data) ? null : $data[0];
        }

        /** Lấy các cột cụ thể của toàn bảng */
        public function getAllFromColumn(array $cols): array {
            try {
                // Tên cột không thể bind bằng prepared statement — dùng whitelist
                $selectCols = implode(', ', $cols);
                $stmt = $this->dbConnection->prepare(
                    "SELECT {$selectCols} FROM {$this->tableName}"
                );
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $ex) {
                error_log("[BaseRepository::getAllFromColumn] " . $ex->getMessage());
                throw $ex;
            }
        }

        // ────────────────────────────────────────────────
        // COUNT / EXISTS
        // ────────────────────────────────────────────────

        /** Đếm số bản ghi — dùng COUNT thay vì fetch toàn bộ rows */
        public function count(array $criteria = []): int {
            try {
                if (empty($criteria)) {
                    $stmt = $this->dbConnection->prepare(
                        "SELECT COUNT(*) AS total FROM {$this->tableName}"
                    );
                    $stmt->execute();
                } else {
                    $keys   = array_keys($criteria);
                    $vals   = array_values($criteria);
                    $where  = implode(" AND ", array_map(fn($k) => "{$k} = :{$k}", $keys));
                    $params = [];
                    for ($i = 0; $i < count($keys); $i++) {
                        $params[":{$keys[$i]}"] = $vals[$i];
                    }
                    $stmt = $this->dbConnection->prepare(
                        "SELECT COUNT(*) AS total FROM {$this->tableName} WHERE {$where}"
                    );
                    $stmt->execute($params);
                }
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return (int)$row["total"];
            } catch (PDOException $ex) {
                error_log("[BaseRepository::count] " . $ex->getMessage());
                throw $ex;
            }
        }

        /** Kiểm tra bản ghi tồn tại theo điều kiện */
        public function exists(array $criteria): bool {
            return $this->count($criteria) > 0;
        }

        /** Kiểm tra bản ghi tồn tại theo ID (auto-increment) */
        public function isExist(int $id): bool {
            return $this->count(["ID" => $id]) > 0;
        }

        // ────────────────────────────────────────────────
        // WRITE
        // ────────────────────────────────────────────────

        /** Thêm bản ghi mới */
        public function add(array $data): bool {
            try {
                $cols        = array_keys($data);
                $vals        = array_values($data);
                $insertedCols = implode(", ", $cols);
                $bindParams  = implode(", ", array_map(fn($c) => ":{$c}", $cols));
                $params = [];
                for ($i = 0; $i < count($cols); $i++) {
                    $params[":{$cols[$i]}"] = $vals[$i];
                }
                $stmt = $this->dbConnection->prepare(
                    "INSERT INTO {$this->tableName} ({$insertedCols}) VALUES ({$bindParams})"
                );
                return $stmt->execute($params);
            } catch (PDOException $ex) {
                error_log("[BaseRepository::add] " . $ex->getMessage());
                throw $ex;
            }
        }

        /** Cập nhật bản ghi theo điều kiện */
        public function updateViaCriteria(array $updatedData, array $criteria): bool {
            try {
                $newDataKeys = array_keys($updatedData);
                $newDataVals = array_values($updatedData);
                $whereKeys   = array_keys($criteria);
                $whereVals   = array_values($criteria);

                $setStmt = implode(", ", array_map(fn($k) => "{$k} = :set_{$k}", $newDataKeys));
                $where   = implode(" AND ", array_map(fn($k) => "{$k} = :wh_{$k}", $whereKeys));

                // Dùng prefix set_ / wh_ để tránh xung đột key khi cùng tên cột
                $params = [];
                for ($j = 0; $j < count($newDataKeys); $j++) {
                    $params[":set_{$newDataKeys[$j]}"] = $newDataVals[$j];
                }
                for ($i = 0; $i < count($whereKeys); $i++) {
                    $params[":wh_{$whereKeys[$i]}"] = $whereVals[$i];
                }

                $stmt = $this->dbConnection->prepare(
                    "UPDATE {$this->tableName} SET {$setStmt} WHERE {$where}"
                );
                return $stmt->execute($params);
            } catch (PDOException $ex) {
                error_log("[BaseRepository::updateViaCriteria] " . $ex->getMessage());
                throw $ex;
            }
        }

        /** Xóa bản ghi theo điều kiện */
        public function deleteViaCriteria(array $criteria): bool {
            try {
                $keys   = array_keys($criteria);
                $vals   = array_values($criteria);
                $where  = implode(" AND ", array_map(fn($k) => "{$k} = :{$k}", $keys));
                $params = [];
                for ($i = 0; $i < count($keys); $i++) {
                    $params[":{$keys[$i]}"] = $vals[$i];
                }
                $stmt = $this->dbConnection->prepare(
                    "DELETE FROM {$this->tableName} WHERE {$where}"
                );
                return $stmt->execute($params);
            } catch (PDOException $ex) {
                error_log("[BaseRepository::deleteViaCriteria] " . $ex->getMessage());
                throw $ex;
            }
        }

        // ────────────────────────────────────────────────
        // TRANSACTION
        // ────────────────────────────────────────────────

        /** Bắt đầu transaction — dùng khi cần nhiều thao tác DB là một khối nguyên tử */
        public function beginTransaction(): void {
            $this->dbConnection->beginTransaction();
        }

        public function commit(): void {
            $this->dbConnection->commit();
        }

        public function rollback(): void {
            $this->dbConnection->rollBack();
        }

        // ────────────────────────────────────────────────
        // HELPERS
        // ────────────────────────────────────────────────

        public function getLatestID(): int {
            return (int)$this->dbConnection->lastInsertId();
        }

        public function getTableName(): string {
            return $this->tableName;
        }

        public function setTableName(string $name): void {
            $this->tableName = $name;
        }

        /** Subclass bắt buộc implement để map DB row → Entity object */
        abstract protected function hydrate(array $row): object;
    }

    // ────────────────────────────────────────────────────────────
    // BaseModel — validation helpers dùng chung cho Entity classes
    // ────────────────────────────────────────────────────────────
    abstract class BaseModel {

        /** Kiểm tra chuỗi có chứa ký tự đặc biệt không (hỗ trợ UTF-8/tiếng Việt) */
        public function doesContainSpecialChars(string $str): bool {
            // \p{L}: chữ cái bất kỳ ngôn ngữ | \p{N}: số | \s: khoảng trắng
            return (bool)preg_match("/[^\p{L}\p{N}\s]/u", $str);
        }

        /** Kiểm tra chuỗi có chứa chữ số không */
        public function doesContainNumber(string $str): bool {
            return (bool)preg_match("/\d/", $str);
        }

        /** Kiểm tra chuỗi có chứa chữ cái không */
        public function doesContainLetter(string $str): bool {
            return (bool)preg_match("/[a-zA-Z]/", $str);
        }

        /** Validate ID dạng auto-increment (phải > 0) */
        public function validateIDForAutoIncrement(int $x): bool {
            return $x > 0;
        }

        /** Set ID có validate — dùng cho các entity có auto-increment PK */
        public function setIDForAutoIncrementType(int &$x, int $y): void {
            if (!$this->validateIDForAutoIncrement($y)) {
                throw new InvalidArgumentException("Invalid ID: must be greater than 0!");
            }
            $x = $y;
        }
    }
?>
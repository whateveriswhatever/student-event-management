<?php
    require_once root_dir . "/config/database-config.php";

    abstract class BaseRepository {
        protected string $tableName;
        protected PDO $dbConnection;

        public function __construct(string $table_name) {
            $this->dbConnection = (DatabaseConfig::getInstance()->getConnection());
            $this->tableName = $table_name;
        }

        public function all(): array {
            try {
                $query = "select * from {$this->tableName}";
                $stmt = ($this->dbConnection)->prepare($query);
                $stmt->execute([]);
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $data;
            } catch (PDOException $ex) {
                error_log($ex->getMessage());
                throw $ex;
            }
        }

        public function add(array $data): bool {
            try {
                $cols = array_keys($data);
                $vals = array_values($data);
                $insertedCols = implode(" , ", array_map(function ($col) {return "{$col}";}, $cols));
                $bindParams = implode(" , ", array_map(function ($col) {return ":{$col}";}, $cols));
                $query = "
                    insert into {$this->tableName} ({$insertedCols})
                    values ({$bindParams})
                ";
                $params = [];
                for ($i = 0; $i < count($cols); $i++) {
                    $params[":{$cols[$i]}"] = $vals[$i];
                }
                $stmt = ($this->dbConnection)->prepare($query);
                $isSuccess = $stmt->execute($params);
                return $isSuccess;
            } catch (PDOException $ex) {
                error_log($ex->getMessage());
                throw $ex;
            }
        } 

        public function findViaCriteria(array $criteria): array {
            try {
                $keys = array_keys($criteria);
                $vals = array_values($criteria);
                $where = implode(" and ", array_map(function ($key) {return "{$key} = :{$key}";}, $keys));
                $params = [];
                for ($i = 0; $i < count($keys); $i++) {
                    $params[":{$keys[$i]}"] = $vals[$i]; 
                }
                $query = "
                    select
                        * 
                    from {$this->tableName}
                    where {$where}";
                $stmt = ($this->dbConnection)->prepare($query);
                $stmt->execute($params);
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $data;
            } catch (PDOException $ex) {
                error_log($ex->getMessage());
                throw $ex;
            }
        }

        public function deleteViaCriteria(array $criteria): bool {
            try {
                $keys = array_keys($criteria);
                $vals = array_values($criteria);
                $where = implode(" and ", array_map(function ($key) {return "{$key} = :{$key}";}, $keys));
                $query = "
                    delete from {$this->tableName}
                    where {$where}
                ";
                $stmt = ($this->dbConnection)->prepare($query);
                $params = [];
                for ($i = 0; $i < count($keys); $i++) {
                    $params[":{$keys[$i]}"] = $vals[$i];
                }
                $isSuccess = $stmt->execute($params);
                if ($isSuccess) {echo "<div>Deleted product successfully</div>";return true;}
                echo "<div>Failed to erase product!</div>";
                return false;

            } catch (PDOException $ex) {
                error_log($ex->getMessage());
                throw $ex;
            }
        }

        public function updateViaCriteria(array $updatedData, array $critera): bool {
            try {
                $newDataKeys = array_keys($updatedData);
                $newDataVals = array_values($updatedData);
                $keys = array_keys($critera);
                $vals = array_values($critera);
                $where = implode(" and ", array_map(function ($key) {return "{$key} = :{$key}";}, $keys));
                $setStmt = implode(" , ", array_map(function ($key) {return "{$key} = :{$key}";}, $newDataKeys));
                $query = "
                    update {$this->tableName}
                    set {$setStmt}
                    where {$where}";
                // echo "<div>{$query}</div>";
                $params = [];
                for ($i = 0; $i < count($keys); $i++) {
                    $params[":{$keys[$i]}"] = $vals[$i];
                }
                for ($j = 0; $j < count($newDataKeys); $j++) {
                    $params[":{$newDataKeys[$j]}"] = $newDataVals[$j];
                }
                // echo "<div>{$query}</div>";
                $stmt = ($this->dbConnection)->prepare($query);
                $isSuccess = $stmt->execute($params);
                if (!$isSuccess) {
                    return false;
                }
                return true;
            } catch (PDOException $ex) {
                error_log($ex->getMessage());
                throw $ex;
            }
        }

        public function getTableName(): string {
            return $this->tableName;
        }

        public function setTableName(string $name): void {
            $this->tableName = $name;
        }

        public function getLatestID(): int {
            $id = $this->dbConnection->lastInsertId();
            return $id;
        }

        public function getAllFromColumn(array $cols): array {
            $bindCols = implode(' , ', array_map(function ($x) {return ":{$x}";}, $cols));
            $query = "select {$bindCols} from {$this->tableName}";
            $params = [];
            for ($i = 0; $i < count($cols); $i++) {
                $params[":{$cols[$i]}"] = $cols[$i];
            }
            $stmt = ($this->dbConnection)->prepare($query);
            $stmt->execute($params);
            $all = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $all;
        }

        abstract protected function hydrate(array $row): object;

        public function isExist(int $id): bool {
            $params = ["ID" => $id];
            $key = array_keys($params);
            $bindKey = ":{$key[0]}";
            $bindParams = [$bindKey => $id];
            $query = "select * from {$this->tableName} where {$key[0]} = {$bindKey}";
            $stmt = ($this->dbConnection)->prepare($query);
            $stmt->execute($bindParams);
            $all = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return count($all) > 0 ? true : false;
        }

        public function findViaCriteriaExceptFor(array $criteria, array $exceptCriteria): array {
            try {
                $cols = array_keys($criteria);
                $vals = array_values($criteria);
                $exceptCols = array_keys($exceptCriteria);
                $exceptVals = array_values($exceptCriteria); 
                $where = implode(" , ", array_map(function ($key) {return "{$key} = :{$key}";}, $cols));
                $exceptWhere = implode(" , ", array_map(function ($key) {return "{$key} != :{$key}";}, $exceptCols));

                $params = [];
                for ($i = 0; $i < count($cols); $i++) {
                    $params[":{$cols[$i]}"] = $vals[$i];
                }
                for ($j = 0; $j < count($exceptCols); $j++) {
                    $params[":{$exceptCols[$j]}"] = $exceptVals[$j];
                }

                $query = "
                    select
                        *
                    from {$this->tableName}
                    where {$where}
                    and {$exceptWhere}
                ";
                $stmt = ($this->dbConnection)->prepare($query);
                $stmt->execute($params);
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $data;
            } catch (PDOException $ex) {
                error_log($ex->getMessage());
                throw $ex;
            }
        }
    }

    abstract class BaseModel {
        public function doesContainSpecialChars(string $str): bool {
            // \p{L}: Khớp với bất kỳ chữ cái nào từ bất kỳ ngôn ngữ nào (có dấu hoặc không)
            // \p{N}: Khớp với bất kỳ con số nào
            // \s: Khớp với khoảng trắng (dấu cách, tab, xuống dòng)
            // Modifier /u: Bắt buộc phải có để PHP hiểu chuỗi theo chuẩn UTF-8
            if (preg_match("/[^\p{L}\p{N}\s]/u", $str)) return true;
            return false;
        }

        public function doesContainLetter(string $str): bool {
            return false;
        }

        public function doesContainNumber(string $str): bool {
            return false;
        }

        public function validateIDForAutoIncrement(int $x): bool {
            return $x > 0 ? true : false; 
        }

        public function setIDForAutoIncrementType(int &$x, int $y): void {
            $isOk = $this->validateIDForAutoIncrement($y);
            if (!$isOk) throw new InvalidArgumentException("Invalid ID!");
            $x = $y;
        }
    }
?>
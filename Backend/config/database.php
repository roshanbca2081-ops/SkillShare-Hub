<?php
/**
 * Database Configuration File
 * Contains database connection settings and helper functions
 */

class Database {
    private static $instance = null;
    private $connection;

    // Private constructor to prevent direct creation
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET,
            ];

            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // In development, show error details
            if (ENVIRONMENT === 'development') {
                die("Connection failed: " . $e->getMessage());
            } else {
                die("Database connection error. Please try again later.");
            }
        }
    }

    // Get the singleton instance
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Get the PDO connection
    public function getConnection() {
        return $this->connection;
    }

    // Prepare a SQL statement
    public function prepare($sql) {
        return $this->connection->prepare($sql);
    }

    // Prepare and execute a SQL query
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            // Log the error
            error_log("Database error: " . $e->getMessage());

            // In development, show error details
            if (ENVIRONMENT === 'development') {
                throw $e;
            } else {
                throw new Exception("Database query failed.");
            }
        }
    }

    // Execute a prepared statement
    public function execute($sql, $params = []) {
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    // Fetch a single row
    public function fetch($sql, $params = []) {
        $stmt = $this->execute($sql, $params);
        return $stmt->fetch();
    }

    // Fetch all rows
    public function fetchAll($sql, $params = []) {
        $stmt = $this->execute($sql, $params);
        return $stmt->fetchAll();
    }

    // Insert data into table
    public function insert($table, $data) {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $sql = "INSERT INTO {$table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->execute($sql, array_values($data));
        return $this->connection->lastInsertId();
    }

    // Update data in table
    public function update($table, $data, $where, $whereParams = []) {
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "{$key} = ?";
        }
        $sql = "UPDATE {$table} SET " . implode(', ', $set) . " WHERE {$where}";
        $params = array_merge(array_values($data), $whereParams);
        $stmt = $this->execute($sql, $params);
        return $stmt->rowCount();
    }

    // Delete data from table
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->execute($sql, $params);
        return $stmt->rowCount();
    }

    // Count rows in table
    public function count($table, $where = '', $params = []) {
        $sql = "SELECT COUNT(*) as total FROM {$table}";
        if (!empty($where)) {
            $sql .= " WHERE {$where}";
        }
        $stmt = $this->execute($sql, $params);
        $result = $stmt->fetch();
        return (int) $result['total'];
    }

    // Begin a transaction
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    // Commit a transaction
    public function commit() {
        return $this->connection->commit();
    }

    // Rollback a transaction
    public function rollBack() {
        return $this->connection->rollback();
    }

    // Get the last inserted ID
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
}

// Initialize database connection
function getDB() {
    return Database::getInstance()->getConnection();
}

// Common database helper functions
function executeQuery($sql, $params = []) {
    return Database::getInstance()->query($sql, $params);
}

function fetchAll($sql, $params = []) {
    return executeQuery($sql, $params)->fetchAll();
}

function fetchOne($sql, $params = []) {
    return executeQuery($sql, $params)->fetch();
}

function rowCount($sql, $params = []) {
    return executeQuery($sql, $params)->rowCount();
}

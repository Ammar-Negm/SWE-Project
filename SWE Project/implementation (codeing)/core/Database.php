<?php
// ============================================================
// FILE: core/Database.php
// FIXED — moved setAttribute calls into constructor
// where they actually execute (were dead code after return).
// ============================================================
class Database {
    private static $instance = null;
    private $pdo;

    private $host   = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "hameed_warehouse";

    // Private constructor → Singleton
    private function __construct() {
        try {
            $this->pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8",
                $this->username,
                $this->password
            );
            // ★ FIXED — these must be BEFORE the return, inside constructor
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            die("Database Connection Failed: " . $e->getMessage());
        }
    }

    // Public static getter → always returns same instance
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Getter for PDO — clean, nothing after return
    public function getConnection() {
        return $this->pdo;
    }
}

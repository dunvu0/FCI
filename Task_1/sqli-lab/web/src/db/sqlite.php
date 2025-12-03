<?php

/**
 * SQLite Database Connection
 */
class SQLiteDB {
    private $conn;
    private $dbPath;
    
    public function __construct() {
        // Database file in /var/www/html/sqli.db (writable by www-data)
        $this->dbPath = __DIR__ . '/../../sqli.db';
        
        $this->connect();
        $this->initializeSchema();
    }
    
    private function connect() {
        try {
            $this->conn = new SQLite3($this->dbPath);
            $this->conn->enableExceptions(true);
        } catch (Exception $e) {
            die("SQLite Connection failed: " . $e->getMessage());
        }
    }
    
    private function initializeSchema() {
        // Check if tables exist
        $result = $this->conn->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'");
        if (!$result->fetchArray()) {
            // Execute schema from seed file
            $seedFile = __DIR__ . '/../../../seeds/sqlite.sql';
            if (file_exists($seedFile)) {
                $sql = file_get_contents($seedFile);
                $this->conn->exec($sql);
            }
        }
    }
    
    /**
     * Execute a single query - VULNERABLE to SQL injection
     */
    public function query($sql) {
        try {
            $result = $this->conn->query($sql);
            
            if ($result === false) {
                return ['error' => $this->conn->lastErrorMsg(), 'errno' => $this->conn->lastErrorCode()];
            }
            
            if ($result === true) {
                return ['success' => true, 'affected_rows' => $this->conn->changes()];
            }
            
            $data = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $data[] = $row;
            }
            
            return $data;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    /**
     * SQLite3::exec() supports multiple statements separated by semicolons
     */
    public function multiQuery($sql) {
        try {
            $result = $this->conn->exec($sql);
            
            if ($result === false) {
                return ['error' => $this->conn->lastErrorMsg()];
            }
            
            return [['success' => true, 'affected_rows' => $this->conn->changes()]];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    /**
     * Get database version
     */
    public function getVersion() {
        $version = SQLite3::version();
        return $version['versionString'];
    }
    
    /**
     * Get database type
     */
    public function getType() {
        return 'SQLite';
    }
    
    /**
     * Check if connected
     */
    public function isConnected() {
        return $this->conn !== null;
    }
    
    /**
     * Close connection
     */
    public function close() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
    
    /**
     * Get raw connection for advanced operations
     */
    public function getConnection() {
        return $this->conn;
    }

}

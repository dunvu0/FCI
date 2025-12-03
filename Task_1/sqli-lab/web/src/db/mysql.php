<?php

/**
 * MySQL Database Connection
 */
class MySQLDB {
    private $conn;
    private $host;
    private $user;
    private $password;
    private $database;
    
    public function __construct() {
        $this->host = getenv('MYSQL_HOST') ?: 'mysql';
        $this->user = getenv('MYSQL_USER') ?: 'sqli_user';
        $this->password = getenv('MYSQL_PASSWORD') ?: 'sqli_pass';
        $this->database = getenv('MYSQL_DB') ?: 'sqli_db';
        
        $this->connect();
    }
    
    private function connect() {
        $this->conn = new mysqli($this->host, $this->user, $this->password, $this->database);
        
        if ($this->conn->connect_error) {
            die("MySQL Connection failed: " . $this->conn->connect_error);
        }
        
        $this->conn->set_charset("utf8mb4");
    }
    
    /**
     * Execute a single query - VULNERABLE to SQL injection
     */
    public function query($sql) {
        $result = $this->conn->query($sql);
        
        if ($result === false) {
            // Return error for error-based SQLi demonstrations
            return ['error' => $this->conn->error, 'errno' => $this->conn->errno];
        }
        
        if ($result === true) {
            return ['success' => true, 'affected_rows' => $this->conn->affected_rows];
        }
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        return $data;
    }
    
    /**
     * Execute multiple queries - For stacked queries demonstration
     */
    public function multiQuery($sql) {
        $results = [];
        
        if ($this->conn->multi_query($sql)) {
            do {
                if ($result = $this->conn->store_result()) {
                    $data = [];
                    while ($row = $result->fetch_assoc()) {
                        $data[] = $row;
                    }
                    $results[] = $data;
                    $result->free();
                } else {
                    if ($this->conn->errno) {
                        $results[] = ['error' => $this->conn->error];
                    } else {
                        $results[] = ['success' => true, 'affected_rows' => $this->conn->affected_rows];
                    }
                }
            } while ($this->conn->next_result());
        } else {
            return ['error' => $this->conn->error];
        }
        
        return $results;
    }
    
    /**
     * Get database version
     */
    public function getVersion() {
        return $this->conn->server_info;
    }
    
    /**
     * Get database type
     */
    public function getType() {
        return 'MySQL';
    }
    
    /**
     * Check if connected
     */
    public function isConnected() {
        return $this->conn && $this->conn->ping();
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

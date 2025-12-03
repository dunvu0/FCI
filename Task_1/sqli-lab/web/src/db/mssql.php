<?php

/**
 * MSSQL Database Connection
 */
class MSSQLDB {
    private $conn;
    private $host;
    private $user;
    private $password;
    private $database;
    
    public function __construct() {
        $this->host = getenv('MSSQL_HOST') ?: 'mssql';
        $this->user = getenv('MSSQL_USER') ?: 'sa';
        $this->password = getenv('MSSQL_PASSWORD') ?: 'SqliPass123!';
        $this->database = getenv('MSSQL_DB') ?: 'sqli_db';
        
        $this->connect();
    }
    
    private function connect() {
        $connectionInfo = [
            'Database' => $this->database,
            'UID' => $this->user,
            'PWD' => $this->password,
            'CharacterSet' => 'UTF-8',
            'MultipleActiveResultSets' => true
        ];
        
        $this->conn = sqlsrv_connect($this->host, $connectionInfo);
        
        if ($this->conn === false) {
            $errors = sqlsrv_errors();
            die("MSSQL Connection failed: " . print_r($errors, true));
        }
    }
    
    /**
     * Execute a single query - VULNERABLE to SQL injection
     */
    public function query($sql) {
        $stmt = sqlsrv_query($this->conn, $sql);
        
        if ($stmt === false) {
            $errors = sqlsrv_errors();
            return ['error' => print_r($errors, true)];
        }
        
        // Check if it's a SELECT query
        $metadata = sqlsrv_field_metadata($stmt);
        
        if ($metadata === false || empty($metadata)) {
            // Non-SELECT query (INSERT, UPDATE, DELETE, etc.)
            $rowsAffected = sqlsrv_rows_affected($stmt);
            sqlsrv_free_stmt($stmt);
            return ['success' => true, 'affected_rows' => $rowsAffected];
        }
        
        $data = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $data[] = $row;
        }
        
        sqlsrv_free_stmt($stmt);
        return $data;
    }
    
    /**
     * MSSQL supports multiple statements in a single query
     */
    public function multiQuery($sql) {
        $results = [];
        
        $stmt = sqlsrv_query($this->conn, $sql);
        
        if ($stmt === false) {
            $errors = sqlsrv_errors();
            return ['error' => print_r($errors, true)];
        }
        
        do {
            $metadata = sqlsrv_field_metadata($stmt);
            
            if ($metadata === false || empty($metadata)) {
                // Non-SELECT query
                $rowsAffected = sqlsrv_rows_affected($stmt);
                $results[] = ['success' => true, 'affected_rows' => $rowsAffected];
            } else {
                // SELECT query
                $data = [];
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $data[] = $row;
                }
                $results[] = $data;
            }
        } while (sqlsrv_next_result($stmt));
        
        sqlsrv_free_stmt($stmt);
        return $results;
    }
    
    /**
     * Get database version
     */
    public function getVersion() {
        $sql = "SELECT @@VERSION as version";
        $result = $this->query($sql);
        
        if (isset($result[0]['version'])) {
            return $result[0]['version'];
        }
        
        return 'Unknown';
    }
    
    /**
     * Get database type
     */
    public function getType() {
        return 'MSSQL';
    }
    
    /**
     * Check if connected
     */
    public function isConnected() {
        return $this->conn !== false && $this->conn !== null;
    }
    
    /**
     * Close connection
     */
    public function close() {
        if ($this->conn) {
            sqlsrv_close($this->conn);
        }
    }
    
    /**
     * Get raw connection for advanced operations
     */
    public function getConnection() {
        return $this->conn;
    }
    
}

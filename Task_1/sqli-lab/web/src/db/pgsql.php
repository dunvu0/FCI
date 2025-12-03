<?php

/**
 * PostgreSQL Database Connection
 */
class PostgreSQLDB {
    private $conn;
    private $host;
    private $user;
    private $password;
    private $database;
    
    public function __construct() {
        $this->host = getenv('POSTGRES_HOST') ?: 'postgres';
        $this->user = getenv('POSTGRES_USER') ?: 'sqli_user';
        $this->password = getenv('POSTGRES_PASSWORD') ?: 'sqli_pass';
        $this->database = getenv('POSTGRES_DB') ?: 'sqli_db';
        
        $this->connect();
    }
    
    private function connect() {
        $connString = sprintf(
            "host=%s port=5432 dbname=%s user=%s password=%s",
            $this->host,
            $this->database,
            $this->user,
            $this->password
        );
        
        $this->conn = pg_connect($connString);
        
        if (!$this->conn) {
            die("PostgreSQL Connection failed");
        }
    }
    
    /**
     * Execute a single query - VULNERABLE to SQL injection
     */
    public function query($sql) {
        $result = pg_query($this->conn, $sql);
        
        if ($result === false) {
            return ['error' => pg_last_error($this->conn)];
        }
        
        // Check if it's a SELECT query
        $resultType = pg_result_status($result);
        
        if ($resultType === PGSQL_COMMAND_OK) {
            return ['success' => true, 'affected_rows' => pg_affected_rows($result)];
        }
        
        $data = [];
        while ($row = pg_fetch_assoc($result)) {
            $data[] = $row;
        }
        
        return $data;
    }
    
    /**
     * Using pg_send_query for async execution which supports multiple statements
     */
    public function multiQuery($sql) {
        $results = [];
        
        // Send query asynchronously (supports multiple statements)
        if (!pg_send_query($this->conn, $sql)) {
            return ['error' => pg_last_error($this->conn)];
        }
        
        // Get all results
        while ($result = pg_get_result($this->conn)) {
            $resultType = pg_result_status($result);
            
            if ($resultType === PGSQL_FATAL_ERROR || $resultType === PGSQL_BAD_RESPONSE) {
                $results[] = ['error' => pg_result_error($result)];
            } elseif ($resultType === PGSQL_COMMAND_OK) {
                $results[] = ['success' => true, 'affected_rows' => pg_affected_rows($result)];
            } elseif ($resultType === PGSQL_TUPLES_OK) {
                $data = [];
                while ($row = pg_fetch_assoc($result)) {
                    $data[] = $row;
                }
                $results[] = $data;
            }
        }
        
        return $results;
    }
    
    /**
     * Get database version
     */
    public function getVersion() {
        $result = pg_version($this->conn);
        return $result['server'] ?? 'Unknown';
    }
    
    /**
     * Get database type
     */
    public function getType() {
        return 'PostgreSQL';
    }
    
    /**
     * Check if connected
     */
    public function isConnected() {
        return $this->conn && pg_connection_status($this->conn) === PGSQL_CONNECTION_OK;
    }
    
    /**
     * Close connection
     */
    public function close() {
        if ($this->conn) {
            pg_close($this->conn);
        }
    }
    
    /**
     * Get raw connection for advanced operations
     */
    public function getConnection() {
        return $this->conn;
    }

}

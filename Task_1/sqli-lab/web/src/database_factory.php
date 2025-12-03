<?php

/**
 * Database Factory - Centralized database creation
 */
class DatabaseFactory {
    /**
     * Create database instance based on type
     * 
     * @param string $type Database type (mysql, sqlite, pgsql, mssql)
     * @return object Database connection object
     * @throws Exception if database driver not found or connection fails
     */
    public static function create($type) {
        // Validate database type
        $allowedTypes = ['mysql', 'sqlite', 'pgsql', 'mssql'];
        if (!in_array($type, $allowedTypes)) {
            $type = 'mysql';
        }
        
        // Load database driver file
        $dbFile = __DIR__ . "/db/{$type}.php";
        if (!file_exists($dbFile)) {
            throw new Exception("Database driver not found: {$type}");
        }
        
        require_once $dbFile;
        
        // Map database type to class name
        $classMap = [
            'mysql' => 'MySQLDB',
            'sqlite' => 'SQLiteDB',
            'pgsql' => 'PostgreSQLDB',
            'mssql' => 'MSSQLDB'
        ];
        
        $className = $classMap[$type];
        $db = new $className();
        
        // Verify connection
        if (!$db->isConnected()) {
            throw new Exception("Failed to connect to {$type} database");
        }
        
        return $db;
    }
}

<?php

/**
 * Search Controller
 * Demonstrates: Error-based and UNION-based SQL Injection
 */
class SearchController {
    private $db;
    private $dbType;
    
    public function __construct($db, $dbType) {
        $this->db = $db;
        $this->dbType = $dbType;
    }
    
    /**
     * Show search form
     */
    public function showSearch() {
        $dbType = $this->dbType;
        require __DIR__ . '/../../templates/search.php';
    }
    
    /**
     * Handle search - VULNERABLE to SQL Injection
     */
    public function handleSearch() {
        $searchQuery = $_GET['q'] ?? $_POST['q'] ?? '';
        
        $query = "SELECT * FROM products WHERE name LIKE '%$searchQuery%' OR description LIKE '%$searchQuery%'";
        
        $displayQuery = $query;
        
        $result = $this->db->query($query);
        
        // Check for errors
        if (isset($result['error'])) {
            $error = $result['error'];
            $dbType = $this->dbType;
            require __DIR__ . '/../../templates/result.php';
            return;
        }
        
        // Display results
        $results = $result;
        $dbType = $this->dbType;
        require __DIR__ . '/../../templates/result.php';
    }
}

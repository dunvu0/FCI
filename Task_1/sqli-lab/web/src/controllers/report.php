<?php

/**
 * Report Controller
 * Demonstrates: Stacked Queries, Out-of-Band, Second-Order SQLi, SQLi to RCE
 */
class ReportController {
    private $db;
    private $dbType;
    
    public function __construct($db, $dbType) {
        $this->db = $db;
        $this->dbType = $dbType;
    }
    
    /**
     * Show report form
     */
    public function showReport() {
        $dbType = $this->dbType;
        require __DIR__ . '/../../templates/report.php';
    }
    
    /**
     * Handle report submission - VULNERABLE to SQL Injection
     */
    public function handleReport() {
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $userId = $_SESSION['user']['id'] ?? 1;
        
        $query = "INSERT INTO reports (title, content, user_id) VALUES ('$title', '$content', $userId)";
        
        $displayQuery = $query;
        
        // Execute with multi-query (allows stacked queries)
        $result = $this->db->multiQuery($query);
        
        // Check for errors
        if (isset($result['error'])) {
            $error = $result['error'];
            $dbType = $this->dbType;
            require __DIR__ . '/../../templates/report.php';
            return;
        }
        
        // Success message
        $success = "Report submitted successfully!";
        
        $dbType = $this->dbType;
        require __DIR__ . '/../../templates/report.php';
    }
    
}

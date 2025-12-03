<?php

/**
 * Authentication Controller
 * Demonstrates: Blind SQL Injection (Boolean-based and Time-based)
 */
class AuthController {
    private $db;
    private $dbType;
    
    public function __construct($db, $dbType) {
        $this->db = $db;
        $this->dbType = $dbType;
    }
    
    /**
     * Show login form
     */
    public function showLogin() {
        $dbType = $this->dbType;
        require __DIR__ . '/../../templates/login.php';
    }
    
    /**
     * Handle login - VULNERABLE to SQL Injection
     */
    public function handleLogin() {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
        
        $displayQuery = $query;
        
        // Execute query
        $result = $this->db->query($query);
        
        // Check for errors
        if (isset($result['error'])) {
            $error = $result['error'];
            $dbType = $this->dbType;
            require __DIR__ . '/../../templates/login.php';
            return;
        }
        
        // Check if user found
        if (is_array($result) && count($result) > 0) {
            // Login successful
            $_SESSION['user'] = $result[0];
            $_SESSION['logged_in'] = true;
            
            $success = "Login successful! Welcome, " . htmlspecialchars($result[0]['username']);
            $user = $result[0];
            $dbType = $this->dbType;
            require __DIR__ . '/../../templates/login.php';
        } else {
            // Login failed
            $error = "Invalid username or password";
            $dbType = $this->dbType;
            require __DIR__ . '/../../templates/login.php';
        }
    }
    
    /**
     * Logout
     */
    public function logout() {
        session_destroy();
        header("Location: /login?db=" . $this->dbType);
        exit;
    }
}

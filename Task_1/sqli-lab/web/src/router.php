<?php

require_once __DIR__ . '/database_factory.php';

/**
 * Closure-based Router
 * 
 * Inspired by Laravel/Slim framework routing style.
 * Uses closures for cleaner route definitions.
 */
class Router {
    private $routes = [];
    private $db;
    private $dbType;
    
    public function __construct() {
        // Get database type from query parameter
        $this->dbType = $_GET['db'] ?? 'mysql';
        
        // Create database connection via factory
        try {
            $this->db = DatabaseFactory::create($this->dbType);
        } catch (Exception $e) {
            die("Database error: " . $e->getMessage());
        }
        
        // Register all routes
        $this->registerRoutes();
    }
    
    /**
     * Register GET route
     */
    private function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }
    
    /**
     * Register POST route
     */
    private function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }
    
    /**
     * Register route for multiple HTTP methods
     */
    private function map($methods, $path, $callback) {
        foreach ((array)$methods as $method) {
            $this->routes[$method][$path] = $callback;
        }
    }
    
    /**
     * Register all application routes
     */
    private function registerRoutes() {
        // Home page
        $this->get('/', function() {
            $dbInfo = [
                'type' => $this->db->getType(),
                'version' => $this->db->getVersion(),
                'connected' => $this->db->isConnected()
            ];
            require __DIR__ . '/../templates/home.php';
        });
        
        // Login routes
        $this->get('/login', function() {
            require_once __DIR__ . '/controllers/auth.php';
            $controller = new AuthController($this->db, $this->dbType);
            $controller->showLogin();
        });
        
        $this->post('/login', function() {
            require_once __DIR__ . '/controllers/auth.php';
            $controller = new AuthController($this->db, $this->dbType);
            $controller->handleLogin();
        });
        
        // Logout route
        $this->get('/logout', function() {
            require_once __DIR__ . '/controllers/auth.php';
            $controller = new AuthController($this->db, $this->dbType);
            $controller->logout();
        });
        
        // Search routes
        $this->get('/search', function() {
            // Show search form or handle GET search
            require_once __DIR__ . '/controllers/search.php';
            $controller = new SearchController($this->db, $this->dbType);
            
            if (isset($_GET['q'])) {
                $controller->handleSearch();
            } else {
                $controller->showSearch();
            }
        });
        
        $this->post('/search', function() {
            require_once __DIR__ . '/controllers/search.php';
            $controller = new SearchController($this->db, $this->dbType);
            $controller->handleSearch();
        });
        
        // Report routes
        $this->get('/report', function() {
            require_once __DIR__ . '/controllers/report.php';
            $controller = new ReportController($this->db, $this->dbType);
            $controller->showReport();
        });
        
        $this->post('/report', function() {
            require_once __DIR__ . '/controllers/report.php';
            $controller = new ReportController($this->db, $this->dbType);
            $controller->handleReport();
        });
    }
    
    /**
     * Route incoming request to appropriate handler
     */
    public function route($uri, $method = 'GET') {
        // Parse URI to remove query string
        $path = parse_url($uri, PHP_URL_PATH);
        
        // Clean up path
        $path = rtrim($path, '/');
        if (empty($path)) {
            $path = '/';
        }
        
        // Special handling for /index.php
        if ($path === '/index.php') {
            $path = '/';
        }
        
        // Check if route exists for this method and path
        if (isset($this->routes[$method][$path])) {
            // Execute the closure
            call_user_func($this->routes[$method][$path]);
        } else {
            // Route not found
            http_response_code(404);
            echo "404 - Page Not Found";
        }
    }
    
    /**
     * Get database type
     */
    public function getDbType() {
        return $this->dbType;
    }
    
    /**
     * Get database connection
     */
    public function getDbConnection() {
        return $this->db;
    }
}

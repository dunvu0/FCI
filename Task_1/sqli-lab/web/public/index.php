<?php
/**
 * Main Entry Point
 */

// Start session
session_start();

// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load router
require_once __DIR__ . '/../src/router.php';

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Get request URI
$uri = $_SERVER['REQUEST_URI'];

// Create router instance and route request
$router = new Router();
$router->route($uri, $method);

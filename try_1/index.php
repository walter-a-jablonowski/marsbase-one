<?php
/**
 * Marsbase.one - Main entry point
 * 
 * A web application to help build a sustainable civilization on Mars
 */

// Initialize session
session_start();

// Include core files
require_once __DIR__ . '/core/config.php';
require_once __DIR__ . '/core/functions.php';
require_once __DIR__ . '/core/yaml_functions.php';
require_once __DIR__ . '/core/item_functions.php';
require_once __DIR__ . '/core/requirement_functions.php';
require_once __DIR__ . '/core/user_functions.php';
require_once __DIR__ . '/core/auth.php';
require_once __DIR__ . '/core/router.php';

// Route the request
$router = new Router();
$router->route();
?>

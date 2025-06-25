<?php
require_once __DIR__ . '/vendor/autoload.php';

use Marsbase\Core\Auth;
use Marsbase\Core\Utils;

// Initialize auth
$auth = Auth::getInstance();

// Get the requested action
$action = $_GET['action'] ?? '';

// Validate action
if( empty($action) )
{
  Utils::jsonResponse(['error' => 'No action specified'], 400);
}

// Get JSON data from request
$jsonData = file_get_contents('php://input');
$data = !empty($jsonData) ? json_decode($jsonData, true) : [];

// If JSON parsing failed
if( $jsonData && json_last_error() !== JSON_ERROR_NONE )
{
  Utils::jsonResponse(['error' => 'Invalid JSON data'], 400);
}

// Route to appropriate handler
$handlerFile = __DIR__ . "/pages/{$action}.php";

if( file_exists($handlerFile) )
{
  include $handlerFile;
}
else
{
  Utils::jsonResponse(['error' => 'Invalid action'], 404);
}

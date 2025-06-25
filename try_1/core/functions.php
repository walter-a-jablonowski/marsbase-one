<?php
/**
 * Core utility functions
 */

// Include Symfony YAML component
require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * Generate an ID from a name
 * 
 * Converts each word to first character uppercase
 * Removes all non-alphanumeric characters
 * Adds a short random string to the end
 */
function generateId( $name ) : string
{
  // Convert to title case
  $name = ucwords($name);
  
  // Remove all non-alphanumeric characters
  $id = preg_replace('/[^a-zA-Z0-9]/', '', $name);
  
  // Add a short random string (6 characters)
  $randomString = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6);
  
  return $id . $randomString;
}

/**
 * Load data from a YAML file
 */
function loadYaml( $filePath ) : array
{
  if( !file_exists($filePath) )
  {
    return [];
  }
  
  try
  {
    $yaml = \Symfony\Component\Yaml\Yaml::parseFile($filePath);
    return $yaml ?: [];
  }
  catch( Exception $e )
  {
    error_log("Error loading YAML file: " . $e->getMessage());
    return [];
  }
}

/**
 * Save data to a YAML file
 */
function saveYaml( $filePath, $data ) : bool
{
  try
  {
    $yaml = \Symfony\Component\Yaml\Yaml::dump($data, 4, 2);
    return file_put_contents($filePath, $yaml) !== false;
  }
  catch( Exception $e )
  {
    error_log("Error saving YAML file: " . $e->getMessage());
    return false;
  }
}

/**
 * Get current timestamp as Unix timestamp
 */
function getCurrentTimestamp() : int
{
  return time();
}

/**
 * Format timestamp to human-readable date
 */
function formatTimestamp($timestamp) : string
{
  if( empty($timestamp) )
  {
    return 'Unknown';
  }
  
  $now = time();
  $diff = $now - $timestamp;
  
  if( $diff < 60 )
  {
    return 'Just now';
  }
  elseif( $diff < 3600 )
  {
    $minutes = floor($diff / 60);
    return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
  }
  elseif( $diff < 86400 )
  {
    $hours = floor($diff / 3600);
    return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
  }
  elseif( $diff < 604800 )
  {
    $days = floor($diff / 86400);
    return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
  }
  else
  {
    return date('M j, Y', $timestamp);
  }
}

/**
 * Sanitize user input
 */
function sanitize( $input ) : string
{
  return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate a random filename for uploaded files
 */
function generateFilename( $extension ) : string
{
  return md5(uniqid(rand(), true)) . '.' . $extension;
}

/**
 * Handle file upload and return the new filename
 */
function handleFileUpload( $fileData, $targetDir ) : string
{
  if( !isset($fileData) || $fileData['error'] !== UPLOAD_ERR_OK )
  {
    return '';
  }
  
  $extension = pathinfo($fileData['name'], PATHINFO_EXTENSION);
  $newFilename = generateFilename($extension);
  $targetPath = $targetDir . '/' . $newFilename;
  
  if( move_uploaded_file($fileData['tmp_name'], $targetPath) )
  {
    return $newFilename;
  }
  
  return '';
}

/**
 * Calculate score for an item or requirement
 */
function calculateScore( $item ) : int
{
  $upvotes = $item['upvotes'] ?? [];
  $downvotes = $item['downvotes'] ?? [];
  
  $score = count($upvotes) - count($downvotes);
  
  return $score;
}

/**
 * Format error message
 */
function formatError( $message ) : string
{
  return '<div class="alert alert-danger">' . $message . '</div>';
}

/**
 * Format success message
 */
function formatSuccess( $message ) : string
{
  return '<div class="alert alert-success">' . $message . '</div>';
}

/**
 * Redirect to a URL
 */
function redirect( $url )
{
  header("Location: $url");
  exit;
}

/**
 * Get all requirements
 */
function getAllRequirements() : array
{
  $requirements = [];
  $files = glob(REQUIREMENTS_DIR . '/*.yaml');
  
  foreach( $files as $file )
  {
    $requirement = loadYaml($file);
    if( !empty($requirement) )
    {
      $requirements[$requirement['id']] = $requirement;
    }
  }
  
  return $requirements;
}

/**
 * Get all items
 */
function getAllItems() : array
{
  $items = [];
  $files = glob(ITEMS_DIR . '/*.yaml');
  
  foreach( $files as $file )
  {
    $item = loadYaml($file);
    if( !empty($item) )
    {
      $items[$item['id']] = $item;
    }
  }
  
  return $items;
}

/**
 * Get all users
 */
function getAllUsers() : array
{
  $users = [];
  $files = glob(USERS_DIR . '/*.yaml');
  
  foreach( $files as $file )
  {
    $user = loadYaml($file);
    if( !empty($user) )
    {
      $users[$user['id']] = $user;
    }
  }
  
  return $users;
}

/**
 * Get requirement by ID
 */
function getRequirement( $id ) : array
{
  $filePath = REQUIREMENTS_DIR . '/' . $id . '.yaml';
  return loadYaml($filePath);
}

/**
 * Get item by ID
 */
function getItem( $id ) : array
{
  $filePath = ITEMS_DIR . '/' . $id . '.yaml';
  return loadYaml($filePath);
}

/**
 * Get user by ID (email)
 */
function getUser( $id ) : array
{
  $filePath = USERS_DIR . '/' . md5($id) . '.yaml';
  return loadYaml($filePath);
}

/**
 * Save requirement
 */
function saveRequirement( $requirement ) : bool
{
  if( empty($requirement['id']) )
  {
    return false;
  }
  
  $filePath = REQUIREMENTS_DIR . '/' . $requirement['id'] . '.yaml';
  return saveYaml($filePath, $requirement);
}

/**
 * Save item
 */
function saveItem( $item ) : bool
{
  if( empty($item['id']) )
  {
    return false;
  }
  
  $filePath = ITEMS_DIR . '/' . $item['id'] . '.yaml';
  return saveYaml($filePath, $item);
}

/**
 * Save user
 */
function saveUser( $user ) : bool
{
  if( empty($user['id']) )
  {
    return false;
  }
  
  $filePath = USERS_DIR . '/' . md5($user['id']) . '.yaml';
  return saveYaml($filePath, $user);
}
?>

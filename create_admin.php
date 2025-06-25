<?php
/**
 * Admin User Creation Script
 * 
 * This script creates the default admin user for MarsBase.One
 */

// Include autoloader
require_once 'vendor/autoload.php';

use Marsbase\Core\Config;
use Marsbase\Models\User;

// Initialize config
$config = Config::getInstance();

// Define admin user data
$adminData = [
  'id' => 'admin',
  'type' => 'person',
  'email' => 'admin@example.com',
  'password' => 'superadmin',  // Will be hashed by the User model
  'name' => 'Admin',
  'bio' => 'System administrator for MarsBase.One',
  'expertise' => 'Mars colony administration, system management',
  'image' => '',
  'location' => 'Mars Base One HQ',
  'memberIds' => [],
  'followedItemIds' => [],
  'followedReqIds' => [],
  'followedUserIds' => [],
  'itemScores' => [],
  'reqScores' => [],
  'modifiedAt' => date('Y-m-d H:i:s')
];

// Check if admin directory exists
$adminDir = 'data/users/admin';
if( !is_dir($adminDir) ) {
  mkdir($adminDir, 0755, true);
  
  // Create uploads directory
  if( !is_dir("$adminDir/uploads") ) {
    mkdir("$adminDir/uploads", 0755, true);
  }
}

// Create admin user
try {
  $admin = new User($adminData);
  
  // Hash the password
  $admin->setPassword($adminData['password']);
  
  // Save the user
  if( $admin->save() ) {
    echo "Admin user created successfully!\n";
    echo "Email: {$adminData['email']}\n";
    echo "Password: {$adminData['password']}\n";
  } else {
    echo "Failed to create admin user.\n";
  }
} catch( Exception $e ) {
  echo "Error: " . $e->getMessage() . "\n";
}

<?php
/**
 * Create Admin User Script
 * Creates an initial admin user for the Mars Colony application
 */

// Include core files
require_once __DIR__ . '/core/config.php';
require_once __DIR__ . '/core/functions.php';
require_once __DIR__ . '/core/yaml_functions.php';
require_once __DIR__ . '/core/auth.php';
require_once __DIR__ . '/vendor/autoload.php';

// Set constants
define('DATA_DIR', __DIR__ . '/data');

// Admin user data
$email = 'admin@example.com';
$userId = md5($email);

// Check if the user already exists
$userFile = DATA_DIR . '/users/' . $userId . '.yaml';
if (file_exists($userFile)) {
  echo "Admin user already exists!\n";
  exit;
}

// Create user directory if it doesn't exist
$usersDir = DATA_DIR . '/users';
if (!is_dir($usersDir)) {
  mkdir($usersDir, 0755, true);
}

// Create the admin user directly
$user = [
  'id' => $userId,
  'email' => $email,
  'type' => 'person',
  'password' => password_hash('superadmin', PASSWORD_DEFAULT),
  'name' => 'Admin',
  'memberIds' => [],
  'image' => '',
  'bio' => 'System Administrator',
  'expertise' => 'System Administration',
  'location' => 'Mars Colony HQ',
  'website' => '',
  'socialUrl' => '',
  'itemsFollowing' => [],
  'requirementsFollowing' => [],
  'usersFollowing' => [],
  'itemScore' => [],
  'reqScore' => [],
  'modifiedAt' => time()
];

// Save the user
if (saveYamlFile($userFile, $user)) {
  echo "Admin user created successfully!\n";
  echo "Email: {$email}\n";
  echo "Password: superadmin\n";
} else {
  echo "Failed to create admin user!\n";
}
?>

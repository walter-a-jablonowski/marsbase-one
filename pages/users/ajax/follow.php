<?php
use Marsbase\Core\Auth;
use Marsbase\Core\Utils;
use Marsbase\Models\User;

// Check if user is logged in
$auth = Auth::getInstance();
if( ! $auth->isLoggedIn() )
{
  Utils::jsonResponse(['success' => false, 'error' => 'You must be logged in to follow users.']);
  exit;
}

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);
if( ! $data )
{
  Utils::jsonResponse(['success' => false, 'error' => 'Invalid request data.']);
  exit;
}

// Validate input
$userId = $data['userId'] ?? '';
$follow = $data['follow'] ?? false;

if( empty($userId) )
{
  Utils::jsonResponse(['success' => false, 'error' => 'User ID is required.']);
  exit;
}

// Load target user
$targetUser = User::findById($userId);
if( ! $targetUser )
{
  Utils::jsonResponse(['success' => false, 'error' => 'User not found.']);
  exit;
}

// Get current user
$currentUser = $auth->getUser();
$currentUserId = $currentUser->getId();

// Prevent following yourself
if( $currentUserId === $userId )
{
  Utils::jsonResponse(['success' => false, 'error' => 'You cannot follow yourself.']);
  exit;
}

// Update following status
if( $follow )
{
  $currentUser->followUser($userId);
}
else
{
  $currentUser->unfollowUser($userId);
}

// Save user data
if( ! $currentUser->save() )
{
  Utils::jsonResponse(['success' => false, 'error' => 'Failed to update following status.']);
  exit;
}

// Return success response
Utils::jsonResponse([
  'success' => true,
  'following' => $follow
]);

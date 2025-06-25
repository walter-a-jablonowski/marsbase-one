<?php
/**
 * User utility functions
 *
 * Note: Core functions like getAllUsers(), getUser(), and saveUser() are already
 * defined in core/functions.php. This file extends those with additional
 * user-specific functionality.
 */

/**
 * Get all users with sensitive information removed
 * 
 * This extends the core getAllUsers() function by removing sensitive information
 */
function getAllUsersPublic()
{
  $users = [];
  $usersDir = DATA_DIR . '/users';
  
  if( !is_dir($usersDir) )
  {
    return $users;
  }
  
  $files = scandir($usersDir);
  
  foreach( $files as $file )
  {
    if( $file === '.' || $file === '..' )
    {
      continue;
    }
    
    $filePath = $usersDir . '/' . $file;
    
    if( is_file($filePath) && pathinfo($filePath, PATHINFO_EXTENSION) === 'yaml' )
    {
      $user = loadYamlFile($filePath);
      
      if( !empty($user) )
      {
        // Remove sensitive information
        unset($user['password']);
        $users[] = $user;
      }
    }
  }
  
  return $users;
}

/**
 * Get user by ID with sensitive information removed
 */
function getUserPublic($id)
{
  $filePath = DATA_DIR . '/users/' . $id . '.yaml';
  
  if( !file_exists($filePath) )
  {
    return null;
  }
  
  $user = loadYamlFile($filePath);
  
  if( !empty($user) )
  {
    // Remove sensitive information
    unset($user['password']);
  }
  
  return $user;
}

/**
 * Get user by email with sensitive information removed
 */
function getUserByEmailPublic($email)
{
  $id = md5($email);
  return getUserPublic($id);
}

// Note: saveUser function is already defined in core/functions.php

/**
 * Delete user
 */
function deleteUser($id)
{
  $filePath = DATA_DIR . '/users/' . $id . '.yaml';
  
  if( file_exists($filePath) )
  {
    return unlink($filePath);
  }
  
  return false;
}

/**
 * Get users followed by user
 */
function getUsersFollowedByUser($userId)
{
  $user = getUserPublic($userId);
  
  if( empty($user) || empty($user['usersFollowing']) )
  {
    return [];
  }
  
  $followedUsers = [];
  
  foreach( $user['usersFollowing'] as $followedId )
  {
    $followedUser = getUserPublic($followedId);
    if( !empty($followedUser) )
    {
      $followedUsers[] = $followedUser;
    }
  }
  
  return $followedUsers;
}

/**
 * Get users following a user
 */
function getUserFollowers($userId)
{
  $followers = [];
  $allUsers = getAllUsersPublic();
  
  foreach( $allUsers as $user )
  {
    if( !empty($user['usersFollowing']) && in_array($userId, $user['usersFollowing']) )
    {
      $followers[] = $user;
    }
  }
  
  return $followers;
}

/**
 * Get user contributions
 */
function getUserContributions($userId)
{
  $contributions = [];
  $allItems = getAllItemsWithScores();
  
  foreach( $allItems as $item )
  {
    if( !empty($item['contributions']) )
    {
      foreach( $item['contributions'] as $contribution )
      {
        if( $contribution['user'] === $userId )
        {
          $contributions[] = [
            'item' => $item,
            'amount' => $contribution['amount'],
            'time' => $contribution['time']
          ];
        }
      }
    }
  }
  
  // Sort by time (newest first)
  usort($contributions, function($a, $b) {
    return $b['time'] - $a['time'];
  });
  
  return $contributions;
}

/**
 * Get user activity
 */
function getUserActivity($userId)
{
  $activity = [];
  
  // Get requirements created
  $requirements = getRequirementsByUser($userId);
  foreach( $requirements as $req )
  {
    $activity[] = [
      'type' => 'requirement_created',
      'time' => $req['modifiedAt'] ?? 0,
      'data' => $req
    ];
  }
  
  // Get items created
  $items = getItemsByUser($userId);
  foreach( $items as $item )
  {
    $activity[] = [
      'type' => 'item_created',
      'time' => $item['modifiedAt'] ?? 0,
      'data' => $item
    ];
  }
  
  // Get contributions
  $contributions = getUserContributions($userId);
  foreach( $contributions as $contribution )
  {
    $activity[] = [
      'type' => 'contribution',
      'time' => $contribution['time'],
      'data' => $contribution
    ];
  }
  
  // Sort by time (newest first)
  usort($activity, function($a, $b) {
    return $b['time'] - $a['time'];
  });
  
  return $activity;
}

/**
 * Format user activity for display
 */
function formatUserActivity($activity)
{
  $html = '';
  
  foreach( $activity as $item )
  {
    $html .= '<div class="activity-item">';
    
    switch( $item['type'] )
    {
      case 'requirement_created':
        $req = $item['data'];
        $html .= '<div class="activity-icon bg-success"><i class="fas fa-clipboard-list"></i></div>';
        $html .= '<div class="activity-content">';
        $html .= '<div class="activity-time">' . formatTimestamp($item['time']) . '</div>';
        $html .= '<div class="activity-title">Created requirement: <a href="index.php?page=requirements&action=view&id=' . $req['id'] . '">' . $req['name'] . '</a></div>';
        $html .= '</div>';
        break;
        
      case 'item_created':
        $itemData = $item['data'];
        $html .= '<div class="activity-icon bg-primary"><i class="fas fa-cube"></i></div>';
        $html .= '<div class="activity-content">';
        $html .= '<div class="activity-time">' . formatTimestamp($item['time']) . '</div>';
        $html .= '<div class="activity-title">Created item: <a href="index.php?page=items&action=view&id=' . $itemData['id'] . '">' . $itemData['name'] . '</a></div>';
        $html .= '</div>';
        break;
        
      case 'contribution':
        $contribution = $item['data'];
        $html .= '<div class="activity-icon bg-warning"><i class="fas fa-hand-holding-usd"></i></div>';
        $html .= '<div class="activity-content">';
        $html .= '<div class="activity-time">' . formatTimestamp($item['time']) . '</div>';
        $html .= '<div class="activity-title">Contributed $' . $contribution['amount'] . ' to <a href="index.php?page=items&action=view&id=' . $contribution['item']['id'] . '">' . $contribution['item']['name'] . '</a></div>';
        $html .= '</div>';
        break;
    }
    
    $html .= '</div>';
  }
  
  return $html;
}
?>

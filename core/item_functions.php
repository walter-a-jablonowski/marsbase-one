<?php
/**
 * Item utility functions
 *
 * Note: Core functions like getAllItems(), getItem(), and saveItem() are already
 * defined in core/functions.php. This file extends those with additional
 * item-specific functionality.
 */

/**
 * Get all items with scores
 * 
 * This extends the core getAllItems() function by calculating scores for each item
 */
function getAllItemsWithScores()
{
  $items = [];
  $itemsDir = DATA_DIR . '/items';
  
  if( !is_dir($itemsDir) )
  {
    return $items;
  }
  
  $files = scandir($itemsDir);
  
  foreach( $files as $file )
  {
    if( $file === '.' || $file === '..' )
    {
      continue;
    }
    
    $filePath = $itemsDir . '/' . $file;
    
    if( is_file($filePath) && pathinfo($filePath, PATHINFO_EXTENSION) === 'yaml' )
    {
      $item = loadYamlFile($filePath);
      
      if( !empty($item) )
      {
        // Calculate score
        $item['score'] = calculateScore($item);
        $items[] = $item;
      }
    }
  }
  
  return $items;
}

/**
 * Get item by ID with score calculated
 */
function getItemWithScore($id)
{
  $filePath = DATA_DIR . '/items/' . $id . '.yaml';
  
  if( !file_exists($filePath) )
  {
    return null;
  }
  
  $item = loadYamlFile($filePath);
  
  if( !empty($item) )
  {
    // Calculate score
    $item['score'] = calculateScore($item);
  }
  
  return $item;
}

// Note: saveItem function is already defined in core/functions.php

/**
 * Delete item
 */
function deleteItem($id)
{
  $filePath = DATA_DIR . '/items/' . $id . '.yaml';
  
  if( file_exists($filePath) )
  {
    return unlink($filePath);
  }
  
  return false;
}

/**
 * Get items that fulfill a specific requirement
 */
function getItemsForRequirement($requirementId)
{
  $items = [];
  $allItems = getAllItemsWithScores();
  
  foreach( $allItems as $item )
  {
    if( isset($item['requirementIds']) && in_array($requirementId, $item['requirementIds']) )
    {
      $items[] = $item;
    }
  }
  
  return $items;
}

/**
 * Get top items by score
 */
function getTopItems($limit = 5)
{
  $items = getAllItemsWithScores();
  
  // Sort by score
  usort($items, function($a, $b) {
    return ($b['score'] ?? 0) - ($a['score'] ?? 0);
  });
  
  return array_slice($items, 0, $limit);
}

/**
 * Process contribution to an item
 */
function addContribution($itemId, $userId, $amount)
{
  $item = getItemWithScore($itemId);
  
  if( empty($item) )
  {
    return false;
  }
  
  // Add contribution
  $contribution = [
    'user' => $userId,
    'time' => getCurrentTimestamp(),
    'amount' => $amount
  ];
  
  if( !isset($item['contributions']) )
  {
    $item['contributions'] = [];
  }
  
  $item['contributions'][] = $contribution;
  
  // Calculate current funding
  $currentFunding = 0;
  foreach( $item['contributions'] as $c )
  {
    $currentFunding += $c['amount'];
  }
  
  $item['currentFunding'] = $currentFunding;
  $item['modifiedAt'] = getCurrentTimestamp();
  
  return saveItem($item);
}

/**
 * Get funding percentage for an item
 */
function getFundingPercentage($item)
{
  if( empty($item['fundingGoal']) || $item['fundingGoal'] <= 0 )
  {
    return 0;
  }
  
  $currentFunding = $item['currentFunding'] ?? 0;
  $percentage = ($currentFunding / $item['fundingGoal']) * 100;
  
  return min(100, $percentage);
}

/**
 * Get items by project lead
 */
function getItemsByProjectLead($userId)
{
  $items = [];
  $allItems = getAllItemsWithScores();
  
  foreach( $allItems as $item )
  {
    if( isset($item['projectLead']) && $item['projectLead'] === $userId )
    {
      $items[] = $item;
    }
  }
  
  return $items;
}

/**
 * Get items created by user
 */
function getItemsByUser($userId)
{
  $items = [];
  $allItems = getAllItemsWithScores();
  
  foreach( $allItems as $item )
  {
    if( isset($item['createdBy']) && $item['createdBy'] === $userId )
    {
      $items[] = $item;
    }
  }
  
  return $items;
}

/**
 * Get items followed by user
 */
function getItemsFollowedByUser($userId)
{
  $user = getUser($userId);
  
  if( empty($user) || empty($user['itemsFollowing']) )
  {
    return [];
  }
  
  $items = [];
  $allItems = getAllItemsWithScores();
  
  foreach( $allItems as $item )
  {
    if( in_array($item['id'], $user['itemsFollowing']) )
    {
      $items[] = $item;
    }
  }
  
  return $items;
}
?>

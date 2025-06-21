<?php
/**
 * AJAX Handler
 * Processes asynchronous requests from the client
 */

// Initialize session
session_start();

// Include core files
require_once __DIR__ . '/core/config.php';
require_once __DIR__ . '/core/functions.php';
require_once __DIR__ . '/core/auth.php';
require_once __DIR__ . '/core/item_functions.php';
require_once __DIR__ . '/core/requirement_functions.php';
require_once __DIR__ . '/core/user_functions.php';

// Get JSON request data
$requestData = json_decode(file_get_contents('php://input'), true);
$action = $requestData['action'] ?? '';

// Initialize auth
$auth = new Auth();

// Response array
$response = [
  'success' => false,
  'message' => 'Invalid action'
];

// Process action
switch( $action )
{
  case 'vote':
    // Check if user is logged in
    if( !$auth->isLoggedIn() )
    {
      $response = [
        'success' => false,
        'message' => 'Please login to vote'
      ];
      break;
    }
    
    $itemId = $requestData['itemId'] ?? '';
    $itemType = $requestData['itemType'] ?? '';
    $voteType = $requestData['voteType'] ?? '';
    
    if( empty($itemId) || empty($itemType) || empty($voteType) )
    {
      $response = [
        'success' => false,
        'message' => 'Missing required parameters'
      ];
      break;
    }
    
    // Process vote
    if( $itemType === 'item' )
    {
      $result = $auth->voteOnItem($itemId, $voteType);
      
      if( $result['success'] )
      {
        $item = getItemWithScore($itemId);
        $response = [
          'success' => true,
          'message' => $result['message'],
          'score' => $item['score'] ?? 0
        ];
      }
      else
      {
        $response = $result;
      }
    }
    elseif( $itemType === 'requirement' )
    {
      $result = $auth->voteOnRequirement($itemId, $voteType);
      
      if( $result['success'] )
      {
        $requirement = getRequirementWithScore($itemId);
        $response = [
          'success' => true,
          'message' => $result['message'],
          'score' => $requirement['score'] ?? 0
        ];
      }
      else
      {
        $response = $result;
      }
    }
    break;
    
  case 'follow':
    // Check if user is logged in
    if( !$auth->isLoggedIn() )
    {
      $response = [
        'success' => false,
        'message' => 'Please login to follow'
      ];
      break;
    }
    
    $itemId = $requestData['itemId'] ?? '';
    $itemType = $requestData['itemType'] ?? '';
    
    if( empty($itemId) || empty($itemType) )
    {
      $response = [
        'success' => false,
        'message' => 'Missing required parameters'
      ];
      break;
    }
    
    // Process follow
    $user = $auth->getCurrentUser();
    
    if( $itemType === 'item' )
    {
      $result = $auth->toggleFollowItem($itemId);
      $following = in_array($itemId, $result['user']['itemsFollowing'] ?? []);
    }
    elseif( $itemType === 'requirement' )
    {
      $result = $auth->toggleFollowRequirement($itemId);
      $following = in_array($itemId, $result['user']['requirementsFollowing'] ?? []);
    }
    elseif( $itemType === 'user' )
    {
      $result = $auth->toggleFollowUser($itemId);
      $following = in_array($itemId, $result['user']['usersFollowing'] ?? []);
    }
    else
    {
      $result = [
        'success' => false,
        'message' => 'Invalid item type'
      ];
    }
    
    if( $result['success'] )
    {
      $response = [
        'success' => true,
        'message' => $result['message'],
        'following' => $following
      ];
    }
    else
    {
      $response = $result;
    }
    break;
    
  case 'contribute':
    // Check if user is logged in
    if( !$auth->isLoggedIn() )
    {
      $response = [
        'success' => false,
        'message' => 'Please login to contribute'
      ];
      break;
    }
    
    $itemId = $requestData['itemId'] ?? '';
    $amount = floatval($requestData['amount'] ?? 0);
    
    if( empty($itemId) || $amount <= 0 )
    {
      $response = [
        'success' => false,
        'message' => 'Invalid contribution parameters'
      ];
      break;
    }
    
    // Get item
    $item = getItemWithScore($itemId);
    
    if( empty($item) )
    {
      $response = [
        'success' => false,
        'message' => 'Item not found'
      ];
      break;
    }
    
    // Add contribution
    $user = $auth->getCurrentUser();
    
    // Use the utility function to add contribution
    if( addContribution($itemId, $user['id'], $amount) )
    {
      // Get updated item
      $item = getItemWithScore($itemId);
      $currentFunding = $item['currentFunding'] ?? 0;
      
      // Calculate percentage
      $percentage = getFundingPercentage($item);
      
      $response = [
        'success' => true,
        'message' => 'Contribution successful',
        'current' => $currentFunding,
        'goal' => $item['fundingGoal'],
        'percentage' => round($percentage)
      ];
    }
    else
    {
      $response = [
        'success' => false,
        'message' => 'Failed to process contribution'
      ];
    }
    break;
    
  default:
    $response = [
      'success' => false,
      'message' => 'Invalid action'
    ];
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>

<?php
/**
 * Authentication and user management
 */

class Auth
{
  /**
   * Register a new user
   */
  public function register( $userData ) : array
  {
    // Check if email already exists
    $existingUser = getUser($userData['email']);
    if( !empty($existingUser) )
    {
      return [
        'success' => false,
        'message' => 'Email already registered'
      ];
    }
    
    // Create new user
    $user = [
      'id' => $userData['email'],
      'type' => $userData['type'] ?? 'person',
      'password' => password_hash($userData['password'], PASSWORD_DEFAULT),
      'name' => $userData['name'],
      'memberIds' => [],
      'image' => '',
      'bio' => $userData['bio'] ?? '',
      'expertise' => $userData['expertise'] ?? '',
      'location' => $userData['location'] ?? 'Earth',
      'website' => $userData['website'] ?? '',
      'socialUrl' => $userData['socialUrl'] ?? '',
      'itemsFollowing' => [],
      'requirementsFollowing' => [],
      'usersFollowing' => [],
      'itemScore' => [],
      'reqScore' => [],
      'modifiedAt' => getCurrentTimestamp()
    ];
    
    // Save user
    if( saveUser($user) )
    {
      return [
        'success' => true,
        'message' => 'Registration successful',
        'user' => $user
      ];
    }
    
    return [
      'success' => false,
      'message' => 'Failed to create user'
    ];
  }
  
  /**
   * Login a user
   */
  public function login( $email, $password ) : array
  {
    $user = getUser($email);
    
    if( empty($user) || !password_verify($password, $user['password']) )
    {
      return [
        'success' => false,
        'message' => 'Invalid email or password'
      ];
    }
    
    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_type'] = $user['type'];
    
    return [
      'success' => true,
      'message' => 'Login successful',
      'user' => $user
    ];
  }
  
  /**
   * Logout the current user
   */
  public function logout()
  {
    // Destroy session
    session_unset();
    session_destroy();
    
    return [
      'success' => true,
      'message' => 'Logout successful'
    ];
  }
  
  /**
   * Check if user is logged in
   */
  public function isLoggedIn() : bool
  {
    return isset($_SESSION['user_id']);
  }
  
  /**
   * Get current user data
   */
  public function getCurrentUser() : array
  {
    if( !$this->isLoggedIn() )
    {
      return [];
    }
    
    return getUser($_SESSION['user_id']);
  }
  
  /**
   * Update user profile
   */
  public function updateProfile( $userData ) : array
  {
    if( !$this->isLoggedIn() )
    {
      return [
        'success' => false,
        'message' => 'Not logged in'
      ];
    }
    
    $user = $this->getCurrentUser();
    
    // Update fields
    $user['name'] = $userData['name'] ?? $user['name'];
    $user['bio'] = $userData['bio'] ?? $user['bio'];
    $user['expertise'] = $userData['expertise'] ?? $user['expertise'];
    $user['location'] = $userData['location'] ?? $user['location'];
    $user['modifiedAt'] = getCurrentTimestamp();
    
    // Handle image upload
    if( isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK )
    {
      $newImage = handleFileUpload($_FILES['image'], UPLOADS_DIR . '/images');
      if( $newImage )
      {
        $user['image'] = $newImage;
      }
    }
    
    // Save updated user
    if( saveUser($user) )
    {
      return [
        'success' => true,
        'message' => 'Profile updated successfully',
        'user' => $user
      ];
    }
    
    return [
      'success' => false,
      'message' => 'Failed to update profile'
    ];
  }
  
  /**
   * Follow/unfollow item
   */
  public function toggleFollowItem( $itemId ) : array
  {
    if( !$this->isLoggedIn() )
    {
      return [
        'success' => false,
        'message' => 'Not logged in'
      ];
    }
    
    $user = $this->getCurrentUser();
    
    if( in_array($itemId, $user['itemsFollowing']) )
    {
      // Unfollow
      $user['itemsFollowing'] = array_diff($user['itemsFollowing'], [$itemId]);
      $action = 'unfollowed';
    }
    else
    {
      // Follow
      $user['itemsFollowing'][] = $itemId;
      $action = 'followed';
    }
    
    $user['modifiedAt'] = getCurrentTimestamp();
    
    if( saveUser($user) )
    {
      return [
        'success' => true,
        'message' => "Item $action successfully",
        'user' => $user
      ];
    }
    
    return [
      'success' => false,
      'message' => "Failed to $action item"
    ];
  }
  
  /**
   * Follow/unfollow requirement
   */
  public function toggleFollowRequirement( $reqId ) : array
  {
    if( !$this->isLoggedIn() )
    {
      return [
        'success' => false,
        'message' => 'Not logged in'
      ];
    }
    
    $user = $this->getCurrentUser();
    
    if( in_array($reqId, $user['requirementsFollowing']) )
    {
      // Unfollow
      $user['requirementsFollowing'] = array_diff($user['requirementsFollowing'], [$reqId]);
      $action = 'unfollowed';
    }
    else
    {
      // Follow
      $user['requirementsFollowing'][] = $reqId;
      $action = 'followed';
    }
    
    $user['modifiedAt'] = getCurrentTimestamp();
    
    if( saveUser($user) )
    {
      return [
        'success' => true,
        'message' => "Requirement $action successfully",
        'user' => $user
      ];
    }
    
    return [
      'success' => false,
      'message' => "Failed to $action requirement"
    ];
  }
  
  /**
   * Follow/unfollow user
   */
  public function toggleFollowUser( $userId ) : array
  {
    if( !$this->isLoggedIn() )
    {
      return [
        'success' => false,
        'message' => 'Not logged in'
      ];
    }
    
    $user = $this->getCurrentUser();
    
    // Can't follow yourself
    if( $userId === $user['id'] )
    {
      return [
        'success' => false,
        'message' => 'Cannot follow yourself'
      ];
    }
    
    if( in_array($userId, $user['usersFollowing']) )
    {
      // Unfollow
      $user['usersFollowing'] = array_diff($user['usersFollowing'], [$userId]);
      $action = 'unfollowed';
    }
    else
    {
      // Follow
      $user['usersFollowing'][] = $userId;
      $action = 'followed';
    }
    
    $user['modifiedAt'] = getCurrentTimestamp();
    
    if( saveUser($user) )
    {
      return [
        'success' => true,
        'message' => "User $action successfully",
        'user' => $user
      ];
    }
    
    return [
      'success' => false,
      'message' => "Failed to $action user"
    ];
  }
  
  /**
   * Vote on item
   */
  public function voteOnItem( $itemId, $vote ) : array
  {
    if( !$this->isLoggedIn() )
    {
      return [
        'success' => false,
        'message' => 'Not logged in'
      ];
    }
    
    $user = $this->getCurrentUser();
    $voteValue = ($vote === 'up') ? 1 : -1;
    
    // Check if user already voted
    $existingVote = false;
    foreach( $user['itemScore'] as $key => $scoreItem )
    {
      if( isset($scoreItem[$itemId]) )
      {
        // If same vote, remove it (toggle)
        if( $scoreItem[$itemId] === $voteValue )
        {
          unset($user['itemScore'][$key]);
          $existingVote = true;
          $action = 'removed';
        }
        else
        {
          // Change vote
          $user['itemScore'][$key][$itemId] = $voteValue;
          $existingVote = true;
          $action = 'changed';
        }
        break;
      }
    }
    
    // Add new vote if not already voted
    if( !$existingVote )
    {
      $user['itemScore'][] = [$itemId => $voteValue];
      $action = 'added';
    }
    
    $user['modifiedAt'] = getCurrentTimestamp();
    
    // Update item score
    $item = getItem($itemId);
    if( !empty($item) )
    {
      // Get all votes for this item from all users
      $allUsers = getAllUsers();
      $votes = [];
      
      foreach( $allUsers as $u )
      {
        foreach( $u['itemScore'] as $scoreItem )
        {
          if( isset($scoreItem[$itemId]) )
          {
            $votes[] = [$itemId => $scoreItem[$itemId]];
          }
        }
      }
      
      $item['score'] = calculateScore($votes);
      saveItem($item);
    }
    
    if( saveUser($user) )
    {
      return [
        'success' => true,
        'message' => "Vote $action successfully",
        'user' => $user
      ];
    }
    
    return [
      'success' => false,
      'message' => "Failed to $action vote"
    ];
  }
  
  /**
   * Vote on requirement
   */
  public function voteOnRequirement( $reqId, $vote ) : array
  {
    if( !$this->isLoggedIn() )
    {
      return [
        'success' => false,
        'message' => 'Not logged in'
      ];
    }
    
    $user = $this->getCurrentUser();
    $voteValue = ($vote === 'up') ? 1 : -1;
    
    // Check if user already voted
    $existingVote = false;
    foreach( $user['reqScore'] as $key => $scoreItem )
    {
      if( isset($scoreItem[$reqId]) )
      {
        // If same vote, remove it (toggle)
        if( $scoreItem[$reqId] === $voteValue )
        {
          unset($user['reqScore'][$key]);
          $existingVote = true;
          $action = 'removed';
        }
        else
        {
          // Change vote
          $user['reqScore'][$key][$reqId] = $voteValue;
          $existingVote = true;
          $action = 'changed';
        }
        break;
      }
    }
    
    // Add new vote if not already voted
    if( !$existingVote )
    {
      $user['reqScore'][] = [$reqId => $voteValue];
      $action = 'added';
    }
    
    $user['modifiedAt'] = getCurrentTimestamp();
    
    // Update requirement score
    $requirement = getRequirement($reqId);
    if( !empty($requirement) )
    {
      // Get all votes for this requirement from all users
      $allUsers = getAllUsers();
      $votes = [];
      
      foreach( $allUsers as $u )
      {
        foreach( $u['reqScore'] as $scoreItem )
        {
          if( isset($scoreItem[$reqId]) )
          {
            $votes[] = [$reqId => $scoreItem[$reqId]];
          }
        }
      }
      
      $requirement['score'] = calculateScore($votes);
      saveRequirement($requirement);
    }
    
    if( saveUser($user) )
    {
      return [
        'success' => true,
        'message' => "Vote $action successfully",
        'user' => $user
      ];
    }
    
    return [
      'success' => false,
      'message' => "Failed to $action vote"
    ];
  }
}
?>

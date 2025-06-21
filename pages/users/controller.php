<?php
/**
 * Users controller
 */

class UsersController extends BaseController
{
  /**
   * View a user profile
   */
  public function viewAction()
  {
    $id = $_GET['id'] ?? '';
    
    if( empty($id) )
    {
      $_SESSION['message'] = 'User ID not provided';
      $_SESSION['message_type'] = 'danger';
      redirect('index.php');
    }
    
    // Get user
    $profileUser = getUser($id);
    
    if( empty($profileUser) )
    {
      $_SESSION['message'] = 'User not found';
      $_SESSION['message_type'] = 'danger';
      redirect('index.php');
    }
    
    // Get requirements created by user
    $requirements = [];
    $allRequirements = getAllRequirements();
    foreach( $allRequirements as $req )
    {
      if( isset($req['createdBy']) && $req['createdBy'] === $id )
      {
        $requirements[] = $req;
      }
    }
    
    // Get items created by user
    $items = [];
    $allItems = getAllItems();
    foreach( $allItems as $item )
    {
      if( isset($item['createdBy']) && $item['createdBy'] === $id )
      {
        $items[] = $item;
      }
    }
    
    // Get items where user is project lead
    $leadingItems = [];
    foreach( $allItems as $item )
    {
      if( isset($item['projectLead']) && $item['projectLead'] === $id )
      {
        $leadingItems[] = $item;
      }
    }
    
    // Check if current user is following this user
    $isFollowing = false;
    if( $this->auth->isLoggedIn() )
    {
      $isFollowing = in_array($id, $this->user['usersFollowing'] ?? []);
    }
    
    // Render view
    $this->render(__DIR__ . '/view.php', [
      'profileUser' => $profileUser,
      'requirements' => $requirements,
      'items' => $items,
      'leadingItems' => $leadingItems,
      'isLoggedIn' => $this->auth->isLoggedIn(),
      'isFollowing' => $isFollowing,
      'user' => $this->user
    ]);
  }
  
  /**
   * Edit user profile
   */
  public function editAction()
  {
    // Check if user is logged in
    $this->requireLogin();
    
    $error = '';
    $success = '';
    
    // Get current user data
    $profileUser = $this->user;
    
    // Process form submission
    if( $_SERVER['REQUEST_METHOD'] === 'POST' )
    {
      // Get form data
      $name = $_POST['name'] ?? '';
      $bio = $_POST['bio'] ?? '';
      $expertise = $_POST['expertise'] ?? '';
      $location = $_POST['location'] ?? '';
      $website = $_POST['website'] ?? '';
      $social = $_POST['social'] ?? '';
      $currentPassword = $_POST['currentPassword'] ?? '';
      $newPassword = $_POST['newPassword'] ?? '';
      $confirmPassword = $_POST['confirmPassword'] ?? '';
      
      // Validate input
      if( empty($name) )
      {
        $error = 'Name is required';
      }
      else
      {
        // Update user data
        $profileUser['name'] = $name;
        $profileUser['bio'] = $bio;
        $profileUser['expertise'] = $expertise;
        $profileUser['location'] = $location;
        $profileUser['website'] = $website;
        $profileUser['social'] = $social;
        
        // Handle password change if provided
        if( !empty($currentPassword) && !empty($newPassword) )
        {
          if( $newPassword !== $confirmPassword )
          {
            $error = 'New passwords do not match';
          }
          elseif( !password_verify($currentPassword, $profileUser['password']) )
          {
            $error = 'Current password is incorrect';
          }
          else
          {
            $profileUser['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
          }
        }
        
        // Handle profile image upload
        if( isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] === UPLOAD_ERR_OK )
        {
          $newImage = handleFileUpload($_FILES['profileImage'], UPLOADS_DIR . '/profiles');
          if( $newImage )
          {
            $profileUser['profileImage'] = $newImage;
          }
        }
        
        // Save user if no errors
        if( empty($error) )
        {
          if( saveUser($profileUser) )
          {
            // Update session
            $_SESSION['user'] = $profileUser;
            $success = 'Profile updated successfully';
          }
          else
          {
            $error = 'Failed to update profile';
          }
        }
      }
    }
    
    // Render view
    $this->render(__DIR__ . '/edit.php', [
      'profileUser' => $profileUser,
      'error' => $error,
      'success' => $success
    ]);
  }
  
  /**
   * List all users
   */
  public function indexAction()
  {
    // Get all users
    $users = getAllUsers();
    
    // Render view
    $this->render(__DIR__ . '/list.php', [
      'users' => $users,
      'isLoggedIn' => $this->auth->isLoggedIn(),
      'user' => $this->user
    ]);
  }
  
  /**
   * Dashboard for logged in user
   */
  public function dashboardAction()
  {
    // Check if user is logged in
    $this->requireLogin();
    
    // Get requirements created by user
    $requirements = [];
    $allRequirements = getAllRequirements();
    foreach( $allRequirements as $req )
    {
      if( isset($req['createdBy']) && $req['createdBy'] === $this->user['id'] )
      {
        $requirements[] = $req;
      }
    }
    
    // Get items created by user
    $items = [];
    $allItems = getAllItems();
    foreach( $allItems as $item )
    {
      if( isset($item['createdBy']) && $item['createdBy'] === $this->user['id'] )
      {
        $items[] = $item;
      }
    }
    
    // Get followed requirements
    $followedRequirements = [];
    if( !empty($this->user['requirementsFollowing']) )
    {
      foreach( $allRequirements as $req )
      {
        if( in_array($req['id'], $this->user['requirementsFollowing']) )
        {
          $followedRequirements[] = $req;
        }
      }
    }
    
    // Get followed items
    $followedItems = [];
    if( !empty($this->user['itemsFollowing']) )
    {
      foreach( $allItems as $item )
      {
        if( in_array($item['id'], $this->user['itemsFollowing']) )
        {
          $followedItems[] = $item;
        }
      }
    }
    
    // Get followed users
    $followedUsers = [];
    if( !empty($this->user['usersFollowing']) )
    {
      $allUsers = getAllUsers();
      foreach( $allUsers as $u )
      {
        if( in_array($u['id'], $this->user['usersFollowing']) )
        {
          $followedUsers[] = $u;
        }
      }
    }
    
    // Get contributions
    $contributions = [];
    foreach( $allItems as $item )
    {
      if( !empty($item['contributions']) )
      {
        foreach( $item['contributions'] as $contribution )
        {
          if( $contribution['user'] === $this->user['id'] )
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
    
    // Sort contributions by time (newest first)
    usort($contributions, function($a, $b) {
      return $b['time'] - $a['time'];
    });
    
    // Render view
    $this->render(__DIR__ . '/dashboard.php', [
      'requirements' => $requirements,
      'items' => $items,
      'followedRequirements' => $followedRequirements,
      'followedItems' => $followedItems,
      'followedUsers' => $followedUsers,
      'contributions' => $contributions
    ]);
  }
}
?>

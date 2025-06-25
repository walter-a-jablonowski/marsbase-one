<?php
/**
 * Authentication controller
 */

class AuthController extends BaseController
{
  /**
   * Login page
   */
  public function loginAction()
  {
    // Check if already logged in
    if( $this->auth->isLoggedIn() )
    {
      redirect('index.php');
    }
    
    $error = '';
    
    // Process login form
    if( $_SERVER['REQUEST_METHOD'] === 'POST' )
    {
      $email = $_POST['email'] ?? '';
      $password = $_POST['password'] ?? '';
      
      if( empty($email) || empty($password) )
      {
        $error = 'Please enter both email and password';
      }
      else
      {
        $result = $this->auth->login($email, $password);
        
        if( $result['success'] )
        {
          $_SESSION['message'] = 'Login successful';
          $_SESSION['message_type'] = 'success';
          redirect('index.php');
        }
        else
        {
          $error = $result['message'];
        }
      }
    }
    
    // Render login form
    $this->render(__DIR__ . '/login.php', [
      'error' => $error
    ]);
  }
  
  /**
   * Registration page
   */
  public function registerAction()
  {
    // Check if already logged in
    if( $this->auth->isLoggedIn() )
    {
      redirect('index.php');
    }
    
    $error = '';
    $userData = [
      'email' => '',
      'name' => '',
      'type' => 'person',
      'bio' => '',
      'expertise' => '',
      'location' => 'Earth'
    ];
    
    // Process registration form
    if( $_SERVER['REQUEST_METHOD'] === 'POST' )
    {
      $userData = [
        'email' => $_POST['email'] ?? '',
        'password' => $_POST['password'] ?? '',
        'confirm_password' => $_POST['confirm_password'] ?? '',
        'name' => $_POST['name'] ?? '',
        'type' => $_POST['type'] ?? 'person',
        'bio' => $_POST['bio'] ?? '',
        'expertise' => $_POST['expertise'] ?? '',
        'location' => $_POST['location'] ?? 'Earth'
      ];
      
      // Validate input
      if( empty($userData['email']) || empty($userData['password']) || 
          empty($userData['confirm_password']) || empty($userData['name']) )
      {
        $error = 'Please fill in all required fields';
      }
      elseif( !filter_var($userData['email'], FILTER_VALIDATE_EMAIL) )
      {
        $error = 'Please enter a valid email address';
      }
      elseif( $userData['password'] !== $userData['confirm_password'] )
      {
        $error = 'Passwords do not match';
      }
      elseif( strlen($userData['password']) < 6 )
      {
        $error = 'Password must be at least 6 characters long';
      }
      else
      {
        $result = $this->auth->register($userData);
        
        if( $result['success'] )
        {
          $_SESSION['message'] = 'Registration successful. Please log in.';
          $_SESSION['message_type'] = 'success';
          redirect('index.php?page=auth&action=login');
        }
        else
        {
          $error = $result['message'];
        }
      }
    }
    
    // Render registration form
    $this->render(__DIR__ . '/register.php', [
      'error' => $error,
      'userData' => $userData
    ]);
  }
  
  /**
   * Logout action
   */
  public function logoutAction()
  {
    $this->auth->logout();
    
    $_SESSION['message'] = 'You have been logged out';
    $_SESSION['message_type'] = 'info';
    
    redirect('index.php');
  }
}
?>

<?php
use Marsbase\Core\Auth;
use Marsbase\Core\Utils;

// Check if already logged in
$auth = Auth::getInstance();
if( $auth->isLoggedIn() )
{
  Utils::redirect('index.php');
}

// Handle registration form submission
if( $_SERVER['REQUEST_METHOD'] === 'POST' )
{
  $name = $_POST['name'] ?? '';
  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';
  $confirmPassword = $_POST['confirm_password'] ?? '';
  $type = $_POST['type'] ?? 'person';
  
  // Validate input
  $errors = [];
  
  if( empty($name) )
  {
    $errors[] = 'Name is required.';
  }
  
  if( empty($email) )
  {
    $errors[] = 'Email is required.';
  }
  else if( !filter_var($email, FILTER_VALIDATE_EMAIL) )
  {
    $errors[] = 'Invalid email format.';
  }
  
  if( empty($password) )
  {
    $errors[] = 'Password is required.';
  }
  else if( strlen($password) < 6 )
  {
    $errors[] = 'Password must be at least 6 characters.';
  }
  
  if( $password !== $confirmPassword )
  {
    $errors[] = 'Passwords do not match.';
  }
  
  if( !in_array($type, ['person', 'organization']) )
  {
    $errors[] = 'Invalid account type.';
  }
  
  if( empty($errors) )
  {
    // Create user
    $userData = [
      'name' => $name,
      'email' => $email,
      'password' => $password,
      'type' => $type,
      'bio' => '',
      'expertise' => '',
      'location' => '',
      'image' => ''
    ];
    
    if( $auth->register($userData) )
    {
      Utils::setFlashMessage('Your account has been created successfully.');
      Utils::redirect('index.php');
    }
    else
    {
      Utils::setFlashError('An error occurred while creating your account. Email may already be in use.');
    }
  }
  else
  {
    Utils::setFlashError(implode('<br>', $errors));
  }
}
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
      <div class="card shadow">
        <div class="card-header bg-mars text-white text-center py-3">
          <h4 class="mb-0">Join MarsBase.One</h4>
        </div>
        <div class="card-body p-4">
          <form method="post" action="index.php?page=register">
            <div class="mb-3">
              <label class="form-label">Account Type</label>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="type" id="typePerson" value="person" checked>
                <label class="form-check-label" for="typePerson">
                  <i class="fas fa-user me-1"></i>Individual
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="type" id="typeOrg" value="organization">
                <label class="form-check-label" for="typeOrg">
                  <i class="fas fa-building me-1"></i>Organization
                </label>
              </div>
            </div>
            
            <div class="mb-3">
              <label for="name" class="form-label">Name</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input type="text" class="form-control" id="name" name="name" required>
              </div>
              <div class="form-text">For individuals: your full name. For organizations: organization name.</div>
            </div>
            
            <div class="mb-3">
              <label for="email" class="form-label">Email Address</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" class="form-control" id="email" name="email" required>
              </div>
            </div>
            
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control" id="password" name="password" required>
              </div>
              <div class="form-text">Must be at least 6 characters.</div>
            </div>
            
            <div class="mb-3">
              <label for="confirm_password" class="form-label">Confirm Password</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
              </div>
            </div>
            
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-user-plus me-2"></i>Create Account
              </button>
            </div>
          </form>
        </div>
        <div class="card-footer bg-light text-center py-3">
          <p class="mb-0">Already have an account? <a href="index.php?page=login">Login</a></p>
        </div>
      </div>
    </div>
  </div>
</div>

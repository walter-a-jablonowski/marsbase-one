<?php
use Marsbase\Core\Auth;
use Marsbase\Core\Utils;

// Check if already logged in
$auth = Auth::getInstance();
if( $auth->isLoggedIn() )
{
  Utils::redirect('index.php');
}

// Handle login form submission
if( $_SERVER['REQUEST_METHOD'] === 'POST' )
{
  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';
  
  if( empty($email) || empty($password) )
  {
    Utils::setFlashError('Please enter both email and password.');
  }
  else if( $auth->login($email, $password) )
  {
    Utils::setFlashMessage('You have been successfully logged in.');
    Utils::redirect('index.php');
  }
  else
  {
    Utils::setFlashError('Invalid email or password.');
  }
}
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card shadow">
        <div class="card-header bg-mars text-white text-center py-3">
          <h4 class="mb-0">Login to MarsBase.One</h4>
        </div>
        <div class="card-body p-4">
          <form method="post" action="index.php?page=login">
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
            </div>
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-sign-in-alt me-2"></i>Login
              </button>
            </div>
          </form>
        </div>
        <div class="card-footer bg-light text-center py-3">
          <p class="mb-0">Don't have an account? <a href="index.php?page=register">Register</a></p>
        </div>
      </div>
    </div>
  </div>
</div>

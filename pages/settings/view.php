<?php
use Marsbase\Core\Auth;
use Marsbase\Core\Utils;
use Marsbase\Core\FileUpload;

// Check if user is logged in
$auth = Auth::getInstance();
if( !$auth->isLoggedIn() )
{
  Utils::redirect('index.php?page=login');
}

$user = $auth->getUser();
$userId = $user->getId();

// Handle form submission
if( $_SERVER['REQUEST_METHOD'] === 'POST' )
{
  $action = $_POST['action'] ?? '';
  
  if( $action === 'profile' )
  {
    // Update profile information
    $name = $_POST['name'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $expertise = $_POST['expertise'] ?? '';
    $location = $_POST['location'] ?? '';
    
    // Validate input
    $errors = [];
    
    if( empty($name) )
    {
      $errors[] = 'Name is required.';
    }
    
    if( empty($errors) )
    {
      // Update user data
      $user->set('name', $name);
      $user->set('bio', $bio);
      $user->set('expertise', $expertise);
      $user->set('location', $location);
      
      // Handle image upload
      if( !empty($_FILES['image']['name']) )
      {
        $fileUpload = new FileUpload();
        $uploadResult = $fileUpload->uploadImage('image', "data/users/{$userId}/uploads");
        
        if( $uploadResult['success'] )
        {
          $user->set('image', $uploadResult['filename']);
        }
        else
        {
          $errors[] = $uploadResult['error'];
        }
      }
      
      if( empty($errors) )
      {
        // Save user data
        if( $user->save() )
        {
          Utils::setFlashMessage('Profile updated successfully.');
          Utils::redirect('index.php?page=settings');
        }
        else
        {
          Utils::setFlashError('An error occurred while saving your profile.');
        }
      }
      else
      {
        Utils::setFlashError(implode('<br>', $errors));
      }
    }
    else
    {
      Utils::setFlashError(implode('<br>', $errors));
    }
  }
  else if( $action === 'password' )
  {
    // Change password
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validate input
    $errors = [];
    
    if( empty($currentPassword) )
    {
      $errors[] = 'Current password is required.';
    }
    else if( !$user->verifyPassword($currentPassword) )
    {
      $errors[] = 'Current password is incorrect.';
    }
    
    if( empty($newPassword) )
    {
      $errors[] = 'New password is required.';
    }
    else if( strlen($newPassword) < 6 )
    {
      $errors[] = 'New password must be at least 6 characters.';
    }
    
    if( $newPassword !== $confirmPassword )
    {
      $errors[] = 'New passwords do not match.';
    }
    
    if( empty($errors) )
    {
      // Update password
      $user->setPassword($newPassword);
      
      // Save user data
      if( $user->save() )
      {
        Utils::setFlashMessage('Password changed successfully.');
        Utils::redirect('index.php?page=settings');
      }
      else
      {
        Utils::setFlashError('An error occurred while changing your password.');
      }
    }
    else
    {
      Utils::setFlashError(implode('<br>', $errors));
    }
  }
}
?>

<div class="container py-4">
  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active" aria-current="page">Settings</li>
    </ol>
  </nav>
  
  <!-- Page Header -->
  <h1 class="mb-4">Account Settings</h1>
  
  <!-- Settings Tabs -->
  <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" 
              type="button" role="tab" aria-controls="profile" aria-selected="true">
        <i class="fas fa-user me-2"></i>Profile
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" 
              type="button" role="tab" aria-controls="password" aria-selected="false">
        <i class="fas fa-lock me-2"></i>Password
      </button>
    </li>
  </ul>
  
  <!-- Tab Content -->
  <div class="tab-content" id="settingsTabsContent">
    <!-- Profile Tab -->
    <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
      <div class="card">
        <div class="card-body">
          <form method="post" action="index.php?page=settings" enctype="multipart/form-data">
            <input type="hidden" name="action" value="profile">
            
            <div class="row mb-3">
              <div class="col-md-3 text-center">
                <div class="mb-3">
                  <?php if( $user->get('image') ): ?>
                    <img src="data/users/<?= $userId ?>/uploads/<?= $user->get('image') ?>" 
                         class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;" 
                         alt="<?= htmlspecialchars($user->get('name')) ?>">
                  <?php else: ?>
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto img-thumbnail" 
                         style="width: 150px; height: 150px;">
                      <i class="fas <?= $user->get('type') === 'person' ? 'fa-user' : 'fa-building' ?> fa-4x text-secondary"></i>
                    </div>
                  <?php endif; ?>
                </div>
                
                <div class="mb-3">
                  <label for="image" class="form-label">Profile Image</label>
                  <input type="file" class="form-control" id="image" name="image" accept="image/jpeg,image/png,image/jpg">
                  <div class="form-text">Max size: 2MB. JPG or PNG only.</div>
                </div>
              </div>
              
              <div class="col-md-9">
                <div class="mb-3">
                  <label for="name" class="form-label">Name</label>
                  <input type="text" class="form-control" id="name" name="name" 
                         value="<?= htmlspecialchars($user->get('name')) ?>" required>
                  <div class="form-text">
                    <?= $user->get('type') === 'person' ? 'Your full name.' : 'Organization name.' ?>
                  </div>
                </div>
                
                <div class="mb-3">
                  <label for="bio" class="form-label">Bio</label>
                  <textarea class="form-control" id="bio" name="bio" rows="3"><?= htmlspecialchars($user->get('bio')) ?></textarea>
                  <div class="form-text">
                    <?= $user->get('type') === 'person' ? 'A short biography about yourself.' : 'A brief description of your organization.' ?>
                  </div>
                </div>
                
                <div class="mb-3">
                  <label for="expertise" class="form-label">Expertise</label>
                  <input type="text" class="form-control" id="expertise" name="expertise" 
                         value="<?= htmlspecialchars($user->get('expertise')) ?>">
                  <div class="form-text">
                    <?= $user->get('type') === 'person' ? 'Your skills and areas of expertise.' : 'Your organization\'s specialties.' ?>
                  </div>
                </div>
                
                <div class="mb-3">
                  <label for="location" class="form-label">Location</label>
                  <input type="text" class="form-control" id="location" name="location" 
                         value="<?= htmlspecialchars($user->get('location')) ?>">
                  <div class="form-text">Your Earth-based location.</div>
                </div>
              </div>
            </div>
            
            <div class="text-end">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Save Profile
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    
    <!-- Password Tab -->
    <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
      <div class="card">
        <div class="card-body">
          <form method="post" action="index.php?page=settings">
            <input type="hidden" name="action" value="password">
            
            <div class="mb-3">
              <label for="current_password" class="form-label">Current Password</label>
              <input type="password" class="form-control" id="current_password" name="current_password" required>
            </div>
            
            <div class="mb-3">
              <label for="new_password" class="form-label">New Password</label>
              <input type="password" class="form-control" id="new_password" name="new_password" required>
              <div class="form-text">Must be at least 6 characters.</div>
            </div>
            
            <div class="mb-3">
              <label for="confirm_password" class="form-label">Confirm New Password</label>
              <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            
            <div class="text-end">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-key me-2"></i>Change Password
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Handle tab selection from URL hash
    const hash = window.location.hash;
    if (hash) {
      const tab = document.querySelector(`#settingsTabs button[data-bs-target="${hash}"]`);
      if (tab) {
        const tabInstance = new bootstrap.Tab(tab);
        tabInstance.show();
      }
    }
    
    // Update URL hash when tab changes
    const tabs = document.querySelectorAll('#settingsTabs button');
    tabs.forEach(tab => {
      tab.addEventListener('shown.bs.tab', function(event) {
        const target = event.target.getAttribute('data-bs-target');
        window.location.hash = target;
      });
    });
  });
</script>

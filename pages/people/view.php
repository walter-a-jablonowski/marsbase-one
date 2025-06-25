<?php
use Marsbase\Models\User;
use Marsbase\Core\Auth;
use Marsbase\Core\Utils;

// Get all users
$users = User::findAll();

// Get filter parameters from request
$filter = $_GET['filter'] ?? '';
$type = $_GET['type'] ?? 'all';
$location = $_GET['location'] ?? '';

// Check if user is logged in
$auth = Auth::getInstance();
$isLoggedIn = $auth->isLoggedIn();

// Filter users based on parameters
$filteredUsers = array_filter($users, function($user) use ($filter, $type, $location) {
  // Filter by search term
  if( !empty($filter) ) {
    $name = $user->get('name', '');
    $bio = $user->get('bio', '');
    $expertise = $user->get('expertise', '');
    
    if( stripos($name, $filter) === false && 
        stripos($bio, $filter) === false && 
        stripos($expertise, $filter) === false ) {
      return false;
    }
  }
  
  // Filter by type
  if( $type !== 'all' && $user->get('type') !== $type ) {
    return false;
  }
  
  // Filter by location
  if( !empty($location) ) {
    $userLocation = $user->get('location', '');
    if( stripos($userLocation, $location) === false ) {
      return false;
    }
  }
  
  return true;
});

// Sort users by name
usort($filteredUsers, function($a, $b) {
  return strcasecmp($a->get('name', ''), $b->get('name', ''));
});
?>

<div class="container py-4">
  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active" aria-current="page">People</li>
    </ol>
  </nav>
  
  <!-- Page Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Mars Colony Contributors</h1>
  </div>
  
  <!-- Filters -->
  <div class="card mb-4">
    <div class="card-body">
      <form method="get" action="index.php" class="row g-3 align-items-end">
        <input type="hidden" name="page" value="people">
        
        <div class="col-md-4">
          <label for="filter" class="form-label">Search</label>
          <input type="text" class="form-control" id="filter" name="filter" 
                 value="<?= htmlspecialchars($filter) ?>">
        </div>
        
        <div class="col-md-3">
          <label for="type" class="form-label">Type</label>
          <select class="form-select" id="type" name="type">
            <option value="all" <?= $type === 'all' ? 'selected' : '' ?>>All</option>
            <option value="person" <?= $type === 'person' ? 'selected' : '' ?>>Individuals</option>
            <option value="organization" <?= $type === 'organization' ? 'selected' : '' ?>>Organizations</option>
          </select>
        </div>
        
        <div class="col-md-3">
          <label for="location" class="form-label">Location</label>
          <input type="text" class="form-control" id="location" name="location" 
                 value="<?= htmlspecialchars($location) ?>">
        </div>
        
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary w-100">
            <i class="fas fa-filter me-2"></i>Filter
          </button>
        </div>
      </form>
    </div>
  </div>
  
  <!-- People List -->
  <?php if( empty($filteredUsers) ): ?>
    <div class="alert alert-info">
      <i class="fas fa-info-circle me-2"></i>No people or organizations found matching your criteria.
    </div>
  <?php else: ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
      <?php foreach( $filteredUsers as $user ): ?>
        <div class="col">
          <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
              <span class="badge bg-<?= $user->get('type') === 'person' ? 'info' : 'primary' ?>">
                <?= $user->get('type') === 'person' ? 'Individual' : 'Organization' ?>
              </span>
            </div>
            
            <div class="card-body text-center">
              <div class="mb-3">
                <?php if( $user->get('image') ): ?>
                  <img src="data/users/<?= $user->getId() ?>/uploads/<?= $user->get('image') ?>" 
                       class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;" 
                       alt="<?= htmlspecialchars($user->get('name')) ?>">
                <?php else: ?>
                  <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto" 
                       style="width: 100px; height: 100px;">
                    <i class="fas <?= $user->get('type') === 'person' ? 'fa-user' : 'fa-building' ?> fa-3x text-secondary"></i>
                  </div>
                <?php endif; ?>
              </div>
              
              <h5 class="card-title"><?= htmlspecialchars($user->get('name')) ?></h5>
              
              <?php if( $user->get('location') ): ?>
                <p class="card-text text-muted mb-2">
                  <i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($user->get('location')) ?>
                </p>
              <?php endif; ?>
              
              <?php if( $user->get('expertise') ): ?>
                <p class="card-text small">
                  <strong>Expertise:</strong> <?= htmlspecialchars($user->get('expertise')) ?>
                </p>
              <?php endif; ?>
              
              <?php if( $user->get('bio') ): ?>
                <p class="card-text">
                  <?= htmlspecialchars(Utils::truncate($user->get('bio'), 100)) ?>
                </p>
              <?php endif; ?>
            </div>
            
            <div class="card-footer bg-transparent">
              <div class="d-flex justify-content-between align-items-center">
                <a href="index.php?page=profile&id=<?= $user->getId() ?>" class="btn btn-sm btn-outline-primary">
                  <i class="fas fa-user me-1"></i>View Profile
                </a>
                
                <?php if( $isLoggedIn && $auth->getUserId() !== $user->getId() ): ?>
                  <?php 
                    $currentUser = $auth->getUser();
                    $isFollowing = in_array($user->getId(), $currentUser->get('followedUserIds', []));
                  ?>
                  <button class="btn btn-sm <?= $isFollowing ? 'btn-success' : 'btn-outline-success' ?> follow-btn"
                          data-user-id="<?= $user->getId() ?>" data-following="<?= $isFollowing ? '1' : '0' ?>">
                    <i class="fas <?= $isFollowing ? 'fa-user-check' : 'fa-user-plus' ?> me-1"></i>
                    <?= $isFollowing ? 'Following' : 'Follow' ?>
                  </button>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Add logged-in class to body if user is logged in
    if (<?= $isLoggedIn ? 'true' : 'false' ?>) {
      document.body.classList.add('logged-in');
    }
    
    // Handle follow/unfollow buttons
    document.querySelectorAll('.follow-btn').forEach(button => {
      button.addEventListener('click', function() {
        if (!isLoggedIn()) {
          showLoginModal();
          return;
        }
        
        const userId = this.dataset.userId;
        const isFollowing = this.dataset.following === '1';
        
        fetch('ajax.php?action=users/follow', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            userId: userId,
            follow: !isFollowing
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Update button state
            this.dataset.following = isFollowing ? '0' : '1';
            this.classList.toggle('btn-outline-success', isFollowing);
            this.classList.toggle('btn-success', !isFollowing);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-user-plus', isFollowing);
            icon.classList.toggle('fa-user-check', !isFollowing);
            
            this.innerHTML = isFollowing ? 
              '<i class="fas fa-user-plus me-1"></i>Follow' : 
              '<i class="fas fa-user-check me-1"></i>Following';
          } else {
            showAlert(data.error || 'An error occurred', 'danger');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showAlert('An error occurred', 'danger');
        });
      });
    });
  });
</script>

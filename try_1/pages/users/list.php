<div class="container mt-4">
  <div class="row mb-4">
    <div class="col">
      <h1>Mars Colony Community</h1>
      <p class="lead">Connect with individuals and organizations working on Mars colonization.</p>
    </div>
  </div>

  <div class="row">
    <div class="col-md-3 mb-4">
      <div class="card">
        <div class="card-header bg-mars text-white">
          <h5 class="mb-0">Filter Users</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label for="filter-type" class="form-label">Account Type</label>
            <select class="form-select" id="filter-type">
              <option value="all">All</option>
              <option value="person">Individuals</option>
              <option value="organization">Organizations</option>
            </select>
          </div>
          
          <div class="mb-3">
            <label for="filter-sort" class="form-label">Sort By</label>
            <select class="form-select" id="filter-sort">
              <option value="name">Name (A-Z)</option>
              <option value="newest">Newest</option>
              <option value="activity">Most Active</option>
            </select>
          </div>
          
          <?php if( $isLoggedIn ): ?>
            <div class="mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="filter-following" value="following">
                <label class="form-check-label" for="filter-following">
                  Only show users I follow
                </label>
              </div>
            </div>
          <?php endif; ?>
          
          <button id="apply-filters" class="btn btn-outline-mars w-100">Apply Filters</button>
        </div>
      </div>
    </div>
    
    <div class="col-md-9">
      <div class="card mb-4">
        <div class="card-header bg-mars text-white">
          <h5 class="mb-0">Community Members</h5>
        </div>
        <div class="card-body">
          <?php if( !empty($users) ): ?>
            <div class="row" id="users-container">
              <?php foreach( $users as $u ): ?>
                <div class="col-md-6 mb-4 user-card" data-type="<?= $u['type'] ?? 'person' ?>">
                  <div class="card h-100">
                    <div class="card-body">
                      <div class="d-flex">
                        <?php if( !empty($u['profileImage']) ): ?>
                          <img src="uploads/profiles/<?= $u['profileImage'] ?>" alt="<?= $u['name'] ?>" class="rounded-circle me-3" style="width: 64px; height: 64px; object-fit: cover;">
                        <?php else: ?>
                          <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width: 64px; height: 64px;">
                            <i class="fas fa-user fa-2x text-secondary"></i>
                          </div>
                        <?php endif; ?>
                        
                        <div>
                          <h5 class="card-title mb-1"><?= $u['name'] ?></h5>
                          <p class="text-muted small mb-2">
                            <?= isset($u['type']) && $u['type'] === 'organization' ? 'Organization' : 'Individual' ?>
                            <?php if( !empty($u['location']) ): ?>
                              • <?= $u['location'] ?>
                            <?php endif; ?>
                          </p>
                          
                          <?php if( $isLoggedIn && isset($user['id']) && isset($u['id']) && $user['id'] !== $u['id'] ): ?>
                            <button class="btn btn-sm <?= in_array($u['id'], $user['usersFollowing'] ?? []) ? 'btn-mars' : 'btn-outline-mars' ?> follow-btn" 
                                    data-id="<?= $u['id'] ?>" 
                                    data-type="user" 
                                    data-following="<?= in_array($u['id'], $user['usersFollowing'] ?? []) ? '1' : '0' ?>">
                              <i class="fas <?= in_array($u['id'], $user['usersFollowing'] ?? []) ? 'fa-user-check' : 'fa-user-plus' ?>"></i>
                              <span><?= in_array($u['id'], $user['usersFollowing'] ?? []) ? 'Following' : 'Follow' ?></span>
                            </button>
                          <?php endif; ?>
                        </div>
                      </div>
                      
                      <?php if( !empty($u['expertise']) ): ?>
                        <div class="mt-3">
                          <p class="card-text small text-truncate"><?= $u['expertise'] ?></p>
                        </div>
                      <?php endif; ?>
                    </div>
                    <div class="card-footer">
                      <a href="index.php?page=users&action=view&id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-mars">View Profile</a>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-center">No users found.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const filterType = document.getElementById('filter-type');
  const filterSort = document.getElementById('filter-sort');
  const filterFollowing = document.getElementById('filter-following');
  const applyFiltersBtn = document.getElementById('apply-filters');
  const usersContainer = document.getElementById('users-container');
  
  applyFiltersBtn.addEventListener('click', function() {
    const type = filterType.value;
    const sort = filterSort.value;
    const following = filterFollowing?.checked || false;
    
    // Filter users based on selected criteria
    const users = document.querySelectorAll('.user-card');
    
    users.forEach(user => {
      let show = true;
      
      // Filter by type
      if( type !== 'all' ) {
        const userType = user.dataset.type;
        if( userType !== type ) {
          show = false;
        }
      }
      
      // Filter by following (if logged in)
      if( following && <?= $isLoggedIn ? 'true' : 'false' ?> ) {
        const followBtn = user.querySelector('.follow-btn');
        if( followBtn && followBtn.dataset.following !== '1' ) {
          show = false;
        }
      }
      
      user.style.display = show ? '' : 'none';
    });
    
    // Sort users
    const usersArray = Array.from(users).filter(user => user.style.display !== 'none');
    
    usersArray.sort((a, b) => {
      if( sort === 'name' ) {
        const nameA = a.querySelector('.card-title').textContent.trim();
        const nameB = b.querySelector('.card-title').textContent.trim();
        return nameA.localeCompare(nameB);
      } else if( sort === 'newest' ) {
        // We don't have creation date in the card, so we'll use ID as a proxy
        const idA = a.querySelector('a').getAttribute('href').split('id=')[1];
        const idB = b.querySelector('a').getAttribute('href').split('id=')[1];
        return idB.localeCompare(idA);
      } else if( sort === 'activity' ) {
        // We don't have activity data in the card, so we'll use a random sort for now
        return Math.random() - 0.5;
      }
      return 0;
    });
    
    // Re-append sorted users
    usersArray.forEach(user => {
      usersContainer.appendChild(user);
    });
  });
});
</script>

<div class="container mt-4">
  <div class="row mb-4">
    <div class="col">
      <h1>Search Results</h1>
      <p class="lead">Showing results for: "<?= htmlspecialchars($query) ?>"</p>
    </div>
  </div>

  <div class="row">
    <div class="col-md-3 mb-4">
      <div class="card">
        <div class="card-header bg-mars text-white">
          <h5 class="mb-0">Filter Results</h5>
        </div>
        <div class="card-body">
          <form action="index.php" method="GET">
            <input type="hidden" name="page" value="search">
            <input type="hidden" name="q" value="<?= htmlspecialchars($query) ?>">
            
            <div class="mb-3">
              <label for="filter" class="form-label">Result Type</label>
              <select class="form-select" id="filter" name="filter" onchange="this.form.submit()">
                <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All Results</option>
                <option value="requirements" <?= $filter === 'requirements' ? 'selected' : '' ?>>Requirements</option>
                <option value="items" <?= $filter === 'items' ? 'selected' : '' ?>>Items</option>
                <option value="users" <?= $filter === 'users' ? 'selected' : '' ?>>Users</option>
              </select>
            </div>
          </form>
          
          <div class="mt-4">
            <h6>Search Statistics</h6>
            <ul class="list-group list-group-flush">
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Requirements
                <span class="badge bg-mars rounded-pill"><?= count($requirements) ?></span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Items
                <span class="badge bg-mars rounded-pill"><?= count($items) ?></span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Users
                <span class="badge bg-mars rounded-pill"><?= count($users) ?></span>
              </li>
            </ul>
          </div>
          
          <div class="mt-4">
            <form action="index.php" method="GET" class="d-grid">
              <input type="hidden" name="page" value="search">
              <div class="input-group mb-3">
                <input type="text" class="form-control" name="q" value="<?= htmlspecialchars($query) ?>" placeholder="Search...">
                <button class="btn btn-mars" type="submit">
                  <i class="fas fa-search"></i>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-md-9">
      <?php if( empty($query) ): ?>
        <div class="card mb-4">
          <div class="card-body text-center py-5">
            <i class="fas fa-search fa-4x text-muted mb-3"></i>
            <h3>Enter a search term</h3>
            <p class="text-muted">Search for requirements, items, or users in the Mars colony project.</p>
          </div>
        </div>
      <?php elseif( empty($requirements) && empty($items) && empty($users) ): ?>
        <div class="card mb-4">
          <div class="card-body text-center py-5">
            <i class="fas fa-search fa-4x text-muted mb-3"></i>
            <h3>No results found</h3>
            <p class="text-muted">Try different keywords or check your spelling.</p>
          </div>
        </div>
      <?php else: ?>
        <?php if( ($filter === 'all' || $filter === 'requirements') && !empty($requirements) ): ?>
          <div class="card mb-4">
            <div class="card-header bg-mars text-white">
              <h5 class="mb-0">Requirements</h5>
            </div>
            <div class="card-body">
              <div class="list-group">
                <?php foreach( $requirements as $req ): ?>
                  <a href="index.php?page=requirements&action=view&id=<?= $req['id'] ?>" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                      <h5 class="mb-1"><?= $req['name'] ?></h5>
                      <span class="badge bg-mars"><?= $req['score'] ?? 0 ?></span>
                    </div>
                    <p class="mb-1"><?= $req['description'] ?></p>
                    <small class="text-muted">
                      Status: <?= ucfirst($req['status'] ?? 'proposed') ?>
                      <?php if( !empty($req['parentId']) ): ?>
                        • Sub-requirement
                      <?php endif; ?>
                    </small>
                  </a>
                <?php endforeach; ?>
              </div>
              
              <?php if( $filter === 'all' && count($requirements) > 5 ): ?>
                <div class="mt-3 text-center">
                  <a href="index.php?page=search&q=<?= urlencode($query) ?>&filter=requirements" class="btn btn-outline-mars">
                    View All <?= count($requirements) ?> Requirements
                  </a>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>
        
        <?php if( ($filter === 'all' || $filter === 'items') && !empty($items) ): ?>
          <div class="card mb-4">
            <div class="card-header bg-mars text-white">
              <h5 class="mb-0">Items</h5>
            </div>
            <div class="card-body">
              <div class="row">
                <?php 
                  $displayItems = $filter === 'all' ? array_slice($items, 0, 6) : $items;
                  foreach( $displayItems as $item ): 
                ?>
                  <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                      <?php if( !empty($item['primaryImage']) ): ?>
                        <img src="uploads/images/<?= $item['primaryImage'] ?>" class="card-img-top" alt="<?= $item['name'] ?>" style="height: 140px; object-fit: cover;">
                      <?php else: ?>
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 140px;">
                          <i class="fas fa-cube fa-3x text-secondary"></i>
                        </div>
                      <?php endif; ?>
                      
                      <div class="card-body">
                        <h5 class="card-title"><?= $item['name'] ?></h5>
                        <p class="card-text small"><?= $item['description'] ?></p>
                      </div>
                      <div class="card-footer d-flex justify-content-between align-items-center">
                        <span class="badge bg-mars"><?= $item['score'] ?? 0 ?></span>
                        <a href="index.php?page=items&action=view&id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-mars">View Details</a>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
              
              <?php if( $filter === 'all' && count($items) > 6 ): ?>
                <div class="mt-3 text-center">
                  <a href="index.php?page=search&q=<?= urlencode($query) ?>&filter=items" class="btn btn-outline-mars">
                    View All <?= count($items) ?> Items
                  </a>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>
        
        <?php if( ($filter === 'all' || $filter === 'users') && !empty($users) ): ?>
          <div class="card mb-4">
            <div class="card-header bg-mars text-white">
              <h5 class="mb-0">Users</h5>
            </div>
            <div class="card-body">
              <div class="row">
                <?php 
                  $displayUsers = $filter === 'all' ? array_slice($users, 0, 6) : $users;
                  foreach( $displayUsers as $u ): 
                ?>
                  <div class="col-md-6 mb-4">
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
                              <?= $u['accountType'] === 'organization' ? 'Organization' : 'Individual' ?>
                              <?php if( !empty($u['location']) ): ?>
                                • <?= $u['location'] ?>
                              <?php endif; ?>
                            </p>
                            
                            <?php if( $isLoggedIn && $user['id'] !== $u['id'] ): ?>
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
                      </div>
                      <div class="card-footer">
                        <a href="index.php?page=users&action=view&id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-mars">View Profile</a>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
              
              <?php if( $filter === 'all' && count($users) > 6 ): ?>
                <div class="mt-3 text-center">
                  <a href="index.php?page=search&q=<?= urlencode($query) ?>&filter=users" class="btn btn-outline-mars">
                    View All <?= count($users) ?> Users
                  </a>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

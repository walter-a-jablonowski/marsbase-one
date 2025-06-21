<div class="container mt-4">
  <div class="row">
    <div class="col-md-4">
      <div class="card mb-4">
        <div class="card-body text-center">
          <?php if( !empty($profileUser['profileImage']) ): ?>
            <img src="uploads/profiles/<?= $profileUser['profileImage'] ?>" alt="<?= $profileUser['name'] ?>" class="rounded-circle img-fluid mb-3" style="max-width: 150px;">
          <?php else: ?>
            <div class="profile-placeholder rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center bg-light" style="width: 150px; height: 150px;">
              <i class="fas fa-user fa-4x text-secondary"></i>
            </div>
          <?php endif; ?>
          
          <h3><?= $profileUser['name'] ?></h3>
          <p class="text-muted">
            <?= $profileUser['accountType'] === 'organization' ? 'Organization' : 'Individual' ?>
          </p>
          
          <?php if( $isLoggedIn && $user['id'] !== $profileUser['id'] ): ?>
            <button class="btn <?= $isFollowing ? 'btn-mars' : 'btn-outline-mars' ?> follow-btn mb-3" 
                    data-id="<?= $profileUser['id'] ?>" 
                    data-type="user" 
                    data-following="<?= $isFollowing ? '1' : '0' ?>">
              <i class="fas <?= $isFollowing ? 'fa-user-check' : 'fa-user-plus' ?>"></i>
              <span><?= $isFollowing ? 'Following' : 'Follow' ?></span>
            </button>
          <?php elseif( $isLoggedIn && $user['id'] === $profileUser['id'] ): ?>
            <a href="index.php?page=users&action=edit" class="btn btn-outline-mars mb-3">
              <i class="fas fa-edit"></i> Edit Profile
            </a>
          <?php endif; ?>
          
          <div class="user-stats d-flex justify-content-around mb-3">
            <div class="text-center">
              <h5><?= count($requirements) ?></h5>
              <small class="text-muted">Requirements</small>
            </div>
            <div class="text-center">
              <h5><?= count($items) ?></h5>
              <small class="text-muted">Items</small>
            </div>
            <div class="text-center">
              <h5><?= count($leadingItems) ?></h5>
              <small class="text-muted">Leading</small>
            </div>
          </div>
          
          <?php if( !empty($profileUser['location']) ): ?>
            <p class="mb-1">
              <i class="fas fa-map-marker-alt me-2"></i> <?= $profileUser['location'] ?>
            </p>
          <?php endif; ?>
          
          <?php if( !empty($profileUser['website']) ): ?>
            <p class="mb-1">
              <i class="fas fa-globe me-2"></i> 
              <a href="<?= $profileUser['website'] ?>" target="_blank" rel="noopener noreferrer"><?= $profileUser['website'] ?></a>
            </p>
          <?php endif; ?>
          
          <?php if( !empty($profileUser['social']) ): ?>
            <p class="mb-1">
              <i class="fas fa-share-alt me-2"></i> 
              <a href="<?= $profileUser['social'] ?>" target="_blank" rel="noopener noreferrer">Social Media</a>
            </p>
          <?php endif; ?>
        </div>
      </div>
      
      <?php if( !empty($profileUser['expertise']) ): ?>
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0">Expertise</h5>
          </div>
          <div class="card-body">
            <p><?= nl2br($profileUser['expertise']) ?></p>
          </div>
        </div>
      <?php endif; ?>
    </div>
    
    <div class="col-md-8">
      <?php if( !empty($profileUser['bio']) ): ?>
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0">About</h5>
          </div>
          <div class="card-body">
            <p><?= nl2br($profileUser['bio']) ?></p>
          </div>
        </div>
      <?php endif; ?>
      
      <div class="card mb-4">
        <div class="card-header">
          <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="requirements-tab" data-bs-toggle="tab" data-bs-target="#requirements" type="button" role="tab" aria-controls="requirements" aria-selected="true">Requirements</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="items-tab" data-bs-toggle="tab" data-bs-target="#items" type="button" role="tab" aria-controls="items" aria-selected="false">Items</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="leading-tab" data-bs-toggle="tab" data-bs-target="#leading" type="button" role="tab" aria-controls="leading" aria-selected="false">Leading</button>
            </li>
          </ul>
        </div>
        <div class="card-body">
          <div class="tab-content" id="profileTabsContent">
            <div class="tab-pane fade show active" id="requirements" role="tabpanel" aria-labelledby="requirements-tab">
              <?php if( !empty($requirements) ): ?>
                <div class="list-group">
                  <?php foreach( $requirements as $req ): ?>
                    <a href="index.php?page=requirements&action=view&id=<?= $req['id'] ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                      <?= $req['name'] ?>
                      <span class="badge bg-mars rounded-pill"><?= $req['score'] ?? 0 ?></span>
                    </a>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <p class="text-muted">No requirements created yet.</p>
              <?php endif; ?>
            </div>
            
            <div class="tab-pane fade" id="items" role="tabpanel" aria-labelledby="items-tab">
              <?php if( !empty($items) ): ?>
                <div class="list-group">
                  <?php foreach( $items as $item ): ?>
                    <a href="index.php?page=items&action=view&id=<?= $item['id'] ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                      <?= $item['name'] ?>
                      <span class="badge bg-mars rounded-pill"><?= $item['score'] ?? 0 ?></span>
                    </a>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <p class="text-muted">No items created yet.</p>
              <?php endif; ?>
            </div>
            
            <div class="tab-pane fade" id="leading" role="tabpanel" aria-labelledby="leading-tab">
              <?php if( !empty($leadingItems) ): ?>
                <div class="list-group">
                  <?php foreach( $leadingItems as $item ): ?>
                    <a href="index.php?page=items&action=view&id=<?= $item['id'] ?>" class="list-group-item list-group-item-action">
                      <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1"><?= $item['name'] ?></h5>
                        <?php if( !empty($item['fundingGoal']) ): ?>
                          <small>
                            $<?= $item['currentFunding'] ?? 0 ?> / $<?= $item['fundingGoal'] ?>
                          </small>
                        <?php endif; ?>
                      </div>
                      <p class="mb-1"><?= $item['description'] ?></p>
                      <?php if( !empty($item['availabilityDate']) ): ?>
                        <small class="text-muted">Available: <?= $item['availabilityDate'] ?></small>
                      <?php endif; ?>
                    </a>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <p class="text-muted">Not leading any projects yet.</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

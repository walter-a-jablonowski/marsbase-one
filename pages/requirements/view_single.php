<div class="container mt-4">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item"><a href="index.php?page=requirements">Requirements</a></li>
      <li class="breadcrumb-item active"><?= $requirement['name'] ?></li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-md-8">
      <div class="card mb-4">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <h1 class="mb-0"><?= $requirement['name'] ?></h1>
            <div>
              <span class="badge bg-<?= $requirement['status'] === 'validated' ? 'success' : 'warning' ?> me-2">
                <?= ucfirst($requirement['status']) ?>
              </span>
              <span class="badge bg-mars">
                <i class="fas fa-star me-1"></i> <?= $requirement['score'] ?? 0 ?>
              </span>
            </div>
          </div>
          
          <?php if( !empty($requirement['primaryImage']) ): ?>
            <div class="text-center mb-4">
              <img src="uploads/images/<?= $requirement['primaryImage'] ?>" alt="<?= $requirement['name'] ?>" class="img-fluid rounded" style="max-height: 300px;">
            </div>
          <?php endif; ?>
          
          <h5>Description</h5>
          <p><?= $requirement['description'] ?></p>
          
          <?php if( !empty($requirement['detailed']) ): ?>
            <h5>Detailed Information</h5>
            <div class="mb-4">
              <?= nl2br($requirement['detailed']) ?>
            </div>
          <?php endif; ?>
          
          <div class="d-flex justify-content-between align-items-center mt-4">
            <div>
              <small class="text-muted">
                Created by: <a href="index.php?page=profile&action=view&id=<?= $requirement['createdBy'] ?>"><?= $requirement['createdBy'] ?></a>
              </small>
              <br>
              <small class="text-muted">Last modified: <?= $requirement['modifiedAt'] ?></small>
            </div>
            
            <div class="d-flex">
              <?php if( $isLoggedIn ): ?>
                <?php if( $requirement['createdBy'] === $user['id'] ): ?>
                  <a href="index.php?page=requirements&action=edit&id=<?= $requirement['id'] ?>" class="btn btn-outline-mars me-2">
                    <i class="fas fa-edit"></i> Edit
                  </a>
                <?php endif; ?>
                
                <button class="btn <?= $isFollowing ? 'btn-mars' : 'btn-outline-mars' ?> follow-btn me-2" data-id="<?= $requirement['id'] ?>" data-type="requirement">
                  <i class="<?= $isFollowing ? 'fas' : 'far' ?> fa-star me-1"></i>
                  <?= $isFollowing ? 'Unfollow' : 'Follow' ?>
                </button>
                
                <div class="btn-group">
                  <button class="btn btn-outline-success vote-btn upvote <?= $userVote === 1 ? 'active' : '' ?>" data-id="<?= $requirement['id'] ?>" data-type="requirement" data-vote="up">
                    <i class="fas fa-arrow-up"></i>
                  </button>
                  <button class="btn btn-outline-danger vote-btn downvote <?= $userVote === -1 ? 'active' : '' ?>" data-id="<?= $requirement['id'] ?>" data-type="requirement" data-vote="down">
                    <i class="fas fa-arrow-down"></i>
                  </button>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      
      <?php if( !empty($childRequirements) ): ?>
        <div class="card mb-4">
          <div class="card-header bg-mars text-white">
            <h5 class="mb-0">Child Requirements</h5>
          </div>
          <div class="card-body">
            <div class="list-group">
              <?php foreach( $childRequirements as $child ): ?>
                <a href="index.php?page=requirements&action=view&id=<?= $child['id'] ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="mb-1"><?= $child['name'] ?></h6>
                    <p class="mb-1 text-muted small"><?= $child['description'] ?></p>
                  </div>
                  <span class="badge bg-mars rounded-pill"><?= $child['score'] ?? 0 ?></span>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      <?php endif; ?>
      
      <?php if( !empty($items) ): ?>
        <div class="card">
          <div class="card-header bg-mars text-white">
            <h5 class="mb-0">Items Fulfilling This Requirement</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <?php foreach( $items as $item ): ?>
                <div class="col-md-6 mb-4">
                  <div class="card h-100 item-card">
                    <?php if( !empty($item['primaryImage']) ): ?>
                      <img src="uploads/images/<?= $item['primaryImage'] ?>" class="card-img-top" alt="<?= $item['name'] ?>" style="height: 150px; object-fit: cover;">
                    <?php endif; ?>
                    
                    <?php if( !empty($item['availabilityDate']) ): ?>
                      <div class="availability">
                        <?= $item['availabilityDate'] ?>
                      </div>
                    <?php endif; ?>
                    
                    <div class="card-body">
                      <h5 class="card-title"><?= $item['name'] ?></h5>
                      <p class="card-text"><?= $item['description'] ?></p>
                      
                      <?php if( !empty($item['fundingGoal']) ): ?>
                        <div class="mt-2">
                          <small class="text-muted">Funding Progress:</small>
                          <div class="progress mt-1 mb-2">
                            <?php 
                              $currentFunding = $item['currentFunding'] ?? 0;
                              $percentage = ($currentFunding / $item['fundingGoal']) * 100;
                              $percentage = min(100, $percentage);
                            ?>
                            <div class="progress-bar bg-mars" role="progressbar" style="width: <?= $percentage ?>%" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                          </div>
                          <small class="text-muted">$<?= $currentFunding ?> of $<?= $item['fundingGoal'] ?> (<?= round($percentage) ?>%)</small>
                        </div>
                      <?php endif; ?>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                      <small class="text-muted">Score: <?= $item['score'] ?? 0 ?></small>
                      <a href="index.php?page=items&action=view&id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-mars">View Details</a>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
    
    <div class="col-md-4">
      <?php if( !empty($relatedRequirements) ): ?>
        <div class="card mb-4">
          <div class="card-header bg-mars text-white">
            <h5 class="mb-0">Related Requirements</h5>
          </div>
          <div class="card-body">
            <ul class="list-group list-group-flush">
              <?php foreach( $relatedRequirements as $related ): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <a href="index.php?page=requirements&action=view&id=<?= $related['id'] ?>" class="text-decoration-none">
                    <?= $related['name'] ?>
                  </a>
                  <span class="badge bg-mars rounded-pill"><?= $related['score'] ?? 0 ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      <?php endif; ?>
      
      <div class="card mb-4">
        <div class="card-header bg-mars text-white">
          <h5 class="mb-0">Suggest an Item</h5>
        </div>
        <div class="card-body">
          <?php if( $isLoggedIn ): ?>
            <p>Do you have an item that could fulfill this requirement?</p>
            <a href="index.php?page=items&action=create&req=<?= $requirement['id'] ?>" class="btn btn-mars w-100">
              <i class="fas fa-plus-circle me-2"></i> Add Item
            </a>
          <?php else: ?>
            <p>Please <a href="index.php?page=auth&action=login">login</a> to suggest items that fulfill this requirement.</p>
          <?php endif; ?>
        </div>
      </div>
      
      <div class="card">
        <div class="card-header bg-mars text-white">
          <h5 class="mb-0">Requirement Details</h5>
        </div>
        <div class="card-body">
          <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between">
              <span>Status:</span>
              <span class="badge bg-<?= $requirement['status'] === 'validated' ? 'success' : 'warning' ?>">
                <?= ucfirst($requirement['status']) ?>
              </span>
            </li>
            <li class="list-group-item d-flex justify-content-between">
              <span>Score:</span>
              <span><?= $requirement['score'] ?? 0 ?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between">
              <span>Child Requirements:</span>
              <span><?= count($childRequirements) ?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between">
              <span>Related Requirements:</span>
              <span><?= count($relatedRequirements) ?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between">
              <span>Items Fulfilling:</span>
              <span><?= count($items) ?></span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

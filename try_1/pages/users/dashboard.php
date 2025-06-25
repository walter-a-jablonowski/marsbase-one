<div class="container mt-4">
  <div class="row mb-4">
    <div class="col">
      <h1>My Dashboard</h1>
      <p class="lead">Welcome to your Mars Colony dashboard. Manage your contributions and see what you're following.</p>
    </div>
  </div>

  <div class="row">
    <div class="col-md-3 mb-4">
      <div class="list-group">
        <a href="#my-content" class="list-group-item list-group-item-action active" data-bs-toggle="list">My Content</a>
        <a href="#following" class="list-group-item list-group-item-action" data-bs-toggle="list">Following</a>
        <a href="#contributions" class="list-group-item list-group-item-action" data-bs-toggle="list">My Contributions</a>
        <a href="index.php?page=users&action=edit" class="list-group-item list-group-item-action">Edit Profile</a>
      </div>
    </div>
    
    <div class="col-md-9">
      <div class="tab-content">
        <!-- My Content Tab -->
        <div class="tab-pane fade show active" id="my-content">
          <div class="card mb-4">
            <div class="card-header bg-mars text-white">
              <h5 class="mb-0">My Requirements</h5>
            </div>
            <div class="card-body">
              <?php if( !empty($requirements) ): ?>
                <div class="list-group">
                  <?php foreach( $requirements as $req ): ?>
                    <a href="index.php?page=requirements&action=view&id=<?= $req['id'] ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                      <div>
                        <h6 class="mb-1"><?= $req['name'] ?></h6>
                        <small class="text-muted"><?= $req['description'] ?></small>
                      </div>
                      <div>
                        <span class="badge bg-mars rounded-pill me-2"><?= $req['score'] ?? 0 ?></span>
                        <span class="badge bg-secondary"><?= $req['status'] ?? 'proposed' ?></span>
                      </div>
                    </a>
                  <?php endforeach; ?>
                </div>
                <div class="mt-3">
                  <a href="index.php?page=requirements&action=create" class="btn btn-outline-mars">
                    <i class="fas fa-plus-circle me-2"></i> Add New Requirement
                  </a>
                </div>
              <?php else: ?>
                <p class="text-center mb-4">You haven't created any requirements yet.</p>
                <div class="d-grid">
                  <a href="index.php?page=requirements&action=create" class="btn btn-mars">
                    <i class="fas fa-plus-circle me-2"></i> Create Your First Requirement
                  </a>
                </div>
              <?php endif; ?>
            </div>
          </div>
          
          <div class="card mb-4">
            <div class="card-header bg-mars text-white">
              <h5 class="mb-0">My Items</h5>
            </div>
            <div class="card-body">
              <?php if( !empty($items) ): ?>
                <div class="row">
                  <?php foreach( $items as $item ): ?>
                    <div class="col-md-6 mb-3">
                      <div class="card h-100">
                        <?php if( !empty($item['primaryImage']) ): ?>
                          <img src="uploads/images/<?= $item['primaryImage'] ?>" class="card-img-top" alt="<?= $item['name'] ?>" style="height: 140px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                          <h6 class="card-title"><?= $item['name'] ?></h6>
                          <p class="card-text small"><?= $item['description'] ?></p>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                          <span class="badge bg-mars"><?= $item['score'] ?? 0 ?></span>
                          <a href="index.php?page=items&action=view&id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-mars">View</a>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
                <div class="mt-3">
                  <a href="index.php?page=items&action=create" class="btn btn-outline-mars">
                    <i class="fas fa-plus-circle me-2"></i> Add New Item
                  </a>
                </div>
              <?php else: ?>
                <p class="text-center mb-4">You haven't created any items yet.</p>
                <div class="d-grid">
                  <a href="index.php?page=items&action=create" class="btn btn-mars">
                    <i class="fas fa-plus-circle me-2"></i> Create Your First Item
                  </a>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
        
        <!-- Following Tab -->
        <div class="tab-pane fade" id="following">
          <div class="card mb-4">
            <div class="card-header bg-mars text-white">
              <h5 class="mb-0">Requirements I'm Following</h5>
            </div>
            <div class="card-body">
              <?php if( !empty($followedRequirements) ): ?>
                <div class="list-group">
                  <?php foreach( $followedRequirements as $req ): ?>
                    <a href="index.php?page=requirements&action=view&id=<?= $req['id'] ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                      <div>
                        <h6 class="mb-1"><?= $req['name'] ?></h6>
                        <small class="text-muted"><?= $req['description'] ?></small>
                      </div>
                      <span class="badge bg-mars rounded-pill"><?= $req['score'] ?? 0 ?></span>
                    </a>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <p class="text-center">You're not following any requirements yet.</p>
                <div class="d-grid">
                  <a href="index.php?page=requirements" class="btn btn-outline-mars">Browse Requirements</a>
                </div>
              <?php endif; ?>
            </div>
          </div>
          
          <div class="card mb-4">
            <div class="card-header bg-mars text-white">
              <h5 class="mb-0">Items I'm Following</h5>
            </div>
            <div class="card-body">
              <?php if( !empty($followedItems) ): ?>
                <div class="row">
                  <?php foreach( $followedItems as $item ): ?>
                    <div class="col-md-6 mb-3">
                      <div class="card h-100">
                        <?php if( !empty($item['primaryImage']) ): ?>
                          <img src="uploads/images/<?= $item['primaryImage'] ?>" class="card-img-top" alt="<?= $item['name'] ?>" style="height: 140px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                          <h6 class="card-title"><?= $item['name'] ?></h6>
                          <p class="card-text small"><?= $item['description'] ?></p>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                          <span class="badge bg-mars"><?= $item['score'] ?? 0 ?></span>
                          <a href="index.php?page=items&action=view&id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-mars">View</a>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <p class="text-center">You're not following any items yet.</p>
                <div class="d-grid">
                  <a href="index.php?page=items" class="btn btn-outline-mars">Browse Items</a>
                </div>
              <?php endif; ?>
            </div>
          </div>
          
          <div class="card mb-4">
            <div class="card-header bg-mars text-white">
              <h5 class="mb-0">People I'm Following</h5>
            </div>
            <div class="card-body">
              <?php if( !empty($followedUsers) ): ?>
                <div class="list-group">
                  <?php foreach( $followedUsers as $u ): ?>
                    <a href="index.php?page=users&action=view&id=<?= $u['id'] ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                      <?php if( !empty($u['profileImage']) ): ?>
                        <img src="uploads/profiles/<?= $u['profileImage'] ?>" alt="<?= $u['name'] ?>" class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
                      <?php else: ?>
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                          <i class="fas fa-user text-secondary"></i>
                        </div>
                      <?php endif; ?>
                      <div>
                        <h6 class="mb-0"><?= $u['name'] ?></h6>
                        <small class="text-muted"><?= $u['accountType'] === 'organization' ? 'Organization' : 'Individual' ?></small>
                      </div>
                    </a>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <p class="text-center">You're not following anyone yet.</p>
                <div class="d-grid">
                  <a href="index.php?page=users" class="btn btn-outline-mars">Browse Users</a>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
        
        <!-- Contributions Tab -->
        <div class="tab-pane fade" id="contributions">
          <div class="card mb-4">
            <div class="card-header bg-mars text-white">
              <h5 class="mb-0">My Contributions</h5>
            </div>
            <div class="card-body">
              <?php if( !empty($contributions) ): ?>
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Project</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach( $contributions as $contribution ): ?>
                        <tr>
                          <td><?= $contribution['item']['name'] ?></td>
                          <td><?= formatTimestamp($contribution['time']) ?></td>
                          <td>$<?= $contribution['amount'] ?></td>
                          <td>
                            <a href="index.php?page=items&action=view&id=<?= $contribution['item']['id'] ?>" class="btn btn-sm btn-outline-mars">View Project</a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php else: ?>
                <p class="text-center mb-4">You haven't made any contributions yet.</p>
                <div class="d-grid">
                  <a href="index.php?page=items" class="btn btn-outline-mars">Browse Projects to Support</a>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

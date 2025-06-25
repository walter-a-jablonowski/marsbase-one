<div class="container mt-4">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php?page=community">Community Projects</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($project['name']) ?></li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-md-8">
      <h1><?= htmlspecialchars($project['name']) ?></h1>
      
      <div class="mb-4">
        <p class="text-muted">
          Created by: <?= htmlspecialchars(getUserPublic($project['userId'])['name'] ?? 'Unknown') ?>
          on <?= formatTimestamp($project['createdAt']) ?>
        </p>
        
        <?php if( !empty($project['itemId']) ): ?>
          <?php $item = getItemWithScore($project['itemId']); ?>
          <?php if( !empty($item) ): ?>
            <p>
              <strong>Related Item:</strong>
              <a href="index.php?page=items&action=view&id=<?= $item['id'] ?>">
                <?= htmlspecialchars($item['name']) ?>
              </a>
            </p>
          <?php endif; ?>
        <?php endif; ?>
      </div>
      
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">Project Description</h5>
        </div>
        <div class="card-body">
          <?= nl2br(htmlspecialchars($project['description'])) ?>
        </div>
      </div>
      
      <?php if( !empty($project['backers']) ): ?>
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0">Backers (<?= count($project['backers']) ?>)</h5>
          </div>
          <div class="card-body p-0">
            <ul class="list-group list-group-flush">
              <?php foreach( $project['backers'] as $backer ): ?>
                <?php $backerUser = getUserPublic($backer['userId']); ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <?php if( !empty($backerUser) ): ?>
                      <a href="index.php?page=users&action=view&id=<?= $backerUser['id'] ?>">
                        <?= htmlspecialchars($backerUser['name']) ?>
                      </a>
                    <?php else: ?>
                      Anonymous
                    <?php endif; ?>
                    <small class="text-muted ms-2">
                      <?= formatTimestamp($backer['timestamp']) ?>
                    </small>
                  </div>
                  <span class="badge bg-primary rounded-pill">
                    <?= number_format($backer['amount']) ?> MC
                  </span>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      <?php endif; ?>
    </div>
    
    <div class="col-md-4">
      <div class="card mb-4 sticky-top" style="top: 20px;">
        <div class="card-header">
          <h5 class="mb-0">Funding Status</h5>
        </div>
        <div class="card-body">
          <?php 
            $fundingPercent = ($project['currentFunding'] / $project['goalAmount']) * 100;
            $fundingPercent = min($fundingPercent, 100);
            
            if( $fundingPercent < 30 ) {
              $progressClass = 'bg-danger';
            } elseif( $fundingPercent < 70 ) {
              $progressClass = 'bg-warning';
            } else {
              $progressClass = 'bg-success';
            }
          ?>
          
          <div class="progress mb-3">
            <div class="progress-bar <?= $progressClass ?>" role="progressbar" 
                 style="width: <?= $fundingPercent ?>%;" 
                 aria-valuenow="<?= $fundingPercent ?>" aria-valuemin="0" aria-valuemax="100">
              <?= round($fundingPercent) ?>%
            </div>
          </div>
          
          <div class="d-flex justify-content-between mb-3">
            <div>
              <h3><?= number_format($project['currentFunding']) ?> MC</h3>
              <p class="text-muted mb-0">of <?= number_format($project['goalAmount']) ?> MC goal</p>
            </div>
            <div class="text-end">
              <h3><?= count($project['backers']) ?></h3>
              <p class="text-muted mb-0">backers</p>
            </div>
          </div>
          
          <?php if( $this->auth->isLoggedIn() ): ?>
            <form action="index.php?page=community&action=fund" method="post">
              <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
              
              <div class="mb-3">
                <label for="amount" class="form-label">Contribution Amount (MC)</label>
                <div class="input-group">
                  <input type="number" class="form-control" id="amount" name="amount" 
                         min="1" step="1" required>
                  <span class="input-group-text">MC</span>
                </div>
                <div class="form-text">Enter the amount of Mars Credits you want to contribute</div>
              </div>
              
              <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-rocket"></i> Fund This Project
              </button>
            </form>
          <?php else: ?>
            <div class="alert alert-info mb-0">
              <p class="mb-2"><a href="index.php?page=auth&action=login">Log in</a> to fund this project.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

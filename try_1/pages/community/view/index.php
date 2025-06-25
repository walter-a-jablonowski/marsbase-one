<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Community Projects</h1>
    <?php if( $this->auth->isLoggedIn() ): ?>
      <a href="index.php?page=community&action=create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Create Project
      </a>
    <?php endif; ?>
  </div>
  
  <?php if( empty($projects) ): ?>
    <div class="alert alert-info">
      <p>No community projects have been created yet.</p>
      <?php if( $this->auth->isLoggedIn() ): ?>
        <p>Be the first to create a project for the Mars Colony!</p>
        <a href="index.php?page=community&action=create" class="btn btn-primary">
          <i class="fas fa-plus"></i> Create Project
        </a>
      <?php else: ?>
        <p><a href="index.php?page=auth&action=login">Log in</a> to create a project.</p>
      <?php endif; ?>
    </div>
  <?php else: ?>
    <div class="row">
      <?php foreach( $projects as $project ): ?>
        <div class="col-md-6 col-lg-4 mb-4">
          <div class="card h-100">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($project['name']) ?></h5>
              <p class="card-text text-muted">
                Created by: <?= htmlspecialchars(getUserPublic($project['userId'])['name'] ?? 'Unknown') ?>
              </p>
              <p class="card-text">
                <?= nl2br(htmlspecialchars(substr($project['description'], 0, 150))) ?>
                <?= strlen($project['description']) > 150 ? '...' : '' ?>
              </p>
              
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
              
              <p class="card-text">
                <strong>Funding:</strong> 
                <?= number_format($project['currentFunding']) ?> / <?= number_format($project['goalAmount']) ?> MC
                <br>
                <strong>Backers:</strong> <?= count($project['backers']) ?>
              </p>
            </div>
            <div class="card-footer bg-transparent">
              <a href="index.php?page=community&action=view&id=<?= $project['id'] ?>" class="btn btn-primary w-100">
                View Project
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

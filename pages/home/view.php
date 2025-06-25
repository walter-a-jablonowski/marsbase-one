<?php
use Marsbase\Models\Requirement;
use Marsbase\Models\Solution;
use Marsbase\Core\Auth;

// Get top requirements and solutions
$requirements = Requirement::findAll();
$solutions = Solution::findAll();

// Sort requirements by score
usort($requirements, function($a, $b) {
  return $b->calculateScore() - $a->calculateScore();
});

// Sort solutions by score
usort($solutions, function($a, $b) {
  return $b->calculateScore() - $a->calculateScore();
});

// Take only top 3 of each
$topRequirements = array_slice($requirements, 0, 3);
$topSolutions = array_slice($solutions, 0, 3);

// Check if user is logged in
$auth = Auth::getInstance();
$isLoggedIn = $auth->isLoggedIn();
?>

<!-- Hero Section -->
<section class="hero">
  <div class="container hero-content text-center">
    <h1 class="display-4 fw-bold mb-4">Building the Future on Mars</h1>
    <p class="lead mb-4">Join our community to define requirements and propose solutions for establishing a sustainable civilization on Mars.</p>
    <?php if( !$isLoggedIn ): ?>
      <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
        <a href="index.php?page=register" class="btn btn-primary btn-lg px-4 gap-3">Join the Mission</a>
        <a href="index.php?page=login" class="btn btn-outline-light btn-lg px-4">Login</a>
      </div>
    <?php else: ?>
      <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
        <a href="index.php?page=requirements" class="btn btn-primary btn-lg px-4 gap-3">Explore Requirements</a>
        <a href="index.php?page=people" class="btn btn-outline-light btn-lg px-4">Find Collaborators</a>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- Main Content -->
<div class="container py-5">
  <!-- Introduction -->
  <div class="row mb-5">
    <div class="col-lg-8 mx-auto text-center">
      <h2 class="fw-bold mb-3">Why Mars?</h2>
      <p class="lead">
        Mars represents humanity's next frontier. While Elon Musk is building the rockets to get us there, 
        we need to define what we'll need to survive and thrive once we arrive.
      </p>
      <p>
        MarsBase.One is a collaborative platform where experts and enthusiasts can define requirements 
        for a sustainable Mars colony and propose solutions to meet those requirements.
      </p>
    </div>
  </div>

  <!-- Top Requirements -->
  <div class="row mb-5">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Top Requirements</h2>
        <a href="index.php?page=requirements" class="btn btn-outline-primary">View All</a>
      </div>
      
      <?php if( empty($topRequirements) ): ?>
        <div class="alert alert-info">
          No requirements have been defined yet. Be the first to <a href="index.php?page=requirements" class="alert-link">create a requirement</a>!
        </div>
      <?php else: ?>
        <div class="row">
          <?php foreach( $topRequirements as $req ): ?>
            <div class="col-md-4 mb-4">
              <div class="card h-100 requirement-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <span class="badge bg-<?= $req->get('status') === 'proposed' ? 'warning' : 'success' ?>">
                    <?= ucfirst($req->get('status')) ?>
                  </span>
                  <div class="d-flex align-items-center">
                    <button class="vote-btn me-1" data-type="requirement" data-id="<?= $req->getId() ?>" data-vote="up" title="Upvote">
                      <i class="fas fa-arrow-up"></i>
                    </button>
                    <span class="vote-count" id="vote-count-requirement-<?= $req->getId() ?>"><?= $req->calculateScore() ?></span>
                    <button class="vote-btn ms-1" data-type="requirement" data-id="<?= $req->getId() ?>" data-vote="down" title="Downvote">
                      <i class="fas fa-arrow-down"></i>
                    </button>
                  </div>
                </div>
                <div class="card-body">
                  <h5 class="card-title"><?= $req->get('name') ?></h5>
                  <p class="card-text"><?= $req->get('description') ?></p>
                </div>
                <div class="card-footer bg-transparent">
                  <a href="index.php?page=requirement&id=<?= $req->getId() ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Top Solutions -->
  <div class="row mb-5">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Top Solutions</h2>
        <a href="index.php?page=requirements" class="btn btn-outline-primary">View All</a>
      </div>
      
      <?php if( empty($topSolutions) ): ?>
        <div class="alert alert-info">
          No solutions have been proposed yet. <a href="index.php?page=requirements" class="alert-link">Explore requirements</a> to suggest solutions!
        </div>
      <?php else: ?>
        <div class="row">
          <?php foreach( $topSolutions as $solution ): ?>
            <div class="col-md-4 mb-4">
              <div class="card h-100 solution-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <span class="badge bg-info"><?= ucfirst($solution->get('type')) ?></span>
                  <div class="d-flex align-items-center">
                    <button class="vote-btn me-1" data-type="solution" data-id="<?= $solution->getId() ?>" data-vote="up" title="Upvote">
                      <i class="fas fa-arrow-up"></i>
                    </button>
                    <span class="vote-count" id="vote-count-solution-<?= $solution->getId() ?>"><?= $solution->calculateScore() ?></span>
                    <button class="vote-btn ms-1" data-type="solution" data-id="<?= $solution->getId() ?>" data-vote="down" title="Downvote">
                      <i class="fas fa-arrow-down"></i>
                    </button>
                  </div>
                </div>
                <div class="card-body">
                  <h5 class="card-title"><?= $solution->get('name') ?></h5>
                  <p class="card-text"><?= $solution->get('description') ?></p>
                  
                  <?php if( $solution->get('type') === 'project' && !empty($solution->get('fundingGoal')) ): ?>
                    <?php 
                      $currentFunding = $solution->calculateFunding();
                      $fundingGoal = $solution->get('fundingGoal');
                      $percentage = min(100, ($currentFunding / $fundingGoal) * 100);
                    ?>
                    <div class="mt-3">
                      <p class="mb-1 small">Funding Progress</p>
                      <div class="funding-progress progress" id="funding-progress-<?= $solution->getId() ?>">
                        <div class="progress-bar" role="progressbar" style="width: <?= $percentage ?>%" 
                             aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                      <p class="mt-1 small text-end" id="funding-amount-<?= $solution->getId() ?>">
                        $<?= number_format($currentFunding, 2) ?> of $<?= number_format($fundingGoal, 2) ?>
                      </p>
                    </div>
                  <?php endif; ?>
                </div>
                <div class="card-footer bg-transparent">
                  <?php 
                    // Find the first requirement this solution fulfills
                    $reqIds = $solution->get('requirementIds', []);
                    $reqId = !empty($reqIds) ? $reqIds[0] : null;
                  ?>
                  <?php if( $reqId ): ?>
                    <a href="index.php?page=requirement&id=<?= $reqId ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                  <?php endif; ?>
                  
                  <?php if( $solution->get('type') === 'project' && !empty($solution->get('fundingGoal')) ): ?>
                    <button class="btn btn-sm btn-success float-end" data-bs-toggle="modal" data-bs-target="#fundingModal" 
                            data-solution-id="<?= $solution->getId() ?>" data-solution-name="<?= $solution->get('name') ?>">
                      Fund This Project
                    </button>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- How It Works -->
  <div class="row mb-5">
    <div class="col-lg-8 mx-auto">
      <h2 class="fw-bold text-center mb-4">How It Works</h2>
      <div class="row g-4">
        <div class="col-md-4 text-center">
          <div class="p-3">
            <div class="bg-light p-3 rounded-circle d-inline-block mb-3">
              <i class="fas fa-clipboard-list fa-2x text-primary"></i>
            </div>
            <h4>Define Requirements</h4>
            <p>Identify what we need to build a sustainable Mars colony.</p>
          </div>
        </div>
        <div class="col-md-4 text-center">
          <div class="p-3">
            <div class="bg-light p-3 rounded-circle d-inline-block mb-3">
              <i class="fas fa-lightbulb fa-2x text-primary"></i>
            </div>
            <h4>Propose Solutions</h4>
            <p>Suggest items, services, or projects that fulfill requirements.</p>
          </div>
        </div>
        <div class="col-md-4 text-center">
          <div class="p-3">
            <div class="bg-light p-3 rounded-circle d-inline-block mb-3">
              <i class="fas fa-users fa-2x text-primary"></i>
            </div>
            <h4>Collaborate</h4>
            <p>Vote on the best ideas and contribute to community projects.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Funding Modal -->
<div class="modal fade" id="fundingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Fund This Project</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Your contribution helps make this Mars project a reality!</p>
        <form id="funding-form">
          <input type="hidden" name="solutionId" id="modal-solution-id">
          <div class="mb-3">
            <label for="amount" class="form-label">Contribution Amount ($)</label>
            <input type="number" class="form-control" id="amount" name="amount" min="1" step="0.01" required>
          </div>
          <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i> This is a demo feature. No actual payment will be processed.
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" form="funding-form" class="btn btn-success">Contribute</button>
      </div>
    </div>
  </div>
</div>

<script>
  // Set solution ID when funding modal is opened
  document.addEventListener('DOMContentLoaded', function() {
    const fundingModal = document.getElementById('fundingModal');
    if (fundingModal) {
      fundingModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const solutionId = button.getAttribute('data-solution-id');
        const solutionName = button.getAttribute('data-solution-name');
        
        document.getElementById('modal-solution-id').value = solutionId;
        document.querySelector('#fundingModal .modal-title').textContent = `Fund Project: ${solutionName}`;
      });
    }
    
    // Add logged-in class to body if user is logged in
    if (<?= $isLoggedIn ? 'true' : 'false' ?>) {
      document.body.classList.add('logged-in');
    }
  });
</script>

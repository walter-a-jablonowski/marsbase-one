<?php
use Marsbase\Models\Requirement;
use Marsbase\Core\Auth;
use Marsbase\Core\Config;
use Marsbase\Core\Utils;

// Get all requirements
$requirements = Requirement::findAll();
$config = Config::getInstance();

// Get sort order from config
$defaultSortOrder = $config->get('requirement_sort_order', ['score', 'modifiedAt']);

// Get sort parameters from request
$sortBy = $_GET['sort'] ?? $defaultSortOrder[0];
$sortDir = $_GET['dir'] ?? 'desc';

// Validate sort parameters
$validSortFields = ['name', 'score', 'modifiedAt', 'status'];
if( !in_array($sortBy, $validSortFields) )
{
  $sortBy = $defaultSortOrder[0];
}

$validSortDirs = ['asc', 'desc'];
if( !in_array($sortDir, $validSortDirs) )
{
  $sortDir = 'desc';
}

// Sort requirements
usort($requirements, function($a, $b) use ($sortBy, $sortDir) {
  $aVal = $sortBy === 'score' ? $a->calculateScore() : $a->get($sortBy);
  $bVal = $sortBy === 'score' ? $b->calculateScore() : $b->get($sortBy);
  
  // Handle null values
  if( $aVal === null ) $aVal = '';
  if( $bVal === null ) $bVal = '';
  
  // Compare values
  $result = is_string($aVal) ? strcasecmp($aVal, $bVal) : $aVal <=> $bVal;
  
  // Apply sort direction
  return $sortDir === 'asc' ? $result : -$result;
});

// Check if user is logged in
$auth = Auth::getInstance();
$isLoggedIn = $auth->isLoggedIn();

// Handle new requirement form submission
if( $_SERVER['REQUEST_METHOD'] === 'POST' && $isLoggedIn )
{
  $name = $_POST['name'] ?? '';
  $description = $_POST['description'] ?? '';
  $detailed = $_POST['detailed'] ?? '';
  
  // Validate input
  $errors = [];
  
  if( empty($name) )
  {
    $errors[] = 'Name is required.';
  }
  
  if( empty($description) )
  {
    $errors[] = 'Description is required.';
  }
  
  if( empty($errors) )
  {
    // Create requirement
    $reqData = [
      'name' => $name,
      'description' => $description,
      'detailed' => $detailed,
      'status' => 'proposed',
      'parentIds' => [],
      'childIds' => [],
      'relatedIds' => [],
      'userIds' => [$auth->getUserId()],
      'itemIds' => []
    ];
    
    $requirement = Requirement::create($reqData, $auth->getUserId());
    
    if( $requirement )
    {
      Utils::setFlashMessage('Requirement created successfully.');
      Utils::redirect('index.php?page=requirement&id=' . $requirement->getId());
    }
    else
    {
      Utils::setFlashError('An error occurred while creating the requirement.');
    }
  }
  else
  {
    Utils::setFlashError(implode('<br>', $errors));
  }
}
?>

<div class="container py-4">
  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active" aria-current="page">Requirements</li>
    </ol>
  </nav>
  
  <!-- Page Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Mars Colony Requirements</h1>
    <?php if( $isLoggedIn ): ?>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newRequirementModal">
        <i class="fas fa-plus me-2"></i>New Requirement
      </button>
    <?php endif; ?>
  </div>
  
  <!-- Filters and Sorting -->
  <div class="card mb-4">
    <div class="card-body">
      <form method="get" action="index.php" class="row g-3 align-items-end">
        <input type="hidden" name="page" value="requirements">
        
        <div class="col-md-4">
          <label for="search" class="form-label">Search</label>
          <input type="text" class="form-control" id="search" name="search" 
                 value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        </div>
        
        <div class="col-md-3">
          <label for="sort" class="form-label">Sort By</label>
          <select class="form-select" id="sort" name="sort">
            <option value="score" <?= $sortBy === 'score' ? 'selected' : '' ?>>Score</option>
            <option value="name" <?= $sortBy === 'name' ? 'selected' : '' ?>>Name</option>
            <option value="modifiedAt" <?= $sortBy === 'modifiedAt' ? 'selected' : '' ?>>Last Modified</option>
            <option value="status" <?= $sortBy === 'status' ? 'selected' : '' ?>>Status</option>
          </select>
        </div>
        
        <div class="col-md-2">
          <label for="dir" class="form-label">Direction</label>
          <select class="form-select" id="dir" name="dir">
            <option value="desc" <?= $sortDir === 'desc' ? 'selected' : '' ?>>Descending</option>
            <option value="asc" <?= $sortDir === 'asc' ? 'selected' : '' ?>>Ascending</option>
          </select>
        </div>
        
        <div class="col-md-3">
          <button type="submit" class="btn btn-primary w-100">
            <i class="fas fa-filter me-2"></i>Apply Filters
          </button>
        </div>
      </form>
    </div>
  </div>
  
  <!-- Requirements List -->
  <?php if( empty($requirements) ): ?>
    <div class="alert alert-info">
      <i class="fas fa-info-circle me-2"></i>No requirements found. Be the first to create one!
    </div>
  <?php else: ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
      <?php foreach( $requirements as $req ): ?>
        <?php
          // Filter by search term if provided
          $searchTerm = $_GET['search'] ?? '';
          if( !empty($searchTerm) && 
              stripos($req->get('name'), $searchTerm) === false && 
              stripos($req->get('description'), $searchTerm) === false )
          {
            continue;
          }
          
          // Get user score if logged in
          $userScore = 0;
          if( $isLoggedIn )
          {
            $userScore = $auth->getUser()->getRequirementScore($req->getId());
          }
        ?>
        <div class="col">
          <div class="card h-100 requirement-card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <span class="badge bg-<?= $req->get('status') === 'proposed' ? 'warning' : 'success' ?>">
                <?= ucfirst($req->get('status')) ?>
              </span>
              <div class="d-flex align-items-center">
                <button class="vote-btn me-1 <?= $userScore > 0 ? 'active' : '' ?>" 
                        data-type="requirement" data-id="<?= $req->getId() ?>" data-vote="up" title="Upvote">
                  <i class="fas fa-arrow-up"></i>
                </button>
                <span class="vote-count" id="vote-count-requirement-<?= $req->getId() ?>">
                  <?= $req->calculateScore() ?>
                </span>
                <button class="vote-btn ms-1 <?= $userScore < 0 ? 'active' : '' ?>" 
                        data-type="requirement" data-id="<?= $req->getId() ?>" data-vote="down" title="Downvote">
                  <i class="fas fa-arrow-down"></i>
                </button>
              </div>
            </div>
            
            <?php if( $req->get('primaryImage') ): ?>
              <img src="data/requirements/<?= $req->getId() ?>/uploads/<?= $req->get('primaryImage') ?>" 
                   class="card-img-top" alt="<?= htmlspecialchars($req->get('name')) ?>">
            <?php endif; ?>
            
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($req->get('name')) ?></h5>
              <p class="card-text"><?= htmlspecialchars($req->get('description')) ?></p>
              
              <?php if( !empty($req->get('childIds')) ): ?>
                <p class="card-text">
                  <small class="text-muted">
                    <i class="fas fa-sitemap me-1"></i>
                    <?= count($req->get('childIds')) ?> sub-requirements
                  </small>
                </p>
              <?php endif; ?>
              
              <?php if( !empty($req->get('itemIds')) ): ?>
                <p class="card-text">
                  <small class="text-muted">
                    <i class="fas fa-lightbulb me-1"></i>
                    <?= count($req->get('itemIds')) ?> solutions
                  </small>
                </p>
              <?php endif; ?>
            </div>
            
            <div class="card-footer bg-transparent">
              <div class="d-flex justify-content-between align-items-center">
                <a href="index.php?page=requirement&id=<?= $req->getId() ?>" class="btn btn-sm btn-outline-primary">
                  <i class="fas fa-eye me-1"></i>View Details
                </a>
                <small class="text-muted">
                  <?= Utils::formatDate($req->get('modifiedAt')) ?>
                </small>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<!-- New Requirement Modal -->
<?php if( $isLoggedIn ): ?>
  <div class="modal fade" id="newRequirementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">New Requirement</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="post" action="index.php?page=requirements">
          <div class="modal-body">
            <div class="mb-3">
              <label for="name" class="form-label">Name</label>
              <input type="text" class="form-control" id="name" name="name" required>
              <div class="form-text">A clear, concise name for the requirement.</div>
            </div>
            
            <div class="mb-3">
              <label for="description" class="form-label">Short Description</label>
              <textarea class="form-control" id="description" name="description" rows="2" required></textarea>
              <div class="form-text">A brief summary of the requirement (1-2 sentences).</div>
            </div>
            
            <div class="mb-3">
              <label for="detailed" class="form-label">Detailed Explanation</label>
              <textarea class="form-control" id="detailed" name="detailed" rows="5"></textarea>
              <div class="form-text">Provide more details about this requirement, including why it's important for Mars colonization.</div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Create Requirement</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php endif; ?>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Add logged-in class to body if user is logged in
    if (<?= $isLoggedIn ? 'true' : 'false' ?>) {
      document.body.classList.add('logged-in');
    }
  });
</script>

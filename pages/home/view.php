<div class="hero-section">
  <div class="container text-center">
    <h1 class="display-4">Welcome to Marsbase.one</h1>
    <p class="lead">Building a sustainable civilization on Mars, one requirement at a time.</p>
    <?php if( !$isLoggedIn ): ?>
      <div class="mt-4">
        <a href="index.php?page=auth&action=register" class="btn btn-mars btn-lg me-2">Join the Mission</a>
        <a href="index.php?page=requirements" class="btn btn-outline-light btn-lg">Explore Requirements</a>
      </div>
    <?php else: ?>
      <div class="mt-4">
        <a href="index.php?page=requirements&action=create" class="btn btn-mars btn-lg me-2">Add Requirement</a>
        <a href="index.php?page=items&action=create" class="btn btn-outline-light btn-lg">Add Item</a>
      </div>
    <?php endif; ?>
  </div>
</div>

<div class="container mt-5">
  <div class="row">
    <div class="col-md-8">
      <h2>Building a Second Civilization on Mars</h2>
      <p>
        While Elon is building the rocket to get us to Mars, we need to plan what we'll need once we arrive.
        Marsbase.one is a community-driven platform where users can suggest, discuss, and vote on requirements
        for establishing a sustainable colony on Mars.
      </p>
      <p>
        From habitat modules to food production systems, from energy solutions to medical facilities,
        we're cataloging everything needed for humanity's second home.
      </p>
      
      <div class="row mt-5">
        <div class="col-md-6">
          <div class="card mb-4 card-mars h-100">
            <div class="card-header">
              <h5 class="mb-0">How It Works</h5>
            </div>
            <div class="card-body">
              <ol>
                <li>Browse or suggest <strong>requirements</strong> for the Mars colony</li>
                <li>Add <strong>items</strong> that fulfill these requirements</li>
                <li><strong>Vote</strong> on requirements and items to prioritize them</li>
                <li><strong>Follow</strong> items to stay updated on their progress</li>
                <li>Support community projects through <strong>contributions</strong></li>
              </ol>
              <a href="index.php?page=about" class="btn btn-outline-mars mt-3">Learn More</a>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card mb-4 card-mars h-100">
            <div class="card-header">
              <h5 class="mb-0">Get Involved</h5>
            </div>
            <div class="card-body">
              <p>Join our community of Mars enthusiasts, engineers, scientists, and dreamers.</p>
              <ul>
                <li>Share your expertise in specific areas</li>
                <li>Collaborate on solving Mars colonization challenges</li>
                <li>Contribute to community projects</li>
                <li>Connect with like-minded individuals</li>
              </ul>
              <?php if( !$isLoggedIn ): ?>
                <a href="index.php?page=auth&action=register" class="btn btn-mars mt-3">Join Now</a>
              <?php else: ?>
                <a href="index.php?page=community" class="btn btn-mars mt-3">Community Hub</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-md-4">
      <div class="card mb-4">
        <div class="card-header bg-mars text-white">
          <h5 class="mb-0">Top Requirements</h5>
        </div>
        <div class="card-body">
          <?php if( !empty($topRequirements) ): ?>
            <ul class="list-group list-group-flush">
              <?php foreach( $topRequirements as $req ): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <a href="index.php?page=requirements&action=view&id=<?= $req['id'] ?>" class="text-decoration-none">
                    <?= $req['name'] ?>
                  </a>
                  <span class="badge bg-mars rounded-pill"><?= $req['score'] ?? 0 ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
            <div class="text-center mt-3">
              <a href="index.php?page=requirements" class="btn btn-sm btn-outline-mars">View All Requirements</a>
            </div>
          <?php else: ?>
            <p class="text-center">No requirements yet. Be the first to add one!</p>
            <div class="text-center">
              <a href="index.php?page=requirements&action=create" class="btn btn-sm btn-mars">Add Requirement</a>
            </div>
          <?php endif; ?>
        </div>
      </div>
      
      <div class="card">
        <div class="card-header bg-mars text-white">
          <h5 class="mb-0">Top Items</h5>
        </div>
        <div class="card-body">
          <?php if( !empty($topItems) ): ?>
            <ul class="list-group list-group-flush">
              <?php foreach( $topItems as $item ): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <a href="index.php?page=items&action=view&id=<?= $item['id'] ?>" class="text-decoration-none">
                    <?= $item['name'] ?>
                  </a>
                  <span class="badge bg-mars rounded-pill"><?= $item['score'] ?? 0 ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
            <div class="text-center mt-3">
              <a href="index.php?page=items" class="btn btn-sm btn-outline-mars">View All Items</a>
            </div>
          <?php else: ?>
            <p class="text-center">No items yet. Be the first to add one!</p>
            <div class="text-center">
              <a href="index.php?page=items&action=create" class="btn btn-sm btn-mars">Add Item</a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

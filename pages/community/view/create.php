<div class="container mt-4">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php?page=community">Community Projects</a></li>
      <li class="breadcrumb-item active" aria-current="page">Create Project</li>
    </ol>
  </nav>

  <div class="card">
    <div class="card-header">
      <h1 class="h3 mb-0">Create Community Project</h1>
    </div>
    <div class="card-body">
      <form action="index.php?page=community&action=create" method="post">
        <div class="mb-3">
          <label for="name" class="form-label">Project Name</label>
          <input type="text" class="form-control" id="name" name="name" required>
        </div>
        
        <div class="mb-3">
          <label for="description" class="form-label">Project Description</label>
          <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
          <div class="form-text">Describe your project in detail. What will the funding be used for?</div>
        </div>
        
        <div class="mb-3">
          <label for="goal_amount" class="form-label">Funding Goal (Mars Credits)</label>
          <div class="input-group">
            <input type="number" class="form-control" id="goal_amount" name="goal_amount" 
                   min="100" step="100" required>
            <span class="input-group-text">MC</span>
          </div>
          <div class="form-text">How many Mars Credits do you need for this project?</div>
        </div>
        
        <div class="mb-3">
          <label for="item_id" class="form-label">Related Item (Optional)</label>
          <select class="form-select" id="item_id" name="item_id">
            <option value="">-- None --</option>
            <?php foreach( $items as $item ): ?>
              <option value="<?= $item['id'] ?>"><?= htmlspecialchars($item['name']) ?></option>
            <?php endforeach; ?>
          </select>
          <div class="form-text">If this project is related to a specific item, select it here.</div>
        </div>
        
        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-rocket"></i> Create Project
          </button>
          <a href="index.php?page=community" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="container mt-4">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item"><a href="index.php?page=requirements">Requirements</a></li>
      <li class="breadcrumb-item active">Create New Requirement</li>
    </ol>
  </nav>

  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card card-mars">
        <div class="card-header">
          <h3 class="mb-0">Create New Requirement</h3>
        </div>
        <div class="card-body">
          <?php if( !empty($error) ): ?>
            <div class="alert alert-danger"><?= $error ?></div>
          <?php endif; ?>
          
          <?php if( !empty($success) ): ?>
            <div class="alert alert-success"><?= $success ?></div>
          <?php endif; ?>
          
          <form method="POST" action="index.php?page=requirements&action=create" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="name" class="form-label">Name *</label>
              <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($requirement['name']) ?>" required>
              <div class="form-text">A clear, concise name for the requirement</div>
            </div>
            
            <div class="mb-3">
              <label for="description" class="form-label">Short Description *</label>
              <textarea class="form-control" id="description" name="description" rows="2" required><?= htmlspecialchars($requirement['description']) ?></textarea>
              <div class="form-text">A brief description (1-2 sentences)</div>
            </div>
            
            <div class="mb-3">
              <label for="detailed" class="form-label">Detailed Information</label>
              <textarea class="form-control" id="detailed" name="detailed" rows="5"><?= htmlspecialchars($requirement['detailed']) ?></textarea>
              <div class="form-text">Provide detailed information about this requirement</div>
            </div>
            
            <div class="mb-3">
              <label for="parentId" class="form-label">Parent Requirement</label>
              <select class="form-select" id="parentId" name="parentId">
                <option value="">None (Top-Level Requirement)</option>
                <?php foreach( $allRequirements as $req ): ?>
                  <option value="<?= $req['id'] ?>" <?= $requirement['parentId'] === $req['id'] ? 'selected' : '' ?>>
                    <?= $req['name'] ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <div class="form-text">Select a parent requirement if this is a sub-requirement</div>
            </div>
            
            <div class="mb-3">
              <label for="primaryImage" class="form-label">Primary Image</label>
              <input type="file" class="form-control" id="primaryImage" name="primaryImage" accept="image/*">
              <div class="form-text">Upload an image that represents this requirement</div>
            </div>
            
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-mars">Create Requirement</button>
              <a href="index.php?page=requirements" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

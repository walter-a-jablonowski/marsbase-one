<div class="container mt-4">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item"><a href="index.php?page=items">Items</a></li>
      <li class="breadcrumb-item"><a href="index.php?page=items&action=view&id=<?= $item['id'] ?>"><?= $item['name'] ?></a></li>
      <li class="breadcrumb-item active">Edit</li>
    </ol>
  </nav>

  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card card-mars">
        <div class="card-header">
          <h3 class="mb-0">Edit Item</h3>
        </div>
        <div class="card-body">
          <?php if( !empty($error) ): ?>
            <div class="alert alert-danger"><?= $error ?></div>
          <?php endif; ?>
          
          <?php if( !empty($success) ): ?>
            <div class="alert alert-success"><?= $success ?></div>
          <?php endif; ?>
          
          <form method="POST" action="index.php?page=items&action=edit&id=<?= $item['id'] ?>" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="name" class="form-label">Name *</label>
              <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($item['name']) ?>" required>
              <div class="form-text">A clear, concise name for the item</div>
            </div>
            
            <div class="mb-3">
              <label for="description" class="form-label">Description *</label>
              <textarea class="form-control" id="description" name="description" rows="3" required><?= htmlspecialchars($item['description']) ?></textarea>
              <div class="form-text">Describe what this item is and how it helps the Mars colony</div>
            </div>
            
            <div class="mb-3">
              <label for="requirementIds" class="form-label">Requirements Fulfilled *</label>
              <select class="form-select" id="requirementIds" name="requirementIds[]" multiple size="5" required>
                <?php foreach( $allRequirements as $req ): ?>
                  <option value="<?= $req['id'] ?>" <?= in_array($req['id'], $item['requirementIds'] ?? []) ? 'selected' : '' ?>>
                    <?= $req['name'] ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <div class="form-text">Select which requirements this item fulfills (hold Ctrl/Cmd to select multiple)</div>
            </div>
            
            <div class="mb-3">
              <label for="projectLead" class="form-label">Project Lead</label>
              <select class="form-select" id="projectLead" name="projectLead">
                <?php foreach( $allUsers as $u ): ?>
                  <option value="<?= $u['id'] ?>" <?= $item['projectLead'] === $u['id'] ? 'selected' : '' ?>>
                    <?= $u['name'] ?> (<?= $u['email'] ?>)
                  </option>
                <?php endforeach; ?>
              </select>
              <div class="form-text">Who is leading this project or item development?</div>
            </div>
            
            <div class="mb-3">
              <label for="availabilityDate" class="form-label">Availability Date</label>
              <input type="text" class="form-control" id="availabilityDate" name="availabilityDate" value="<?= htmlspecialchars($item['availabilityDate']) ?>">
              <div class="form-text">When will this item be available? (e.g. "Available Now", "Q3 2026", "Mars Mission 2")</div>
            </div>
            
            <div class="row">
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="mass" class="form-label">Mass (kg)</label>
                  <input type="text" class="form-control" id="mass" name="mass" value="<?= htmlspecialchars($item['mass']) ?>">
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="volume" class="form-label">Volume (m³)</label>
                  <input type="text" class="form-control" id="volume" name="volume" value="<?= htmlspecialchars($item['volume']) ?>">
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="shape" class="form-label">Shape/Dimensions</label>
                  <input type="text" class="form-control" id="shape" name="shape" value="<?= htmlspecialchars($item['shape']) ?>">
                </div>
              </div>
            </div>
            
            <div class="mb-3">
              <label for="primaryImage" class="form-label">Primary Image</label>
              <?php if( !empty($item['primaryImage']) ): ?>
                <div class="mb-2">
                  <img src="uploads/images/<?= $item['primaryImage'] ?>" alt="Current image" class="img-thumbnail" style="max-height: 100px;">
                  <div class="form-text">Current image</div>
                </div>
              <?php endif; ?>
              <input type="file" class="form-control" id="primaryImage" name="primaryImage" accept="image/*">
              <div class="form-text">Upload a new image to replace the current one</div>
            </div>
            
            <hr>
            
            <h5>Community Project Details</h5>
            <p class="text-muted small">Fill these fields if this item is a community project that needs funding or volunteers</p>
            
            <div class="mb-3">
              <label for="fundingGoal" class="form-label">Funding Goal ($)</label>
              <input type="number" class="form-control" id="fundingGoal" name="fundingGoal" value="<?= htmlspecialchars($item['fundingGoal']) ?>">
              <div class="form-text">How much funding does this project need?</div>
            </div>
            
            <div class="mb-3">
              <label for="volunteerRoles" class="form-label">Volunteer Roles</label>
              <textarea class="form-control" id="volunteerRoles" name="volunteerRoles" rows="3"><?= htmlspecialchars($item['volunteerRoles']) ?></textarea>
              <div class="form-text">Describe what volunteer roles are needed for this project</div>
            </div>
            
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-mars">Update Item</button>
              <a href="index.php?page=items&action=view&id=<?= $item['id'] ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

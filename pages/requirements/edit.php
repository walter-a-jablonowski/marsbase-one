<div class="container mt-4">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item"><a href="index.php?page=requirements">Requirements</a></li>
      <li class="breadcrumb-item"><a href="index.php?page=requirements&action=view&id=<?= $requirement['id'] ?>"><?= $requirement['name'] ?></a></li>
      <li class="breadcrumb-item active">Edit</li>
    </ol>
  </nav>

  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card card-mars">
        <div class="card-header">
          <h3 class="mb-0">Edit Requirement</h3>
        </div>
        <div class="card-body">
          <?php if( !empty($error) ): ?>
            <div class="alert alert-danger"><?= $error ?></div>
          <?php endif; ?>
          
          <?php if( !empty($success) ): ?>
            <div class="alert alert-success"><?= $success ?></div>
          <?php endif; ?>
          
          <form method="POST" action="index.php?page=requirements&action=edit&id=<?= $requirement['id'] ?>" enctype="multipart/form-data">
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
              <label for="status" class="form-label">Status</label>
              <select class="form-select" id="status" name="status">
                <option value="proposed" <?= $requirement['status'] === 'proposed' ? 'selected' : '' ?>>Proposed</option>
                <option value="validated" <?= $requirement['status'] === 'validated' ? 'selected' : '' ?>>Validated</option>
              </select>
            </div>
            
            <div class="mb-3">
              <label for="relatedIDs" class="form-label">Related Requirements</label>
              <select class="form-select" id="relatedIDs" name="relatedIDs[]" multiple size="5">
                <?php foreach( $allRequirements as $req ): ?>
                  <option value="<?= $req['id'] ?>" <?= in_array($req['id'], $requirement['relatedIDs'] ?? []) ? 'selected' : '' ?>>
                    <?= $req['name'] ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <div class="form-text">Hold Ctrl/Cmd to select multiple related requirements</div>
            </div>
            
            <div class="mb-3">
              <label for="itemIDs" class="form-label">Items Fulfilling This Requirement</label>
              <select class="form-select" id="itemIDs" name="itemIDs[]" multiple size="5">
                <?php foreach( $allItems as $item ): ?>
                  <option value="<?= $item['id'] ?>" <?= in_array($item['id'], $requirement['itemIDs'] ?? []) ? 'selected' : '' ?>>
                    <?= $item['name'] ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <div class="form-text">Hold Ctrl/Cmd to select multiple items</div>
            </div>
            
            <div class="mb-3">
              <label for="primaryImage" class="form-label">Primary Image</label>
              <?php if( !empty($requirement['primaryImage']) ): ?>
                <div class="mb-2">
                  <img src="uploads/images/<?= $requirement['primaryImage'] ?>" alt="Current image" class="img-thumbnail" style="max-height: 100px;">
                  <div class="form-text">Current image</div>
                </div>
              <?php endif; ?>
              <input type="file" class="form-control" id="primaryImage" name="primaryImage" accept="image/*">
              <div class="form-text">Upload a new image to replace the current one</div>
            </div>
            
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-mars">Update Requirement</button>
              <a href="index.php?page=requirements&action=view&id=<?= $requirement['id'] ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

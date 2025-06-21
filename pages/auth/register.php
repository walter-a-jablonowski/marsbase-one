<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card card-mars">
        <div class="card-header">
          <h3 class="mb-0">Register</h3>
        </div>
        <div class="card-body">
          <?php if( !empty($error) ): ?>
            <div class="alert alert-danger"><?= $error ?></div>
          <?php endif; ?>
          
          <form method="POST" action="index.php?page=auth&action=register">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email address *</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($userData['email']) ?>" required>
                <div class="form-text">This will be your login ID</div>
              </div>
              
              <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Name *</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($userData['name']) ?>" required>
                <div class="form-text">Your full name or organization name</div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="password" class="form-label">Password *</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <div class="form-text">Minimum 6 characters</div>
              </div>
              
              <div class="col-md-6 mb-3">
                <label for="confirm_password" class="form-label">Confirm Password *</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
              </div>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Account Type *</label>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="type" id="type_person" value="person" <?= $userData['type'] === 'person' ? 'checked' : '' ?>>
                <label class="form-check-label" for="type_person">
                  Person
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="type" id="type_organization" value="organization" <?= $userData['type'] === 'organization' ? 'checked' : '' ?>>
                <label class="form-check-label" for="type_organization">
                  Organization
                </label>
              </div>
            </div>
            
            <div class="mb-3">
              <label for="bio" class="form-label">Bio</label>
              <textarea class="form-control" id="bio" name="bio" rows="3"><?= htmlspecialchars($userData['bio']) ?></textarea>
              <div class="form-text">Brief description about yourself or your organization</div>
            </div>
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="expertise" class="form-label">Areas of Expertise</label>
                <input type="text" class="form-control" id="expertise" name="expertise" value="<?= htmlspecialchars($userData['expertise']) ?>">
                <div class="form-text">Comma-separated list of skills or expertise areas</div>
              </div>
              
              <div class="col-md-6 mb-3">
                <label for="location" class="form-label">Location</label>
                <select class="form-select" id="location" name="location">
                  <option value="Earth" <?= $userData['location'] === 'Earth' ? 'selected' : '' ?>>Earth</option>
                  <option value="Mars" <?= $userData['location'] === 'Mars' ? 'selected' : '' ?>>Mars</option>
                </select>
              </div>
            </div>
            
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-mars">Register</button>
            </div>
          </form>
          
          <div class="mt-3 text-center">
            <p>Already have an account? <a href="index.php?page=auth&action=login">Login</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

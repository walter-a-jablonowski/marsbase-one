<div class="container mt-4">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item"><a href="index.php?page=users&action=view&id=<?= $profileUser['id'] ?>">My Profile</a></li>
      <li class="breadcrumb-item active">Edit Profile</li>
    </ol>
  </nav>

  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card card-mars">
        <div class="card-header">
          <h3 class="mb-0">Edit Your Profile</h3>
        </div>
        <div class="card-body">
          <?php if( !empty($error) ): ?>
            <div class="alert alert-danger"><?= $error ?></div>
          <?php endif; ?>
          
          <?php if( !empty($success) ): ?>
            <div class="alert alert-success"><?= $success ?></div>
          <?php endif; ?>
          
          <form method="POST" action="index.php?page=users&action=edit" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="name" class="form-label">Name *</label>
              <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($profileUser['name']) ?>" required>
            </div>
            
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" value="<?= htmlspecialchars($profileUser['email']) ?>" disabled>
              <div class="form-text">Email cannot be changed</div>
            </div>
            
            <div class="mb-3">
              <label for="bio" class="form-label">Bio</label>
              <textarea class="form-control" id="bio" name="bio" rows="4"><?= htmlspecialchars($profileUser['bio'] ?? '') ?></textarea>
              <div class="form-text">Tell others about yourself or your organization</div>
            </div>
            
            <div class="mb-3">
              <label for="expertise" class="form-label">Expertise</label>
              <textarea class="form-control" id="expertise" name="expertise" rows="3"><?= htmlspecialchars($profileUser['expertise'] ?? '') ?></textarea>
              <div class="form-text">What are your areas of expertise for Mars colonization?</div>
            </div>
            
            <div class="mb-3">
              <label for="location" class="form-label">Location</label>
              <input type="text" class="form-control" id="location" name="location" value="<?= htmlspecialchars($profileUser['location'] ?? '') ?>">
            </div>
            
            <div class="mb-3">
              <label for="website" class="form-label">Website</label>
              <input type="url" class="form-control" id="website" name="website" value="<?= htmlspecialchars($profileUser['website'] ?? '') ?>">
            </div>
            
            <div class="mb-3">
              <label for="social" class="form-label">Social Media URL</label>
              <input type="url" class="form-control" id="social" name="social" value="<?= htmlspecialchars($profileUser['social'] ?? '') ?>">
            </div>
            
            <div class="mb-3">
              <label for="profileImage" class="form-label">Profile Image</label>
              <?php if( !empty($profileUser['profileImage']) ): ?>
                <div class="mb-2">
                  <img src="uploads/profiles/<?= $profileUser['profileImage'] ?>" alt="Current profile" class="img-thumbnail" style="max-height: 100px;">
                  <div class="form-text">Current profile image</div>
                </div>
              <?php endif; ?>
              <input type="file" class="form-control" id="profileImage" name="profileImage" accept="image/*">
              <div class="form-text">Upload a new profile image (square images work best)</div>
            </div>
            
            <hr>
            
            <h5>Change Password</h5>
            <p class="text-muted small">Leave blank if you don't want to change your password</p>
            
            <div class="mb-3">
              <label for="currentPassword" class="form-label">Current Password</label>
              <input type="password" class="form-control" id="currentPassword" name="currentPassword">
            </div>
            
            <div class="mb-3">
              <label for="newPassword" class="form-label">New Password</label>
              <input type="password" class="form-control" id="newPassword" name="newPassword">
            </div>
            
            <div class="mb-3">
              <label for="confirmPassword" class="form-label">Confirm New Password</label>
              <input type="password" class="form-control" id="confirmPassword" name="confirmPassword">
            </div>
            
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-mars">Update Profile</button>
              <a href="index.php?page=users&action=view&id=<?= $profileUser['id'] ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

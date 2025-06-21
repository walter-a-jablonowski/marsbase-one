<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card card-mars">
        <div class="card-header">
          <h3 class="mb-0">Login</h3>
        </div>
        <div class="card-body">
          <?php if( !empty($error) ): ?>
            <div class="alert alert-danger"><?= $error ?></div>
          <?php endif; ?>
          
          <form method="POST" action="index.php?page=auth&action=login">
            <div class="mb-3">
              <label for="email" class="form-label">Email address</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-mars">Login</button>
            </div>
          </form>
          
          <div class="mt-3 text-center">
            <p>Don't have an account? <a href="index.php?page=auth&action=register">Register</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

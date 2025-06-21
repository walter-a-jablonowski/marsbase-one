<div class="container mt-4">
  <div class="row mb-4">
    <div class="col-md-8">
      <h1>Mars Colony Requirements</h1>
      <p class="lead">Browse and vote on requirements for establishing a sustainable colony on Mars.</p>
    </div>
    <div class="col-md-4 text-end">
      <?php if( $isLoggedIn ): ?>
        <a href="index.php?page=requirements&action=create" class="btn btn-mars">
          <i class="fas fa-plus-circle me-2"></i> Add Requirement
        </a>
      <?php endif; ?>
    </div>
  </div>

  <div class="row">
    <div class="col-md-3 mb-4">
      <div class="card">
        <div class="card-header bg-mars text-white">
          <h5 class="mb-0">Filter Requirements</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label for="filter-status" class="form-label">Status</label>
            <select class="form-select" id="filter-status">
              <option value="all">All</option>
              <option value="proposed">Proposed</option>
              <option value="validated">Validated</option>
            </select>
          </div>
          
          <div class="mb-3">
            <label for="filter-sort" class="form-label">Sort By</label>
            <select class="form-select" id="filter-sort">
              <option value="score">Highest Score</option>
              <option value="newest">Newest</option>
              <option value="name">Name (A-Z)</option>
            </select>
          </div>
          
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="filter-following" value="following">
              <label class="form-check-label" for="filter-following">
                Only show requirements I follow
              </label>
            </div>
          </div>
          
          <button id="apply-filters" class="btn btn-outline-mars w-100">Apply Filters</button>
        </div>
      </div>
    </div>
    
    <div class="col-md-9">
      <div class="card mb-4">
        <div class="card-header bg-mars text-white">
          <h5 class="mb-0">Requirements Hierarchy</h5>
        </div>
        <div class="card-body requirements-tree">
          <?php if( !empty($rootRequirements) ): ?>
            <ul>
              <?php foreach( $rootRequirements as $req ): ?>
                <li>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <a href="index.php?page=requirements&action=view&id=<?= $req['id'] ?>" class="text-decoration-none">
                      <strong><?= $req['name'] ?></strong>
                    </a>
                    <span class="badge bg-mars rounded-pill"><?= $req['score'] ?? 0 ?></span>
                  </div>
                  <div class="text-muted small mb-2"><?= $req['description'] ?></div>
                  
                  <?php if( !empty($req['childIds']) ): ?>
                    <ul>
                      <?php foreach( $req['childIds'] as $childId ): ?>
                        <?php if( isset($childRequirements[$childId]) ): ?>
                          <?php $child = $childRequirements[$childId]; ?>
                          <li>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                              <a href="index.php?page=requirements&action=view&id=<?= $child['id'] ?>" class="text-decoration-none">
                                <?= $child['name'] ?>
                              </a>
                              <span class="badge bg-mars rounded-pill"><?= $child['score'] ?? 0 ?></span>
                            </div>
                            <div class="text-muted small mb-2"><?= $child['description'] ?></div>
                          </li>
                        <?php endif; ?>
                      <?php endforeach; ?>
                    </ul>
                  <?php endif; ?>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p class="text-center">No requirements found. Be the first to add one!</p>
          <?php endif; ?>
        </div>
      </div>
      
      <div class="card">
        <div class="card-header bg-mars text-white">
          <h5 class="mb-0">All Requirements</h5>
        </div>
        <div class="card-body">
          <?php if( !empty($rootRequirements) ): ?>
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Score</th>
                    <th>Created By</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach( array_merge($rootRequirements, $childRequirements) as $req ): ?>
                    <tr>
                      <td>
                        <a href="index.php?page=requirements&action=view&id=<?= $req['id'] ?>" class="text-decoration-none">
                          <?= $req['name'] ?>
                        </a>
                      </td>
                      <td>
                        <span class="badge bg-<?= $req['status'] === 'validated' ? 'success' : 'warning' ?>">
                          <?= ucfirst($req['status']) ?>
                        </span>
                      </td>
                      <td><?= $req['score'] ?? 0 ?></td>
                      <td>
                        <a href="index.php?page=profile&action=view&id=<?= $req['createdBy'] ?>" class="text-decoration-none">
                          <?= $req['createdBy'] ?>
                        </a>
                      </td>
                      <td>
                        <a href="index.php?page=requirements&action=view&id=<?= $req['id'] ?>" class="btn btn-sm btn-outline-mars">
                          <i class="fas fa-eye"></i> View
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <p class="text-center">No requirements found. Be the first to add one!</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const filterStatus = document.getElementById('filter-status');
  const filterSort = document.getElementById('filter-sort');
  const filterFollowing = document.getElementById('filter-following');
  const applyFiltersBtn = document.getElementById('apply-filters');
  
  applyFiltersBtn.addEventListener('click', function() {
    const status = filterStatus.value;
    const sort = filterSort.value;
    const following = filterFollowing.checked;
    
    // Filter requirements based on selected criteria
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
      let show = true;
      
      // Filter by status
      if( status !== 'all' ) {
        const rowStatus = row.querySelector('td:nth-child(2) .badge').textContent.trim().toLowerCase();
        if( rowStatus !== status ) {
          show = false;
        }
      }
      
      // Filter by following (if logged in)
      if( following && <?= $isLoggedIn ? 'true' : 'false' ?> ) {
        const reqId = row.querySelector('td:nth-child(1) a').getAttribute('href').split('id=')[1];
        const followingIds = <?= json_encode($user['reqFollowing'] ?? []) ?>;
        
        if( !followingIds.includes(reqId) ) {
          show = false;
        }
      }
      
      row.style.display = show ? '' : 'none';
    });
    
    // Sort requirements
    const tbody = document.querySelector('tbody');
    const rowsArray = Array.from(rows);
    
    rowsArray.sort((a, b) => {
      if( sort === 'score' ) {
        const scoreA = parseInt(a.querySelector('td:nth-child(3)').textContent);
        const scoreB = parseInt(b.querySelector('td:nth-child(3)').textContent);
        return scoreB - scoreA;
      } else if( sort === 'newest' ) {
        // We don't have creation date in the table, so we'll use ID as a proxy
        const idA = a.querySelector('td:nth-child(1) a').getAttribute('href').split('id=')[1];
        const idB = b.querySelector('td:nth-child(1) a').getAttribute('href').split('id=')[1];
        return idB.localeCompare(idA);
      } else if( sort === 'name' ) {
        const nameA = a.querySelector('td:nth-child(1)').textContent.trim();
        const nameB = b.querySelector('td:nth-child(1)').textContent.trim();
        return nameA.localeCompare(nameB);
      }
      return 0;
    });
    
    // Re-append sorted rows
    rowsArray.forEach(row => {
      tbody.appendChild(row);
    });
  });
});
</script>

<div class="container mt-4">
  <div class="row mb-4">
    <div class="col-md-8">
      <h1>Mars Colony Items</h1>
      <p class="lead">Browse and vote on items that fulfill requirements for the Mars colony.</p>
    </div>
    <div class="col-md-4 text-end">
      <?php if( $isLoggedIn ): ?>
        <a href="index.php?page=items&action=create" class="btn btn-mars">
          <i class="fas fa-plus-circle me-2"></i> Add Item
        </a>
      <?php endif; ?>
    </div>
  </div>

  <div class="row">
    <div class="col-md-3 mb-4">
      <div class="card">
        <div class="card-header bg-mars text-white">
          <h5 class="mb-0">Filter Items</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label for="filter-availability" class="form-label">Availability</label>
            <select class="form-select" id="filter-availability">
              <option value="all">All</option>
              <option value="available">Currently Available</option>
              <option value="future">Future Availability</option>
            </select>
          </div>
          
          <div class="mb-3">
            <label for="filter-sort" class="form-label">Sort By</label>
            <select class="form-select" id="filter-sort">
              <option value="score">Highest Score</option>
              <option value="funding">Highest Funding</option>
              <option value="newest">Newest</option>
              <option value="name">Name (A-Z)</option>
            </select>
          </div>
          
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="filter-following" value="following">
              <label class="form-check-label" for="filter-following">
                Only show items I follow
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
          <h5 class="mb-0">All Items</h5>
        </div>
        <div class="card-body">
          <?php if( !empty($items) ): ?>
            <div class="row" id="items-container">
              <?php foreach( $items as $item ): ?>
                <div class="col-md-6 col-lg-4 mb-4 item-card" data-availability="<?= !empty($item['availabilityDate']) ? 'future' : 'available' ?>">
                  <div class="card h-100">
                    <?php if( !empty($item['primaryImage']) ): ?>
                      <img src="uploads/images/<?= $item['primaryImage'] ?>" class="card-img-top" alt="<?= $item['name'] ?>" style="height: 180px; object-fit: cover;">
                    <?php else: ?>
                      <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                        <i class="fas fa-cube fa-3x text-secondary"></i>
                      </div>
                    <?php endif; ?>
                    
                    <?php if( !empty($item['availabilityDate']) ): ?>
                      <div class="availability">
                        <?= $item['availabilityDate'] ?>
                      </div>
                    <?php endif; ?>
                    
                    <div class="card-body">
                      <h5 class="card-title"><?= $item['name'] ?></h5>
                      <p class="card-text text-muted small"><?= $item['description'] ?></p>
                      
                      <?php if( !empty($item['fundingGoal']) ): ?>
                        <div class="mt-2">
                          <small class="text-muted">Funding Progress:</small>
                          <div class="progress mt-1 mb-2 funding-progress">
                            <?php 
                              $currentFunding = $item['currentFunding'] ?? 0;
                              $percentage = ($currentFunding / $item['fundingGoal']) * 100;
                              $percentage = min(100, $percentage);
                            ?>
                            <div class="progress-bar bg-mars" role="progressbar" style="width: <?= $percentage ?>%" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                          </div>
                          <small class="text-muted">$<?= $currentFunding ?> of $<?= $item['fundingGoal'] ?></small>
                        </div>
                      <?php endif; ?>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                      <div>
                        <span class="badge bg-mars">
                          <i class="fas fa-star me-1"></i> <?= $item['score'] ?? 0 ?>
                        </span>
                      </div>
                      <a href="index.php?page=items&action=view&id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-mars">View Details</a>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-center">No items found. Be the first to add one!</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const filterAvailability = document.getElementById('filter-availability');
  const filterSort = document.getElementById('filter-sort');
  const filterFollowing = document.getElementById('filter-following');
  const applyFiltersBtn = document.getElementById('apply-filters');
  const itemsContainer = document.getElementById('items-container');
  
  applyFiltersBtn.addEventListener('click', function() {
    const availability = filterAvailability.value;
    const sort = filterSort.value;
    const following = filterFollowing.checked;
    
    // Filter items based on selected criteria
    const items = document.querySelectorAll('.item-card');
    
    items.forEach(item => {
      let show = true;
      
      // Filter by availability
      if( availability !== 'all' ) {
        const itemAvailability = item.dataset.availability;
        if( itemAvailability !== availability ) {
          show = false;
        }
      }
      
      // Filter by following (if logged in)
      if( following && <?= $isLoggedIn ? 'true' : 'false' ?> ) {
        const itemId = item.querySelector('.btn-outline-mars').getAttribute('href').split('id=')[1];
        const followingIds = <?= json_encode($user['itemsFollowing'] ?? []) ?>;
        
        if( !followingIds.includes(itemId) ) {
          show = false;
        }
      }
      
      item.style.display = show ? '' : 'none';
    });
    
    // Sort items
    const itemsArray = Array.from(items).filter(item => item.style.display !== 'none');
    
    itemsArray.sort((a, b) => {
      if( sort === 'score' ) {
        const scoreA = parseInt(a.querySelector('.badge').textContent.trim());
        const scoreB = parseInt(b.querySelector('.badge').textContent.trim());
        return scoreB - scoreA;
      } else if( sort === 'funding' ) {
        const fundingA = parseFloat(a.querySelector('.text-muted:last-child')?.textContent.split('of')[0].replace('$', '').trim() || '0');
        const fundingB = parseFloat(b.querySelector('.text-muted:last-child')?.textContent.split('of')[0].replace('$', '').trim() || '0');
        return fundingB - fundingA;
      } else if( sort === 'newest' ) {
        // We don't have creation date in the card, so we'll use ID as a proxy
        const idA = a.querySelector('.btn-outline-mars').getAttribute('href').split('id=')[1];
        const idB = b.querySelector('.btn-outline-mars').getAttribute('href').split('id=')[1];
        return idB.localeCompare(idA);
      } else if( sort === 'name' ) {
        const nameA = a.querySelector('.card-title').textContent.trim();
        const nameB = b.querySelector('.card-title').textContent.trim();
        return nameA.localeCompare(nameB);
      }
      return 0;
    });
    
    // Re-append sorted items
    itemsArray.forEach(item => {
      itemsContainer.appendChild(item);
    });
  });
});
</script>

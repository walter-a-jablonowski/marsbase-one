<div class="container mt-4">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item"><a href="index.php?page=items">Items</a></li>
      <li class="breadcrumb-item active"><?= $item['name'] ?></li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-md-8">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h2 class="mb-0"><?= $item['name'] ?></h2>
          <div class="item-actions">
            <?php if( $isLoggedIn ): ?>
              <button class="btn btn-sm <?= $isFollowing ? 'btn-mars' : 'btn-outline-mars' ?> follow-btn me-2" 
                      data-id="<?= $item['id'] ?>" 
                      data-type="item" 
                      data-following="<?= $isFollowing ? '1' : '0' ?>">
                <i class="fas <?= $isFollowing ? 'fa-bookmark' : 'fa-bookmark' ?>"></i>
                <span><?= $isFollowing ? 'Following' : 'Follow' ?></span>
              </button>
              
              <div class="btn-group vote-btns" role="group" aria-label="Voting">
                <button type="button" class="btn btn-sm btn-outline-success vote-btn <?= $userVote > 0 ? 'active' : '' ?>" 
                        data-id="<?= $item['id'] ?>" 
                        data-type="item" 
                        data-vote="up">
                  <i class="fas fa-arrow-up"></i>
                </button>
                <span class="btn btn-sm btn-outline-secondary score-display"><?= $item['score'] ?? 0 ?></span>
                <button type="button" class="btn btn-sm btn-outline-danger vote-btn <?= $userVote < 0 ? 'active' : '' ?>" 
                        data-id="<?= $item['id'] ?>" 
                        data-type="item" 
                        data-vote="down">
                  <i class="fas fa-arrow-down"></i>
                </button>
              </div>
            <?php else: ?>
              <span class="badge bg-mars">
                <i class="fas fa-star me-1"></i> <?= $item['score'] ?? 0 ?>
              </span>
            <?php endif; ?>
          </div>
        </div>
        
        <?php if( !empty($item['primaryImage']) ): ?>
          <img src="uploads/images/<?= $item['primaryImage'] ?>" class="card-img-top" alt="<?= $item['name'] ?>" style="max-height: 400px; object-fit: contain;">
        <?php endif; ?>
        
        <div class="card-body">
          <p class="lead"><?= $item['description'] ?></p>
          
          <?php if( $isLoggedIn && ($item['createdBy'] === $user['id'] || $item['projectLead'] === $user['id']) ): ?>
            <div class="mb-3 text-end">
              <a href="index.php?page=items&action=edit&id=<?= $item['id'] ?>" class="btn btn-outline-mars">
                <i class="fas fa-edit me-1"></i> Edit Item
              </a>
            </div>
          <?php endif; ?>
          
          <h4 class="mt-4">Item Details</h4>
          <div class="row">
            <div class="col-md-6">
              <table class="table table-sm">
                <tbody>
                  <?php if( !empty($item['availabilityDate']) ): ?>
                    <tr>
                      <th scope="row">Availability</th>
                      <td><?= $item['availabilityDate'] ?></td>
                    </tr>
                  <?php endif; ?>
                  
                  <?php if( !empty($item['mass']) ): ?>
                    <tr>
                      <th scope="row">Mass</th>
                      <td><?= $item['mass'] ?> kg</td>
                    </tr>
                  <?php endif; ?>
                  
                  <?php if( !empty($item['volume']) ): ?>
                    <tr>
                      <th scope="row">Volume</th>
                      <td><?= $item['volume'] ?> m³</td>
                    </tr>
                  <?php endif; ?>
                  
                  <?php if( !empty($item['shape']) ): ?>
                    <tr>
                      <th scope="row">Shape</th>
                      <td><?= $item['shape'] ?></td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
            <div class="col-md-6">
              <table class="table table-sm">
                <tbody>
                  <?php if( !empty($projectLead) ): ?>
                    <tr>
                      <th scope="row">Project Lead</th>
                      <td>
                        <a href="index.php?page=users&action=view&id=<?= $projectLead['id'] ?>">
                          <?= $projectLead['name'] ?>
                        </a>
                      </td>
                    </tr>
                  <?php endif; ?>
                  
                  <tr>
                    <th scope="row">Created</th>
                    <td><?= formatTimestamp($item['modifiedAt'] ?? '') ?></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          
          <?php if( !empty($requirements) ): ?>
            <h4 class="mt-4">Fulfills Requirements</h4>
            <div class="list-group mb-4">
              <?php foreach( $requirements as $req ): ?>
                <a href="index.php?page=requirements&action=view&id=<?= $req['id'] ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                  <?= $req['name'] ?>
                  <span class="badge bg-mars rounded-pill"><?= $req['score'] ?? 0 ?></span>
                </a>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
          
          <?php if( !empty($item['volunteerRoles']) ): ?>
            <h4 class="mt-4">Volunteer Roles</h4>
            <div class="card mb-4">
              <div class="card-body">
                <?= nl2br($item['volunteerRoles']) ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    
    <div class="col-md-4">
      <?php if( !empty($item['fundingGoal']) ): ?>
        <div class="card mb-4">
          <div class="card-header bg-mars text-white">
            <h5 class="mb-0">Project Funding</h5>
          </div>
          <div class="card-body">
            <h4 class="text-center mb-3">$<?= $item['currentFunding'] ?? 0 ?> of $<?= $item['fundingGoal'] ?></h4>
            <div class="progress mb-3" style="height: 20px;">
              <div class="progress-bar bg-mars" role="progressbar" style="width: <?= $fundingPercentage ?>%;" 
                   aria-valuenow="<?= $fundingPercentage ?>" aria-valuemin="0" aria-valuemax="100">
                <?= round($fundingPercentage) ?>%
              </div>
            </div>
            
            <p class="text-muted small mb-4">
              This is a community project that needs funding to become reality. Your contribution helps make Mars colonization possible!
            </p>
            
            <?php if( $isLoggedIn ): ?>
              <form id="contribute-form" class="mb-3">
                <input type="hidden" name="itemId" value="<?= $item['id'] ?>">
                <div class="input-group mb-3">
                  <span class="input-group-text">$</span>
                  <input type="number" class="form-control" name="amount" min="1" step="1" placeholder="Amount">
                  <button class="btn btn-mars" type="submit">Contribute</button>
                </div>
              </form>
              <div id="contribution-message" class="alert d-none"></div>
            <?php else: ?>
              <div class="alert alert-info">
                <a href="index.php?page=auth&action=login">Log in</a> to contribute to this project.
              </div>
            <?php endif; ?>
            
            <?php if( !empty($item['contributions']) ): ?>
              <h6 class="mt-4">Recent Contributors</h6>
              <ul class="list-group list-group-flush">
                <?php 
                  $contributions = array_slice($item['contributions'], -5);
                  $contributions = array_reverse($contributions);
                  foreach( $contributions as $contribution ):
                    $contributor = getUser($contribution['user']);
                ?>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>
                      <?php if( !empty($contributor) ): ?>
                        <a href="index.php?page=users&action=view&id=<?= $contributor['id'] ?>"><?= $contributor['name'] ?></a>
                      <?php else: ?>
                        Anonymous
                      <?php endif; ?>
                    </span>
                    <span class="badge bg-mars">$<?= $contribution['amount'] ?></span>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
      
      <div class="card">
        <div class="card-header bg-mars text-white">
          <h5 class="mb-0">Share This Item</h5>
        </div>
        <div class="card-body">
          <div class="d-grid gap-2">
            <button class="btn btn-outline-primary" onclick="shareOnSocial('facebook')">
              <i class="fab fa-facebook-f me-2"></i> Share on Facebook
            </button>
            <button class="btn btn-outline-info" onclick="shareOnSocial('twitter')">
              <i class="fab fa-twitter me-2"></i> Share on Twitter
            </button>
            <button class="btn btn-outline-secondary" onclick="copyLink()">
              <i class="fas fa-link me-2"></i> Copy Link
            </button>
          </div>
          <div id="copy-message" class="alert alert-success mt-2 d-none">Link copied!</div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Contribution form handling
  const contributeForm = document.getElementById('contribute-form');
  if( contributeForm ) {
    contributeForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const itemId = this.elements.itemId.value;
      const amount = parseFloat(this.elements.amount.value);
      const messageEl = document.getElementById('contribution-message');
      
      if( isNaN(amount) || amount <= 0 ) {
        messageEl.textContent = 'Please enter a valid amount';
        messageEl.classList.remove('d-none', 'alert-success');
        messageEl.classList.add('alert-danger');
        return;
      }
      
      // Send AJAX request
      fetch('ajax.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          action: 'contribute',
          itemId: itemId,
          amount: amount
        })
      })
      .then(response => response.json())
      .then(data => {
        if( data.success ) {
          // Update UI
          messageEl.textContent = data.message;
          messageEl.classList.remove('d-none', 'alert-danger');
          messageEl.classList.add('alert-success');
          
          // Update funding display
          document.querySelector('h4.text-center').textContent = `$${data.current} of $${data.goal}`;
          
          // Update progress bar
          const progressBar = document.querySelector('.progress-bar');
          progressBar.style.width = `${data.percentage}%`;
          progressBar.setAttribute('aria-valuenow', data.percentage);
          progressBar.textContent = `${data.percentage}%`;
          
          // Clear form
          this.elements.amount.value = '';
          
          // Reload page after a delay to show updated contributors
          setTimeout(() => {
            location.reload();
          }, 2000);
        } else {
          messageEl.textContent = data.message;
          messageEl.classList.remove('d-none', 'alert-success');
          messageEl.classList.add('alert-danger');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        messageEl.textContent = 'An error occurred. Please try again.';
        messageEl.classList.remove('d-none', 'alert-success');
        messageEl.classList.add('alert-danger');
      });
    });
  }
  
  // Social sharing
  window.shareOnSocial = function(platform) {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent('<?= $item['name'] ?> - Mars Colony Project');
    
    let shareUrl = '';
    
    if( platform === 'facebook' ) {
      shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
    } else if( platform === 'twitter' ) {
      shareUrl = `https://twitter.com/intent/tweet?text=${title}&url=${url}`;
    }
    
    if( shareUrl ) {
      window.open(shareUrl, '_blank', 'width=600,height=400');
    }
  };
  
  // Copy link
  window.copyLink = function() {
    const el = document.createElement('textarea');
    el.value = window.location.href;
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
    
    const copyMessage = document.getElementById('copy-message');
    copyMessage.classList.remove('d-none');
    
    setTimeout(() => {
      copyMessage.classList.add('d-none');
    }, 2000);
  };
});
</script>

/**
 * MarsBase.One - Main JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
  // Initialize tooltips
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function(tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // Initialize popovers
  const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
  popoverTriggerList.map(function(popoverTriggerEl) {
    return new bootstrap.Popover(popoverTriggerEl);
  });

  // Handle voting buttons
  setupVoting();
  
  // Handle requirement and solution forms
  setupForms();
});

/**
 * Setup voting functionality
 */
function setupVoting() {
  document.querySelectorAll('.vote-btn').forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      
      if (!isLoggedIn()) {
        showLoginModal();
        return;
      }
      
      const type = this.dataset.type; // 'requirement' or 'solution'
      const id = this.dataset.id;
      const vote = this.dataset.vote; // 'up' or 'down'
      const voteValue = vote === 'up' ? 1 : -1;
      
      // Send vote to server
      fetch('ajax.php?action=' + type + '/vote', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          id: id,
          vote: voteValue
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Update vote count
          const countElement = document.querySelector(`#vote-count-${type}-${id}`);
          countElement.textContent = data.newScore;
          
          // Update button states
          const upButton = document.querySelector(`.vote-btn[data-type="${type}"][data-id="${id}"][data-vote="up"]`);
          const downButton = document.querySelector(`.vote-btn[data-type="${type}"][data-id="${id}"][data-vote="down"]`);
          
          upButton.classList.toggle('active', data.userVote === 1);
          downButton.classList.toggle('active', data.userVote === -1);
        } else {
          showAlert(data.error || 'An error occurred while voting', 'danger');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while voting', 'danger');
      });
    });
  });
}

/**
 * Setup form submissions
 */
function setupForms() {
  // Requirement form
  const requirementForm = document.getElementById('requirement-form');
  if (requirementForm) {
    requirementForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      const jsonData = {};
      
      formData.forEach((value, key) => {
        jsonData[key] = value;
      });
      
      // Handle arrays
      if (formData.getAll('parentIds[]').length > 0) {
        jsonData.parentIds = formData.getAll('parentIds[]');
        delete jsonData['parentIds[]'];
      }
      
      if (formData.getAll('userIds[]').length > 0) {
        jsonData.userIds = formData.getAll('userIds[]');
        delete jsonData['userIds[]'];
      }
      
      fetch('ajax.php?action=requirements/save', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(jsonData)
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          window.location.href = `index.php?page=requirement&id=${data.id}`;
        } else {
          showAlert(data.error || 'An error occurred while saving the requirement', 'danger');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while saving the requirement', 'danger');
      });
    });
  }
  
  // Solution form
  const solutionForm = document.getElementById('solution-form');
  if (solutionForm) {
    solutionForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      const jsonData = {};
      
      formData.forEach((value, key) => {
        jsonData[key] = value;
      });
      
      // Handle arrays
      if (formData.getAll('requirementIds[]').length > 0) {
        jsonData.requirementIds = formData.getAll('requirementIds[]');
        delete jsonData['requirementIds[]'];
      }
      
      fetch('ajax.php?action=solutions/save', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(jsonData)
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // If we're on a requirement page, reload to show the new solution
          const urlParams = new URLSearchParams(window.location.search);
          if (urlParams.get('page') === 'requirement') {
            window.location.reload();
          } else {
            window.location.href = `index.php?page=requirement&id=${data.requirementId}`;
          }
        } else {
          showAlert(data.error || 'An error occurred while saving the solution', 'danger');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while saving the solution', 'danger');
      });
    });
  }
  
  // Funding form
  const fundingForm = document.getElementById('funding-form');
  if (fundingForm) {
    fundingForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      const jsonData = {
        solutionId: formData.get('solutionId'),
        amount: parseFloat(formData.get('amount'))
      };
      
      fetch('ajax.php?action=solutions/fund', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(jsonData)
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Update funding progress
          const progressBar = document.querySelector(`#funding-progress-${jsonData.solutionId} .progress-bar`);
          const fundingText = document.querySelector(`#funding-amount-${jsonData.solutionId}`);
          
          progressBar.style.width = `${data.percentage}%`;
          progressBar.setAttribute('aria-valuenow', data.percentage);
          fundingText.textContent = `$${data.currentAmount} of $${data.goalAmount}`;
          
          // Close modal
          const modal = bootstrap.Modal.getInstance(document.getElementById('fundingModal'));
          modal.hide();
          
          showAlert('Thank you for your contribution!', 'success');
        } else {
          showAlert(data.error || 'An error occurred while processing your contribution', 'danger');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while processing your contribution', 'danger');
      });
    });
  }
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
  return document.body.classList.contains('logged-in');
}

/**
 * Show login modal
 */
function showLoginModal() {
  const modalHtml = `
    <div class="modal fade" id="loginRequiredModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Login Required</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>You need to be logged in to perform this action.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <a href="index.php?page=login" class="btn btn-primary">Login</a>
          </div>
        </div>
      </div>
    </div>
  `;
  
  // Append modal to body if it doesn't exist
  if (!document.getElementById('loginRequiredModal')) {
    const div = document.createElement('div');
    div.innerHTML = modalHtml;
    document.body.appendChild(div.firstChild);
  }
  
  // Show modal
  const modal = new bootstrap.Modal(document.getElementById('loginRequiredModal'));
  modal.show();
}

/**
 * Show alert message
 */
function showAlert(message, type = 'info') {
  const alertHtml = `
    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  `;
  
  const alertContainer = document.getElementById('alert-container');
  if (alertContainer) {
    alertContainer.innerHTML = alertHtml;
  } else {
    // Create alert container if it doesn't exist
    const container = document.createElement('div');
    container.id = 'alert-container';
    container.className = 'container mt-3';
    container.innerHTML = alertHtml;
    
    const main = document.querySelector('main');
    main.insertBefore(container, main.firstChild);
  }
}

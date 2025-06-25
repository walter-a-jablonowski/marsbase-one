/**
 * Mars Theme JavaScript
 * Custom functionality for Marsbase.one
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
  
  // Vote buttons functionality
  setupVoteButtons();
  
  // Follow buttons functionality
  setupFollowButtons();
});

/**
 * Setup vote buttons functionality
 */
function setupVoteButtons() {
  const voteButtons = document.querySelectorAll('.vote-btn');
  
  voteButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      
      const itemId = this.dataset.id;
      const itemType = this.dataset.type; // 'item' or 'requirement'
      const voteType = this.dataset.vote; // 'up' or 'down'
      
      // Send vote via fetch API
      fetch('ajax.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          action: 'vote',
          itemId: itemId,
          itemType: itemType,
          voteType: voteType
        })
      })
      .then(response => response.json())
      .then(data => {
        if( data.success ) {
          // Update UI
          const scoreElement = document.querySelector(`#${itemType}-score-${itemId}`);
          if( scoreElement ) {
            scoreElement.textContent = data.score;
          }
          
          // Toggle active class
          const upvoteBtn = document.querySelector(`.vote-btn.upvote[data-id="${itemId}"][data-type="${itemType}"]`);
          const downvoteBtn = document.querySelector(`.vote-btn.downvote[data-id="${itemId}"][data-type="${itemType}"]`);
          
          if( voteType === 'up' ) {
            upvoteBtn.classList.toggle('active');
            downvoteBtn.classList.remove('active');
          } else {
            downvoteBtn.classList.toggle('active');
            upvoteBtn.classList.remove('active');
          }
        } else {
          // Show error
          alert(data.message || 'Error processing vote');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while processing your vote');
      });
    });
  });
}

/**
 * Setup follow buttons functionality
 */
function setupFollowButtons() {
  const followButtons = document.querySelectorAll('.follow-btn');
  
  followButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      
      const itemId = this.dataset.id;
      const itemType = this.dataset.type; // 'item' or 'requirement'
      
      // Send follow request via fetch API
      fetch('ajax.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          action: 'follow',
          itemId: itemId,
          itemType: itemType
        })
      })
      .then(response => response.json())
      .then(data => {
        if( data.success ) {
          // Update button text and icon
          const icon = this.querySelector('i');
          if( data.following ) {
            this.textContent = ' Unfollow';
            this.classList.replace('btn-outline-mars', 'btn-mars');
            icon.className = 'fas fa-star me-1';
          } else {
            this.textContent = ' Follow';
            this.classList.replace('btn-mars', 'btn-outline-mars');
            icon.className = 'far fa-star me-1';
          }
          this.prepend(icon);
        } else {
          // Show error
          alert(data.message || 'Error processing follow request');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while processing your request');
      });
    });
  });
}

/**
 * Handle contribution form submission
 */
function handleContribution(formId, itemId) {
  const form = document.getElementById(formId);
  
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const amount = form.querySelector('input[name="amount"]').value;
    
    // Send contribution via fetch API
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
        // Update funding progress
        const progressBar = document.querySelector(`#funding-progress-${itemId}`);
        const progressText = document.querySelector(`#funding-text-${itemId}`);
        
        if( progressBar && progressText ) {
          progressBar.style.width = `${data.percentage}%`;
          progressText.textContent = `$${data.current} of $${data.goal} (${data.percentage}%)`;
        }
        
        // Reset form and show success message
        form.reset();
        alert('Thank you for your contribution!');
      } else {
        // Show error
        alert(data.message || 'Error processing contribution');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred while processing your contribution');
    });
  });
}

/**
 * Filter items by category
 */
function filterItems(category) {
  const items = document.querySelectorAll('.item-card');
  
  items.forEach(item => {
    if( category === 'all' || item.dataset.category === category ) {
      item.style.display = 'block';
    } else {
      item.style.display = 'none';
    }
  });
  
  // Update active filter button
  const filterButtons = document.querySelectorAll('.filter-btn');
  filterButtons.forEach(button => {
    if( button.dataset.filter === category ) {
      button.classList.add('active');
    } else {
      button.classList.remove('active');
    }
  });
}

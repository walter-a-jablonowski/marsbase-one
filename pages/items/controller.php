<?php
/**
 * Items controller
 */

class ItemsController extends BaseController
{
  /**
   * Index action - list all items
   */
  public function indexAction()
  {
    // Get all items
    $items = getAllItems();
    
    // Sort by score
    usort($items, function($a, $b) {
      return ($b['score'] ?? 0) - ($a['score'] ?? 0);
    });
    
    // Render view
    $this->render(__DIR__ . '/view.php', [
      'items' => $items,
      'isLoggedIn' => $this->auth->isLoggedIn(),
      'user' => $this->user
    ]);
  }
  
  /**
   * View a single item
   */
  public function viewAction()
  {
    $id = $_GET['id'] ?? '';
    
    if( empty($id) )
    {
      $_SESSION['message'] = 'Item ID not provided';
      $_SESSION['message_type'] = 'danger';
      redirect('index.php?page=items');
    }
    
    // Get item
    $item = getItem($id);
    
    if( empty($item) )
    {
      $_SESSION['message'] = 'Item not found';
      $_SESSION['message_type'] = 'danger';
      redirect('index.php?page=items');
    }
    
    // Get requirements this item fulfills
    $requirements = [];
    if( !empty($item['requirementIds']) )
    {
      foreach( $item['requirementIds'] as $reqId )
      {
        $req = getRequirement($reqId);
        if( !empty($req) )
        {
          $requirements[] = $req;
        }
      }
    }
    
    // Get project lead
    $projectLead = [];
    if( !empty($item['projectLead']) )
    {
      $projectLead = getUser($item['projectLead']);
    }
    
    // Calculate funding percentage
    $fundingPercentage = 0;
    if( !empty($item['fundingGoal']) && $item['fundingGoal'] > 0 )
    {
      $currentFunding = $item['currentFunding'] ?? 0;
      $fundingPercentage = ($currentFunding / $item['fundingGoal']) * 100;
      $fundingPercentage = min(100, $fundingPercentage);
    }
    
    // Check if user is following this item
    $isFollowing = false;
    $userVote = 0;
    
    if( $this->auth->isLoggedIn() )
    {
      $isFollowing = in_array($id, $this->user['itemsFollowing'] ?? []);
      
      // Check user vote
      foreach( $this->user['itemScore'] ?? [] as $vote )
      {
        if( isset($vote[$id]) )
        {
          $userVote = $vote[$id];
          break;
        }
      }
    }
    
    // Render view
    $this->render(__DIR__ . '/view_single.php', [
      'item' => $item,
      'requirements' => $requirements,
      'projectLead' => $projectLead,
      'fundingPercentage' => $fundingPercentage,
      'isLoggedIn' => $this->auth->isLoggedIn(),
      'isFollowing' => $isFollowing,
      'userVote' => $userVote,
      'user' => $this->user
    ]);
  }
  
  /**
   * Create a new item
   */
  public function createAction()
  {
    // Check if user is logged in
    $this->requireLogin();
    
    $error = '';
    $success = '';
    
    // Get all requirements for selection
    $allRequirements = getAllRequirements();
    
    // Check if a requirement was pre-selected
    $preSelectedReq = $_GET['req'] ?? '';
    
    // Default values
    $item = [
      'name' => '',
      'description' => '',
      'requirementIds' => !empty($preSelectedReq) ? [$preSelectedReq] : [],
      'availabilityDate' => '',
      'mass' => '',
      'volume' => '',
      'shape' => '',
      'fundingGoal' => '',
      'volunteerRoles' => ''
    ];
    
    // Process form submission
    if( $_SERVER['REQUEST_METHOD'] === 'POST' )
    {
      // Get form data
      $item = [
        'name' => $_POST['name'] ?? '',
        'description' => $_POST['description'] ?? '',
        'requirementIds' => $_POST['requirementIds'] ?? [],
        'availabilityDate' => $_POST['availabilityDate'] ?? '',
        'mass' => $_POST['mass'] ?? '',
        'volume' => $_POST['volume'] ?? '',
        'shape' => $_POST['shape'] ?? '',
        'fundingGoal' => $_POST['fundingGoal'] ?? '',
        'volunteerRoles' => $_POST['volunteerRoles'] ?? ''
      ];
      
      // Validate input
      if( empty($item['name']) )
      {
        $error = 'Name is required';
      }
      elseif( empty($item['description']) )
      {
        $error = 'Description is required';
      }
      elseif( empty($item['requirementIds']) )
      {
        $error = 'At least one requirement must be selected';
      }
      else
      {
        // Generate ID
        $id = generateId($item['name']);
        
        // Create item
        $newItem = [
          'id' => $id,
          'requirementIds' => $item['requirementIds'],
          'name' => $item['name'],
          'description' => $item['description'],
          'projectLead' => $this->user['id'],
          'availabilityDate' => $item['availabilityDate'],
          'primaryImage' => '',
          'images' => [],
          'score' => 0,
          'mass' => $item['mass'],
          'volume' => $item['volume'],
          'shape' => $item['shape'],
          'fundingGoal' => $item['fundingGoal'],
          'contributions' => [],
          'currentFunding' => 0,
          'volunteerRoles' => $item['volunteerRoles'],
          'createdBy' => $this->user['id'],
          'modifiedAt' => getCurrentTimestamp()
        ];
        
        // Handle image upload
        if( isset($_FILES['primaryImage']) && $_FILES['primaryImage']['error'] === UPLOAD_ERR_OK )
        {
          $newImage = handleFileUpload($_FILES['primaryImage'], UPLOADS_DIR . '/images');
          if( $newImage )
          {
            $newItem['primaryImage'] = $newImage;
          }
        }
        
        // Update requirements to include this item
        foreach( $item['requirementIds'] as $reqId )
        {
          $req = getRequirement($reqId);
          if( !empty($req) )
          {
            if( !isset($req['itemIDs']) )
            {
              $req['itemIDs'] = [];
            }
            
            $req['itemIDs'][] = $id;
            saveRequirement($req);
          }
        }
        
        // Save item
        if( saveItem($newItem) )
        {
          $_SESSION['message'] = 'Item created successfully';
          $_SESSION['message_type'] = 'success';
          redirect('index.php?page=items&action=view&id=' . $id);
        }
        else
        {
          $error = 'Failed to create item';
        }
      }
    }
    
    // Render view
    $this->render(__DIR__ . '/create.php', [
      'item' => $item,
      'allRequirements' => $allRequirements,
      'error' => $error,
      'success' => $success
    ]);
  }
  
  /**
   * Edit an item
   */
  public function editAction()
  {
    // Check if user is logged in
    $this->requireLogin();
    
    $id = $_GET['id'] ?? '';
    
    if( empty($id) )
    {
      $_SESSION['message'] = 'Item ID not provided';
      $_SESSION['message_type'] = 'danger';
      redirect('index.php?page=items');
    }
    
    // Get item
    $item = getItem($id);
    
    if( empty($item) )
    {
      $_SESSION['message'] = 'Item not found';
      $_SESSION['message_type'] = 'danger';
      redirect('index.php?page=items');
    }
    
    // Check if user is creator or project lead
    if( $item['createdBy'] !== $this->user['id'] && $item['projectLead'] !== $this->user['id'] )
    {
      $_SESSION['message'] = 'You do not have permission to edit this item';
      $_SESSION['message_type'] = 'danger';
      redirect('index.php?page=items&action=view&id=' . $id);
    }
    
    $error = '';
    $success = '';
    
    // Get all requirements for selection
    $allRequirements = getAllRequirements();
    
    // Get all users for project lead selection
    $allUsers = getAllUsers();
    
    // Process form submission
    if( $_SERVER['REQUEST_METHOD'] === 'POST' )
    {
      // Get form data
      $item['name'] = $_POST['name'] ?? $item['name'];
      $item['description'] = $_POST['description'] ?? $item['description'];
      $item['requirementIds'] = $_POST['requirementIds'] ?? $item['requirementIds'];
      $item['projectLead'] = $_POST['projectLead'] ?? $item['projectLead'];
      $item['availabilityDate'] = $_POST['availabilityDate'] ?? $item['availabilityDate'];
      $item['mass'] = $_POST['mass'] ?? $item['mass'];
      $item['volume'] = $_POST['volume'] ?? $item['volume'];
      $item['shape'] = $_POST['shape'] ?? $item['shape'];
      $item['fundingGoal'] = $_POST['fundingGoal'] ?? $item['fundingGoal'];
      $item['volunteerRoles'] = $_POST['volunteerRoles'] ?? $item['volunteerRoles'];
      $item['modifiedAt'] = getCurrentTimestamp();
      
      // Validate input
      if( empty($item['name']) )
      {
        $error = 'Name is required';
      }
      elseif( empty($item['description']) )
      {
        $error = 'Description is required';
      }
      elseif( empty($item['requirementIds']) )
      {
        $error = 'At least one requirement must be selected';
      }
      else
      {
        // Handle image upload
        if( isset($_FILES['primaryImage']) && $_FILES['primaryImage']['error'] === UPLOAD_ERR_OK )
        {
          $newImage = handleFileUpload($_FILES['primaryImage'], UPLOADS_DIR . '/images');
          if( $newImage )
          {
            $item['primaryImage'] = $newImage;
          }
        }
        
        // Update requirements to include this item
        $allReqs = getAllRequirements();
        
        foreach( $allReqs as $req )
        {
          $itemIDs = $req['itemIDs'] ?? [];
          $hasItem = in_array($id, $itemIDs);
          $shouldHaveItem = in_array($req['id'], $item['requirementIds']);
          
          if( $hasItem && !$shouldHaveItem )
          {
            // Remove item from requirement
            $req['itemIDs'] = array_diff($itemIDs, [$id]);
            saveRequirement($req);
          }
          elseif( !$hasItem && $shouldHaveItem )
          {
            // Add item to requirement
            $req['itemIDs'][] = $id;
            saveRequirement($req);
          }
        }
        
        // Save item
        if( saveItem($item) )
        {
          $success = 'Item updated successfully';
        }
        else
        {
          $error = 'Failed to update item';
        }
      }
    }
    
    // Render view
    $this->render(__DIR__ . '/edit.php', [
      'item' => $item,
      'allRequirements' => $allRequirements,
      'allUsers' => $allUsers,
      'error' => $error,
      'success' => $success
    ]);
  }
  
  /**
   * Contribute to an item
   */
  public function contributeAction()
  {
    // Check if user is logged in
    $this->requireLogin();
    
    $id = $_GET['id'] ?? '';
    
    if( empty($id) )
    {
      $this->jsonResponse([
        'success' => false,
        'message' => 'Item ID not provided'
      ]);
    }
    
    // Get item
    $item = getItem($id);
    
    if( empty($item) )
    {
      $this->jsonResponse([
        'success' => false,
        'message' => 'Item not found'
      ]);
    }
    
    // Process contribution
    $amount = floatval($_POST['amount'] ?? 0);
    
    if( $amount <= 0 )
    {
      $this->jsonResponse([
        'success' => false,
        'message' => 'Invalid contribution amount'
      ]);
    }
    
    // Add contribution
    $contribution = [
      'user' => $this->user['id'],
      'time' => getCurrentTimestamp(),
      'amount' => $amount
    ];
    
    $item['contributions'][] = $contribution;
    
    // Calculate current funding
    $currentFunding = 0;
    foreach( $item['contributions'] as $c )
    {
      $currentFunding += $c['amount'];
    }
    
    $item['currentFunding'] = $currentFunding;
    $item['modifiedAt'] = getCurrentTimestamp();
    
    // Save item
    if( saveItem($item) )
    {
      $percentage = ($currentFunding / $item['fundingGoal']) * 100;
      $percentage = min(100, $percentage);
      
      $this->jsonResponse([
        'success' => true,
        'message' => 'Contribution successful',
        'current' => $currentFunding,
        'goal' => $item['fundingGoal'],
        'percentage' => round($percentage)
      ]);
    }
    else
    {
      $this->jsonResponse([
        'success' => false,
        'message' => 'Failed to process contribution'
      ]);
    }
  }
}
?>

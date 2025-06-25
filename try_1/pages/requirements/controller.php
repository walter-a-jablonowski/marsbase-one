<?php
/**
 * Requirements controller
 */

class RequirementsController extends BaseController
{
  /**
   * Index action - list all requirements
   */
  public function indexAction()
  {
    // Get all requirements
    $requirements = getAllRequirements();
    
    // Sort by score
    usort($requirements, function($a, $b) {
      return ($b['score'] ?? 0) - ($a['score'] ?? 0);
    });
    
    // Build hierarchical structure
    $rootRequirements = [];
    $childRequirements = [];
    
    foreach( $requirements as $req )
    {
      if( empty($req['childIds']) )
      {
        $rootRequirements[] = $req;
      }
      else
      {
        $childRequirements[$req['id']] = $req;
      }
    }
    
    // Render view
    $this->render(__DIR__ . '/view.php', [
      'rootRequirements' => $rootRequirements,
      'childRequirements' => $childRequirements,
      'isLoggedIn' => $this->auth->isLoggedIn(),
      'user' => $this->user
    ]);
  }
  
  /**
   * View a single requirement
   */
  public function viewAction()
  {
    $id = $_GET['id'] ?? '';
    
    if( empty($id) )
    {
      $_SESSION['message'] = 'Requirement ID not provided';
      $_SESSION['message_type'] = 'danger';
      redirect('index.php?page=requirements');
    }
    
    // Get requirement
    $requirement = getRequirement($id);
    
    if( empty($requirement) )
    {
      $_SESSION['message'] = 'Requirement not found';
      $_SESSION['message_type'] = 'danger';
      redirect('index.php?page=requirements');
    }
    
    // Get related requirements
    $relatedRequirements = [];
    if( !empty($requirement['relatedIDs']) )
    {
      foreach( $requirement['relatedIDs'] as $relatedId )
      {
        $related = getRequirement($relatedId);
        if( !empty($related) )
        {
          $relatedRequirements[] = $related;
        }
      }
    }
    
    // Get items that fulfill this requirement
    $items = [];
    if( !empty($requirement['itemIDs']) )
    {
      foreach( $requirement['itemIDs'] as $itemId )
      {
        $item = getItem($itemId);
        if( !empty($item) )
        {
          $items[] = $item;
        }
      }
    }
    
    // Get child requirements
    $childRequirements = [];
    if( !empty($requirement['childIds']) )
    {
      foreach( $requirement['childIds'] as $childId )
      {
        $child = getRequirement($childId);
        if( !empty($child) )
        {
          $childRequirements[] = $child;
        }
      }
    }
    
    // Check if user is following this requirement
    $isFollowing = false;
    $userVote = 0;
    
    if( $this->auth->isLoggedIn() )
    {
      $isFollowing = in_array($id, $this->user['reqFollowing'] ?? []);
      
      // Check user vote
      foreach( $this->user['reqScore'] ?? [] as $vote )
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
      'requirement' => $requirement,
      'relatedRequirements' => $relatedRequirements,
      'items' => $items,
      'childRequirements' => $childRequirements,
      'isLoggedIn' => $this->auth->isLoggedIn(),
      'isFollowing' => $isFollowing,
      'userVote' => $userVote,
      'user' => $this->user
    ]);
  }
  
  /**
   * Create a new requirement
   */
  public function createAction()
  {
    // Check if user is logged in
    $this->requireLogin();
    
    $error = '';
    $success = '';
    
    // Get all requirements for parent selection
    $allRequirements = getAllRequirements();
    
    // Default values
    $requirement = [
      'name' => '',
      'description' => '',
      'detailed' => '',
      'status' => 'proposed',
      'parentId' => ''
    ];
    
    // Process form submission
    if( $_SERVER['REQUEST_METHOD'] === 'POST' )
    {
      // Get form data
      $requirement = [
        'name' => $_POST['name'] ?? '',
        'description' => $_POST['description'] ?? '',
        'detailed' => $_POST['detailed'] ?? '',
        'status' => 'proposed',
        'parentId' => $_POST['parentId'] ?? ''
      ];
      
      // Validate input
      if( empty($requirement['name']) )
      {
        $error = 'Name is required';
      }
      elseif( empty($requirement['description']) )
      {
        $error = 'Description is required';
      }
      else
      {
        // Generate ID
        $id = generateId($requirement['name']);
        
        // Create requirement
        $newRequirement = [
          'id' => $id,
          'childIds' => [],
          'name' => $requirement['name'],
          'primaryImage' => '',
          'images' => [],
          'status' => 'proposed',
          'description' => $requirement['description'],
          'detailed' => $requirement['detailed'],
          'relatedIDs' => [],
          'itemIDs' => [],
          'score' => 0,
          'createdBy' => $this->user['id'],
          'modifiedAt' => getCurrentTimestamp()
        ];
        
        // Handle image upload
        if( isset($_FILES['primaryImage']) && $_FILES['primaryImage']['error'] === UPLOAD_ERR_OK )
        {
          $newImage = handleFileUpload($_FILES['primaryImage'], UPLOADS_DIR . '/images');
          if( $newImage )
          {
            $newRequirement['primaryImage'] = $newImage;
          }
        }
        
        // Add to parent if specified
        if( !empty($requirement['parentId']) )
        {
          $parent = getRequirement($requirement['parentId']);
          
          if( !empty($parent) )
          {
            $parent['childIds'][] = $id;
            saveRequirement($parent);
          }
        }
        
        // Save requirement
        if( saveRequirement($newRequirement) )
        {
          $_SESSION['message'] = 'Requirement created successfully';
          $_SESSION['message_type'] = 'success';
          redirect('index.php?page=requirements&action=view&id=' . $id);
        }
        else
        {
          $error = 'Failed to create requirement';
        }
      }
    }
    
    // Render view
    $this->render(__DIR__ . '/create.php', [
      'requirement' => $requirement,
      'allRequirements' => $allRequirements,
      'error' => $error,
      'success' => $success
    ]);
  }
  
  /**
   * Edit a requirement
   */
  public function editAction()
  {
    // Check if user is logged in
    $this->requireLogin();
    
    $id = $_GET['id'] ?? '';
    
    if( empty($id) )
    {
      $_SESSION['message'] = 'Requirement ID not provided';
      $_SESSION['message_type'] = 'danger';
      redirect('index.php?page=requirements');
    }
    
    // Get requirement
    $requirement = getRequirement($id);
    
    if( empty($requirement) )
    {
      $_SESSION['message'] = 'Requirement not found';
      $_SESSION['message_type'] = 'danger';
      redirect('index.php?page=requirements');
    }
    
    // Check if user is creator or admin
    if( $requirement['createdBy'] !== $this->user['id'] )
    {
      $_SESSION['message'] = 'You do not have permission to edit this requirement';
      $_SESSION['message_type'] = 'danger';
      redirect('index.php?page=requirements&action=view&id=' . $id);
    }
    
    $error = '';
    $success = '';
    
    // Get all requirements for parent and related selection
    $allRequirements = getAllRequirements();
    
    // Remove current requirement from the list
    foreach( $allRequirements as $key => $req )
    {
      if( $req['id'] === $id )
      {
        unset($allRequirements[$key]);
        break;
      }
    }
    
    // Get all items for item selection
    $allItems = getAllItems();
    
    // Process form submission
    if( $_SERVER['REQUEST_METHOD'] === 'POST' )
    {
      // Get form data
      $requirement['name'] = $_POST['name'] ?? $requirement['name'];
      $requirement['description'] = $_POST['description'] ?? $requirement['description'];
      $requirement['detailed'] = $_POST['detailed'] ?? $requirement['detailed'];
      $requirement['status'] = $_POST['status'] ?? $requirement['status'];
      $requirement['relatedIDs'] = $_POST['relatedIDs'] ?? [];
      $requirement['itemIDs'] = $_POST['itemIDs'] ?? [];
      $requirement['modifiedAt'] = getCurrentTimestamp();
      
      // Validate input
      if( empty($requirement['name']) )
      {
        $error = 'Name is required';
      }
      elseif( empty($requirement['description']) )
      {
        $error = 'Description is required';
      }
      else
      {
        // Handle image upload
        if( isset($_FILES['primaryImage']) && $_FILES['primaryImage']['error'] === UPLOAD_ERR_OK )
        {
          $newImage = handleFileUpload($_FILES['primaryImage'], UPLOADS_DIR . '/images');
          if( $newImage )
          {
            $requirement['primaryImage'] = $newImage;
          }
        }
        
        // Save requirement
        if( saveRequirement($requirement) )
        {
          $success = 'Requirement updated successfully';
        }
        else
        {
          $error = 'Failed to update requirement';
        }
      }
    }
    
    // Render view
    $this->render(__DIR__ . '/edit.php', [
      'requirement' => $requirement,
      'allRequirements' => $allRequirements,
      'allItems' => $allItems,
      'error' => $error,
      'success' => $success
    ]);
  }
}
?>

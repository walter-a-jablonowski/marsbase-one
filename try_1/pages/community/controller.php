<?php
/**
 * Community Controller
 * Handles community projects and funding
 */

class CommunityController extends BaseController
{
  /**
   * Index action - show all community projects
   */
  public function indexAction()
  {
    // Get all community projects
    $projects = $this->getProjects();
    
    // Render view
    $this->render(__DIR__ . '/view/index.php', [
      'projects' => $projects,
      'title' => 'Community Projects'
    ]);
  }
  
  /**
   * View a single project
   */
  public function viewAction()
  {
    $this->requireLogin();
    
    $projectId = $_GET['id'] ?? '';
    if( empty($projectId) ) {
      redirect('index.php?page=community');
    }
    
    // Get project details
    $project = $this->getProject($projectId);
    if( empty($project) ) {
      redirect('index.php?page=community');
    }
    
    // Render view
    $this->render(__DIR__ . '/view/view.php', [
      'project' => $project,
      'title' => $project['name']
    ]);
  }
  
  /**
   * Fund a project (dummy implementation)
   */
  public function fundAction()
  {
    $this->requireLogin();
    
    $projectId = $_POST['project_id'] ?? '';
    $amount = (float)($_POST['amount'] ?? 0);
    
    if( empty($projectId) || $amount <= 0 ) {
      redirect('index.php?page=community');
    }
    
    // Get project
    $project = $this->getProject($projectId);
    if( empty($project) ) {
      redirect('index.php?page=community');
    }
    
    // Update funding (dummy implementation)
    $project['currentFunding'] += $amount;
    $project['backers'][] = [
      'userId' => $this->user['id'],
      'amount' => $amount,
      'timestamp' => getCurrentTimestamp()
    ];
    
    // Save project
    $this->saveProject($project);
    
    // Redirect back to project
    redirect('index.php?page=community&action=view&id=' . $projectId);
  }
  
  /**
   * Create a new project
   */
  public function createAction()
  {
    $this->requireLogin();
    
    if( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
      $name = $_POST['name'] ?? '';
      $description = $_POST['description'] ?? '';
      $goalAmount = (float)($_POST['goal_amount'] ?? 0);
      $itemId = $_POST['item_id'] ?? '';
      
      if( !empty($name) && !empty($description) && $goalAmount > 0 ) {
        // Create new project
        $project = [
          'id' => md5($name . time()),
          'name' => $name,
          'description' => $description,
          'goalAmount' => $goalAmount,
          'currentFunding' => 0,
          'itemId' => $itemId,
          'userId' => $this->user['id'],
          'createdAt' => getCurrentTimestamp(),
          'backers' => []
        ];
        
        // Save project
        $this->saveProject($project);
        
        // Redirect to project page
        redirect('index.php?page=community&action=view&id=' . $project['id']);
      }
    }
    
    // Get all items for selection
    $items = getAllItemsWithScores();
    
    // Render view
    $this->render(__DIR__ . '/view/create.php', [
      'items' => $items,
      'title' => 'Create Community Project'
    ]);
  }
  
  /**
   * Get all community projects
   */
  private function getProjects() : array
  {
    $projects = [];
    $projectsDir = DATA_DIR . '/projects';
    
    // Create directory if it doesn't exist
    if( !is_dir($projectsDir) ) {
      mkdir($projectsDir, 0755, true);
      return $projects;
    }
    
    // Get all project files
    $files = scandir($projectsDir);
    foreach( $files as $file ) {
      if( $file === '.' || $file === '..' || is_dir($projectsDir . '/' . $file) ) {
        continue;
      }
      
      // Load project data
      $project = loadYamlFile($projectsDir . '/' . $file);
      if( !empty($project) ) {
        $projects[] = $project;
      }
    }
    
    // Sort by funding percentage (highest first)
    usort($projects, function($a, $b) {
      $aPercent = ($a['currentFunding'] / $a['goalAmount']) * 100;
      $bPercent = ($b['currentFunding'] / $b['goalAmount']) * 100;
      return $bPercent <=> $aPercent;
    });
    
    return $projects;
  }
  
  /**
   * Get a specific project by ID
   */
  private function getProject( $projectId ) : array
  {
    $projectFile = DATA_DIR . '/projects/' . $projectId . '.yaml';
    if( !file_exists($projectFile) ) {
      return [];
    }
    
    return loadYamlFile($projectFile);
  }
  
  /**
   * Save a project
   */
  private function saveProject( $project ) : bool
  {
    $projectsDir = DATA_DIR . '/projects';
    
    // Create directory if it doesn't exist
    if( !is_dir($projectsDir) ) {
      mkdir($projectsDir, 0755, true);
    }
    
    $projectFile = $projectsDir . '/' . $project['id'] . '.yaml';
    return saveYamlFile($projectFile, $project);
  }
}
?>

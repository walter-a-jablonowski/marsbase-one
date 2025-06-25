<?php
/**
 * Search controller
 */

class SearchController extends BaseController
{
  /**
   * Search action
   */
  public function indexAction()
  {
    $query = $_GET['q'] ?? '';
    $filter = $_GET['filter'] ?? 'all';
    
    $requirements = [];
    $items = [];
    $users = [];
    
    if( !empty($query) )
    {
      // Search requirements
      if( $filter === 'all' || $filter === 'requirements' )
      {
        $allRequirements = getAllRequirements();
        foreach( $allRequirements as $req )
        {
          if( $this->matchesSearch($req, $query) )
          {
            $requirements[] = $req;
          }
        }
        
        // Sort by score
        usort($requirements, function($a, $b) {
          return ($b['score'] ?? 0) - ($a['score'] ?? 0);
        });
      }
      
      // Search items
      if( $filter === 'all' || $filter === 'items' )
      {
        $allItems = getAllItems();
        foreach( $allItems as $item )
        {
          if( $this->matchesSearch($item, $query) )
          {
            $items[] = $item;
          }
        }
        
        // Sort by score
        usort($items, function($a, $b) {
          return ($b['score'] ?? 0) - ($a['score'] ?? 0);
        });
      }
      
      // Search users
      if( $filter === 'all' || $filter === 'users' )
      {
        $allUsers = getAllUsers();
        foreach( $allUsers as $user )
        {
          if( $this->matchesSearch($user, $query) )
          {
            $users[] = $user;
          }
        }
        
        // Sort by name
        usort($users, function($a, $b) {
          return strcmp($a['name'], $b['name']);
        });
      }
    }
    
    // Render view
    $this->render(__DIR__ . '/view.php', [
      'query' => $query,
      'filter' => $filter,
      'requirements' => $requirements,
      'items' => $items,
      'users' => $users,
      'isLoggedIn' => $this->auth->isLoggedIn(),
      'user' => $this->user
    ]);
  }
  
  /**
   * Check if an item matches the search query
   */
  private function matchesSearch($item, $query)
  {
    $query = strtolower($query);
    $searchFields = [];
    
    // Common fields
    if( isset($item['name']) )
    {
      $searchFields[] = strtolower($item['name']);
    }
    
    if( isset($item['description']) )
    {
      $searchFields[] = strtolower($item['description']);
    }
    
    // Requirement specific fields
    if( isset($item['detailed']) )
    {
      $searchFields[] = strtolower($item['detailed']);
    }
    
    // User specific fields
    if( isset($item['bio']) )
    {
      $searchFields[] = strtolower($item['bio']);
    }
    
    if( isset($item['expertise']) )
    {
      $searchFields[] = strtolower($item['expertise']);
    }
    
    if( isset($item['location']) )
    {
      $searchFields[] = strtolower($item['location']);
    }
    
    // Item specific fields
    if( isset($item['volunteerRoles']) )
    {
      $searchFields[] = strtolower($item['volunteerRoles']);
    }
    
    // Check if any field contains the query
    foreach( $searchFields as $field )
    {
      if( strpos($field, $query) !== false )
      {
        return true;
      }
    }
    
    return false;
  }
}
?>

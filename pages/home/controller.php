<?php
/**
 * Home page controller
 */

class HomeController extends BaseController
{
  /**
   * Index action - display home page
   */
  public function indexAction()
  {
    // Get top requirements
    $requirements = getAllRequirements();
    
    // Sort by score
    usort($requirements, function($a, $b) {
      return ($b['score'] ?? 0) - ($a['score'] ?? 0);
    });
    
    // Get top items
    $items = getAllItems();
    
    // Sort by score
    usort($items, function($a, $b) {
      return ($b['score'] ?? 0) - ($a['score'] ?? 0);
    });
    
    // Limit to top 5
    $topRequirements = array_slice($requirements, 0, 5);
    $topItems = array_slice($items, 0, 5);
    
    // Render view
    $this->render(__DIR__ . '/view.php', [
      'topRequirements' => $topRequirements,
      'topItems' => $topItems,
      'isLoggedIn' => $this->auth->isLoggedIn()
    ]);
  }
}
?>

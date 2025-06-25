<?php
/**
 * Router for handling page requests
 */

class Router
{
  private $page;
  private $action;
  private $auth;
  
  public function __construct()
  {
    $this->auth = new Auth();
    $this->page = $_GET['page'] ?? 'home';
    $this->action = $_GET['action'] ?? 'index';
  }
  
  /**
   * Route the request to the appropriate page controller
   */
  public function route()
  {
    // Check if page exists
    $pageDir = PAGES_DIR . '/' . $this->page;
    $controllerFile = $pageDir . '/controller.php';
    
    if( !file_exists($controllerFile) )
    {
      $this->renderError('Page not found', 404);
      return;
    }
    
    // Include controller
    require_once $controllerFile;
    
    // Create controller instance
    $controllerClass = ucfirst($this->page) . 'Controller';
    if( !class_exists($controllerClass) )
    {
      $this->renderError('Controller not found', 500);
      return;
    }
    
    $controller = new $controllerClass($this->auth);
    
    // Call action method
    $actionMethod = $this->action . 'Action';
    if( !method_exists($controller, $actionMethod) )
    {
      $this->renderError('Action not found', 404);
      return;
    }
    
    // Call the action
    $controller->$actionMethod();
  }
  
  /**
   * Render error page
   */
  private function renderError( $message, $code )
  {
    http_response_code($code);
    include ROOT_DIR . '/templates/header.php';
    ?>
    <div class="container mt-5">
      <div class="alert alert-danger">
        <h1>Error <?= $code ?></h1>
        <p><?= $message ?></p>
        <a href="index.php" class="btn btn-primary">Return to Home</a>
      </div>
    </div>
    <?php
    include ROOT_DIR . '/templates/footer.php';
  }
}

/**
 * Base controller class
 */
class BaseController
{
  protected $auth;
  protected $user;
  
  public function __construct( $auth )
  {
    $this->auth = $auth;
    $this->user = $auth->getCurrentUser();
  }
  
  /**
   * Render a view
   */
  protected function render( $viewFile, $data = [] )
  {
    // Extract data to variables
    extract($data);
    
    // Include header
    include ROOT_DIR . '/templates/header.php';
    
    // Include view
    include $viewFile;
    
    // Include footer
    include ROOT_DIR . '/templates/footer.php';
  }
  
  /**
   * Check if user is logged in, redirect to login if not
   */
  protected function requireLogin()
  {
    if( !$this->auth->isLoggedIn() )
    {
      redirect('index.php?page=auth&action=login');
    }
  }
  
  /**
   * Return JSON response
   */
  protected function jsonResponse( $data )
  {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
  }
}
?>

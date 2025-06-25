<?php
require_once __DIR__ . '/vendor/autoload.php';

use Marsbase\Core\Auth;
use Marsbase\Core\Config;
use Marsbase\Core\Utils;

// Initialize auth
$auth = Auth::getInstance();
$config = Config::getInstance();

// Get current page
$page = $_GET['page'] ?? 'home';

// Define allowed pages
$allowedPages = [
  'home', 'login', 'register', 'logout', 'requirements', 
  'requirement', 'people', 'profile', 'settings'
];

// Validate page
if( !in_array($page, $allowedPages) )
{
  $page = 'home';
}

// Handle logout
if( $page === 'logout' )
{
  $auth->logout();
  Utils::redirect('index.php');
}

// Check if page requires authentication
$requiresAuth = in_array($page, ['profile', 'settings']);
if( $requiresAuth && !$auth->isLoggedIn() )
{
  Utils::setFlashError('You must be logged in to access this page.');
  Utils::redirect('index.php?page=login');
}

// Include header
include __DIR__ . '/pages/layout/header.php';

// Include page content
$pageFile = __DIR__ . "/pages/{$page}/view.php";
if( file_exists($pageFile) )
{
  include $pageFile;
}
else
{
  echo '<div class="container mt-4"><div class="alert alert-danger">Page not found.</div></div>';
}

// Include footer
include __DIR__ . '/pages/layout/footer.php';

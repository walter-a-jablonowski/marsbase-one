<?php
use Marsbase\Core\Auth;
use Marsbase\Core\Config;
use Marsbase\Core\Utils;

$auth = Auth::getInstance();
$config = Config::getInstance();
$appName = $config->get('app_name', 'MarsBase.One');
$currentPage = $_GET['page'] ?? 'home';
$flashMessage = Utils::getFlashMessage();
$flashError = Utils::getFlashError();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $appName ?> - Building the future on Mars</title>
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/mars-theme.css">
</head>
<body>
  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="index.php">
        <i class="fas fa-rocket me-2"></i><?= $appName ?>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarMain">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link <?= $currentPage === 'home' ? 'active' : '' ?>" href="index.php">
              <i class="fas fa-home me-1"></i>Home
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $currentPage === 'requirements' ? 'active' : '' ?>" href="index.php?page=requirements">
              <i class="fas fa-clipboard-list me-1"></i>Requirements
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $currentPage === 'people' ? 'active' : '' ?>" href="index.php?page=people">
              <i class="fas fa-users me-1"></i>People
            </a>
          </li>
        </ul>
        <ul class="navbar-nav">
          <?php if( $auth->isLoggedIn() ): ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                <i class="fas fa-user-astronaut me-1"></i><?= $auth->getUser()->get('name') ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li>
                  <a class="dropdown-item" href="index.php?page=profile">
                    <i class="fas fa-id-card me-1"></i>Profile
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="index.php?page=settings">
                    <i class="fas fa-cog me-1"></i>Settings
                  </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <a class="dropdown-item" href="index.php?page=logout">
                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                  </a>
                </li>
              </ul>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link <?= $currentPage === 'login' ? 'active' : '' ?>" href="index.php?page=login">
                <i class="fas fa-sign-in-alt me-1"></i>Login
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= $currentPage === 'register' ? 'active' : '' ?>" href="index.php?page=register">
                <i class="fas fa-user-plus me-1"></i>Register
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Flash Messages -->
  <?php if( $flashMessage ): ?>
    <div class="container mt-3">
      <div class="alert alert-success alert-dismissible fade show">
        <?= $flashMessage ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    </div>
  <?php endif; ?>

  <?php if( $flashError ): ?>
    <div class="container mt-3">
      <div class="alert alert-danger alert-dismissible fade show">
        <?= $flashError ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    </div>
  <?php endif; ?>

  <!-- Main Content -->
  <main class="py-4">

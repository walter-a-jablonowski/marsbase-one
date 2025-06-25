<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= APP_NAME ?></title>
  
  <!-- Bootstrap 5.3 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Custom Mars Colony Theme CSS -->
  <link href="assets/css/mars-theme.css" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="mars-theme">
  <nav class="navbar navbar-expand-lg navbar-dark bg-mars">
    <div class="container">
      <a class="navbar-brand" href="index.php">
        <i class="fas fa-rocket me-2"></i> <?= APP_NAME ?>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarMain">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link" href="index.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="index.php?page=requirements">Requirements</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="index.php?page=items">Items</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="index.php?page=community">Community</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="index.php?page=users&action=index">Users</a>
          </li>
        </ul>
        
        <div class="d-flex">
          <form class="d-flex me-2" action="index.php" method="get">
            <input type="hidden" name="page" value="search">
            <input class="form-control me-2" type="search" name="q" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-light" type="submit">
              <i class="fas fa-search"></i>
            </button>
          </form>
          
          <?php if( isset($_SESSION['user_id']) ): ?>
            <div class="dropdown">
              <button class="btn btn-mars dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <?= $_SESSION['user_name'] ?>
              </button>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="index.php?page=profile">My Profile</a></li>
                <li><a class="dropdown-item" href="index.php?page=profile&action=favorites">My Favorites</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="index.php?page=auth&action=logout">Logout</a></li>
              </ul>
            </div>
          <?php else: ?>
            <a href="index.php?page=auth&action=login" class="btn btn-outline-light me-2">Login</a>
            <a href="index.php?page=auth&action=register" class="btn btn-mars">Register</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </nav>

  <div class="content-wrapper">
    <?php if( isset($_SESSION['message']) ): ?>
      <div class="container mt-3">
        <div class="alert alert-<?= $_SESSION['message_type'] ?? 'info' ?> alert-dismissible fade show">
          <?= $_SESSION['message'] ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      </div>
      <?php 
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
      ?>
    <?php endif; ?>

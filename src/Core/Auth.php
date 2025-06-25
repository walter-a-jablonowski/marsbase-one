<?php

namespace Marsbase\Core;

use Marsbase\Models\User;

class Auth
{
  private static $instance = null;
  private $user = null;

  private function __construct()
  {
    $this->initSession();
    $this->loadUser();
  }

  public static function getInstance() : self
  {
    if( self::$instance === null )
    {
      self::$instance = new self();
    }
    return self::$instance;
  }

  private function initSession() : void
  {
    if( session_status() === PHP_SESSION_NONE )
    {
      session_start([
        'cookie_lifetime' => Config::getInstance()->get('session_lifetime', 86400),
        'cookie_httponly' => true,
        'cookie_secure' => isset($_SERVER['HTTPS']),
        'cookie_samesite' => 'Lax'
      ]);
    }
  }

  private function loadUser() : void
  {
    if( isset($_SESSION['user_id']) )
    {
      $this->user = new User($_SESSION['user_id']);
      
      if( !$this->user->exists() )
      {
        $this->user = null;
        unset($_SESSION['user_id']);
      }
    }
  }

  public function login( string $email, string $password ) : bool
  {
    $user = User::findByEmail($email);
    
    if( $user && $user->verifyPassword($password) )
    {
      $_SESSION['user_id'] = $user->getId();
      $this->user = $user;
      return true;
    }
    
    return false;
  }

  public function logout() : void
  {
    $this->user = null;
    unset($_SESSION['user_id']);
    session_destroy();
  }

  public function isLoggedIn() : bool
  {
    return $this->user !== null;
  }

  public function getUser() : ?User
  {
    return $this->user;
  }

  public function getUserId() : ?string
  {
    return $this->user ? $this->user->getId() : null;
  }

  public function register( array $userData ) : bool
  {
    $user = User::create($userData);
    
    if( $user )
    {
      $_SESSION['user_id'] = $user->getId();
      $this->user = $user;
      return true;
    }
    
    return false;
  }
}

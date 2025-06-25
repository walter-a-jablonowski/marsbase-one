<?php

namespace Marsbase\Core;

class Utils
{
  public static function redirect( string $url ) : void
  {
    header("Location: $url");
    exit;
  }

  public static function generateBreadcrumb( array $items ) : string
  {
    $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
    
    foreach( $items as $index => $item )
    {
      $isLast = $index === count($items) - 1;
      $activeClass = $isLast ? ' active" aria-current="page' : '';
      
      if( $isLast || empty($item['url']) )
      {
        $html .= "<li class=\"breadcrumb-item{$activeClass}\">{$item['text']}</li>";
      }
      else
      {
        $html .= "<li class=\"breadcrumb-item\"><a href=\"{$item['url']}\">{$item['text']}</a></li>";
      }
    }
    
    $html .= '</ol></nav>';
    return $html;
  }

  public static function formatDate( string $dateString ) : string
  {
    $date = new \DateTime($dateString);
    return $date->format('M j, Y \a\t g:i a');
  }

  public static function truncate( string $text, int $length = 100 ) : string
  {
    if( strlen($text) <= $length )
    {
      return $text;
    }
    
    return substr($text, 0, $length) . '...';
  }

  public static function getFlashMessage() : ?string
  {
    if( isset($_SESSION['flash_message']) )
    {
      $message = $_SESSION['flash_message'];
      unset($_SESSION['flash_message']);
      return $message;
    }
    
    return null;
  }

  public static function setFlashMessage( string $message ) : void
  {
    $_SESSION['flash_message'] = $message;
  }

  public static function getFlashError() : ?string
  {
    if( isset($_SESSION['flash_error']) )
    {
      $error = $_SESSION['flash_error'];
      unset($_SESSION['flash_error']);
      return $error;
    }
    
    return null;
  }

  public static function setFlashError( string $error ) : void
  {
    $_SESSION['flash_error'] = $error;
  }

  public static function sanitizeInput( $input )
  {
    if( is_array($input) )
    {
      foreach( $input as $key => $value )
      {
        $input[$key] = self::sanitizeInput($value);
      }
      return $input;
    }
    
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
  }

  public static function isAjaxRequest() : bool
  {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
  }

  public static function jsonResponse( $data, int $statusCode = 200 ) : void
  {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
  }
}

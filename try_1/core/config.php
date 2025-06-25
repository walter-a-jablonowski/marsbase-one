<?php
/**
 * Configuration settings for the application
 */

// Application settings
define('APP_NAME', 'Marsbase.one');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/marsbase-one');

// Directory settings
define('ROOT_DIR', __DIR__ . '/..');
define('DATA_DIR', ROOT_DIR . '/data');
define('USERS_DIR', DATA_DIR . '/users');
define('REQUIREMENTS_DIR', DATA_DIR . '/requirements');
define('ITEMS_DIR', DATA_DIR . '/items');
define('UPLOADS_DIR', ROOT_DIR . '/uploads');
define('PAGES_DIR', ROOT_DIR . '/pages');

// Create data directories if they don't exist
$directories = [
  DATA_DIR,
  USERS_DIR,
  REQUIREMENTS_DIR,
  ITEMS_DIR,
  UPLOADS_DIR,
  UPLOADS_DIR . '/images'
];

foreach( $directories as $dir )
{
  if( !file_exists($dir) )
  {
    mkdir($dir, 0777, true);
  }
}

// Include Composer autoloader if it exists
if( file_exists(ROOT_DIR . '/vendor/autoload.php') )
{
  require_once ROOT_DIR . '/vendor/autoload.php';
}
?>

<?php
/**
 * YAML utility functions
 */

// Ensure autoloader is included
if( !class_exists('\Symfony\Component\Yaml\Yaml') ) {
  require_once __DIR__ . '/../vendor/autoload.php';
}

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * Load data from a YAML file
 */
function loadYamlFile($filePath)
{
  if( !file_exists($filePath) )
  {
    return null;
  }
  
  try
  {
    $data = Yaml::parseFile($filePath);
    return $data;
  }
  catch( ParseException $e )
  {
    error_log('Error parsing YAML file: ' . $e->getMessage());
    return null;
  }
}

/**
 * Save data to a YAML file
 */
function saveYamlFile($filePath, $data)
{
  try
  {
    // Create directory if it doesn't exist
    $dir = dirname($filePath);
    if( !is_dir($dir) )
    {
      mkdir($dir, 0755, true);
    }
    
    $yaml = Yaml::dump($data, 4);
    file_put_contents($filePath, $yaml);
    return true;
  }
  catch( \Exception $e )
  {
    error_log('Error saving YAML file: ' . $e->getMessage());
    return false;
  }
}

// Note: formatTimestamp function is now in core/functions.php

// Note: getCurrentTimestamp function is now in core/functions.php

// Note: calculateScore function is now in core/functions.php
?>

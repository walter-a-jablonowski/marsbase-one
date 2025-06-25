<?php

namespace Marsbase\Core;

use Symfony\Component\Yaml\Yaml;

class Config
{
  private static $instance = null;
  private $config = [];

  private function __construct()
  {
    $this->loadConfig();
  }

  public static function getInstance() : self
  {
    if( self::$instance === null )
    {
      self::$instance = new self();
    }
    return self::$instance;
  }

  private function loadConfig() : void
  {
    $configFile = dirname(dirname(__DIR__)) . '/config.yml';
    
    if( file_exists($configFile) )
    {
      $this->config = Yaml::parseFile($configFile);
    }
    else
    {
      throw new \Exception("Configuration file not found");
    }
  }

  public function get( $key, $default = null )
  {
    $keys = explode('.', $key);
    $value = $this->config;
    
    foreach( $keys as $k )
    {
      if( !isset($value[$k]) )
      {
        return $default;
      }
      $value = $value[$k];
    }
    
    return $value;
  }

  public function getDataPath() : string
  {
    return dirname(dirname(__DIR__)) . '/' . $this->get('data_path', 'data');
  }

  public function getUsersPath() : string
  {
    return dirname(dirname(__DIR__)) . '/' . $this->get('users_path', 'data/users');
  }

  public function getRequirementsPath() : string
  {
    return dirname(dirname(__DIR__)) . '/' . $this->get('requirements_path', 'data/requirements');
  }

  public function getSolutionsPath() : string
  {
    return dirname(dirname(__DIR__)) . '/' . $this->get('solutions_path', 'data/solutions');
  }
}

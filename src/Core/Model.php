<?php

namespace Marsbase\Core;

use Symfony\Component\Yaml\Yaml;

abstract class Model
{
  protected $data = [];
  protected $id;
  protected $basePath;
  protected $dataFile;

  public function __construct( string $id = null )
  {
    $this->id = $id;
    $this->basePath = $this->getBasePath();
    
    if( $id )
    {
      $this->dataFile = $this->basePath . '/' . $id . '/data.yml';
      $this->load();
    }
  }

  abstract protected function getBasePath() : string;

  public function getId() : ?string
  {
    return $this->id;
  }

  public function load() : bool
  {
    if( !$this->id || !file_exists($this->dataFile) )
    {
      return false;
    }

    $lock = new FileLock($this->dataFile);
    
    if( $lock->acquire() )
    {
      try
      {
        $this->data = Yaml::parseFile($this->dataFile);
        return true;
      }
      catch( \Exception $e )
      {
        // Log error
        error_log("Error loading YAML file: " . $e->getMessage());
        return false;
      }
      finally
      {
        $lock->release();
      }
    }
    
    return false;
  }

  public function save() : bool
  {
    if( !$this->id )
    {
      return false;
    }

    // Ensure directory exists
    $dir = $this->basePath . '/' . $this->id;
    if( !is_dir($dir) )
    {
      mkdir($dir, 0755, true);
      mkdir($dir . '/uploads', 0755, true);
    }

    $lock = new FileLock($this->dataFile);
    
    if( $lock->acquire() )
    {
      try
      {
        // Update modified timestamp
        $this->data['modifiedAt'] = date('Y-m-d H:i:s');
        
        // Save data to file
        file_put_contents($this->dataFile, Yaml::dump($this->data, 4));
        return true;
      }
      catch( \Exception $e )
      {
        // Log error
        error_log("Error saving YAML file: " . $e->getMessage());
        return false;
      }
      finally
      {
        $lock->release();
      }
    }
    
    return false;
  }

  public function set( $key, $value ) : self
  {
    $this->data[$key] = $value;
    return $this;
  }

  public function get( $key, $default = null )
  {
    return $this->data[$key] ?? $default;
  }

  public function getData() : array
  {
    return $this->data;
  }

  public function exists() : bool
  {
    return file_exists($this->dataFile);
  }

  public static function generateId( $length = 8 ) : string
  {
    $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $id = '';
    
    for( $i = 0; $i < $length; $i++ )
    {
      $id .= $chars[random_int(0, strlen($chars) - 1)];
    }
    
    return $id;
  }

  public static function findAll() : array
  {
    $items = [];
    $class = get_called_class();
    $basePath = (new $class())->getBasePath();
    
    if( !is_dir($basePath) )
    {
      return $items;
    }
    
    $dirs = scandir($basePath);
    
    foreach( $dirs as $dir )
    {
      if( $dir === '.' || $dir === '..' )
      {
        continue;
      }
      
      if( is_dir($basePath . '/' . $dir) && file_exists($basePath . '/' . $dir . '/data.yml') )
      {
        $items[] = new $class($dir);
      }
    }
    
    return $items;
  }
}

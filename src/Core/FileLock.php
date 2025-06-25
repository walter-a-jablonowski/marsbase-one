<?php

namespace Marsbase\Core;

class FileLock
{
  private $lockFile;
  private $lockHandle;
  private $timeout;

  public function __construct( string $targetFile )
  {
    $this->lockFile = $targetFile . '.lock';
    $this->timeout = Config::getInstance()->get('file_lock_timeout', 30);
  }

  public function acquire() : bool
  {
    $startTime = time();
    
    while( true )
    {
      // Try to create lock file
      $this->lockHandle = @fopen($this->lockFile, 'x');
      
      if( $this->lockHandle !== false )
      {
        // Successfully created lock file
        fwrite($this->lockHandle, (string)getmypid());
        return true;
      }
      
      // Check if lock is stale
      if( file_exists($this->lockFile) )
      {
        $lockTime = @filemtime($this->lockFile);
        if( $lockTime && (time() - $lockTime > $this->timeout) )
        {
          // Lock is stale, remove it
          @unlink($this->lockFile);
          continue;
        }
      }
      
      // Check if we've timed out
      if( time() - $startTime > $this->timeout )
      {
        return false;
      }
      
      // Wait a bit before trying again
      usleep(100000); // 100ms
    }
  }

  public function release() : void
  {
    if( $this->lockHandle )
    {
      fclose($this->lockHandle);
      @unlink($this->lockFile);
      $this->lockHandle = null;
    }
  }

  public function __destruct()
  {
    $this->release();
  }
}

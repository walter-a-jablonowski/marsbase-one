<?php

namespace Marsbase\Core;

class FileUpload
{
  private $config;
  private $allowedTypes;
  private $maxSize;
  private $uploadPath;
  private $errors = [];

  public function __construct( string $uploadPath )
  {
    $this->config = Config::getInstance();
    $this->allowedTypes = $this->config->get('allowed_image_types', [
      'image/jpeg',
      'image/png',
      'image/gif'
    ]);
    $this->maxSize = $this->config->get('max_upload_size', 5242880); // 5MB default
    $this->uploadPath = $uploadPath;
    
    // Create upload directory if it doesn't exist
    if( !is_dir($this->uploadPath) )
    {
      mkdir($this->uploadPath, 0755, true);
    }
  }

  public function upload( array $file ) : ?string
  {
    // Check for errors
    if( $file['error'] !== UPLOAD_ERR_OK )
    {
      $this->errors[] = $this->getUploadErrorMessage($file['error']);
      return null;
    }
    
    // Check file type
    $finfo = new \finfo(FILEINFO_MIME_TYPE);
    $type = $finfo->file($file['tmp_name']);
    
    if( !in_array($type, $this->allowedTypes) )
    {
      $this->errors[] = "File type not allowed. Allowed types: " . implode(', ', $this->allowedTypes);
      return null;
    }
    
    // Check file size
    if( $file['size'] > $this->maxSize )
    {
      $this->errors[] = "File too large. Maximum size: " . $this->formatSize($this->maxSize);
      return null;
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = bin2hex(random_bytes(16)) . '.' . $extension;
    $destination = $this->uploadPath . '/' . $filename;
    
    // Move uploaded file
    if( !move_uploaded_file($file['tmp_name'], $destination) )
    {
      $this->errors[] = "Failed to move uploaded file.";
      return null;
    }
    
    return $filename;
  }

  public function getErrors() : array
  {
    return $this->errors;
  }

  private function getUploadErrorMessage( int $error ) : string
  {
    switch( $error )
    {
      case UPLOAD_ERR_INI_SIZE:
        return "The uploaded file exceeds the upload_max_filesize directive in php.ini.";
      case UPLOAD_ERR_FORM_SIZE:
        return "The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form.";
      case UPLOAD_ERR_PARTIAL:
        return "The uploaded file was only partially uploaded.";
      case UPLOAD_ERR_NO_FILE:
        return "No file was uploaded.";
      case UPLOAD_ERR_NO_TMP_DIR:
        return "Missing a temporary folder.";
      case UPLOAD_ERR_CANT_WRITE:
        return "Failed to write file to disk.";
      case UPLOAD_ERR_EXTENSION:
        return "A PHP extension stopped the file upload.";
      default:
        return "Unknown upload error.";
    }
  }

  private function formatSize( int $bytes ) : string
  {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = 0;
    
    while( $bytes >= 1024 && $i < count($units) - 1 )
    {
      $bytes /= 1024;
      $i++;
    }
    
    return round($bytes, 2) . ' ' . $units[$i];
  }
}

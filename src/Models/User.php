<?php

namespace Marsbase\Models;

use Marsbase\Core\Config;
use Marsbase\Core\Model;

class User extends Model
{
  protected function getBasePath() : string
  {
    return Config::getInstance()->getUsersPath();
  }

  public static function findByEmail( string $email ) : ?User
  {
    $users = self::findAll();
    
    foreach( $users as $user )
    {
      if( $user->get('email') === $email )
      {
        return $user;
      }
    }
    
    return null;
  }

  public static function create( array $userData ) : ?User
  {
    // Check if email already exists
    if( self::findByEmail($userData['email']) )
    {
      return null;
    }
    
    // Generate unique ID
    $id = self::generateId();
    
    // Create new user
    $user = new self($id);
    
    // Set user data
    $user->data = $userData;
    $user->data['id'] = $id;
    
    // Hash password if provided
    if( isset($userData['password']) && !empty($userData['password']) )
    {
      $user->setPassword($userData['password']);
    }
    
    // Initialize arrays
    $user->data['memberIds'] = $userData['memberIds'] ?? [];
    $user->data['followedItemIds'] = $userData['followedItemIds'] ?? [];
    $user->data['followedReqIds'] = $userData['followedReqIds'] ?? [];
    $user->data['followedUserIds'] = $userData['followedUserIds'] ?? [];
    $user->data['itemScores'] = $userData['itemScores'] ?? [];
    $user->data['reqScores'] = $userData['reqScores'] ?? [];
    
    // Set creation time
    $user->data['modifiedAt'] = date('Y-m-d H:i:s');
    
    // Save user
    if( $user->save() )
    {
      return $user;
    }
    
    return null;
  }

  public function setPassword( string $password ) : self
  {
    $this->data['password'] = password_hash($password, PASSWORD_DEFAULT);
    return $this;
  }

  public function verifyPassword( string $password ) : bool
  {
    return password_verify($password, $this->data['password'] ?? '');
  }

  public function getRequirementScore( string $reqId ) : int
  {
    $reqScores = $this->get('reqScores', []);
    
    foreach( $reqScores as $score )
    {
      if( $score['reqId'] === $reqId )
      {
        return $score['score'];
      }
    }
    
    return 0;
  }

  public function setRequirementScore( string $reqId, int $score ) : bool
  {
    $reqScores = $this->get('reqScores', []);
    $found = false;
    
    // Update existing score or add new one
    foreach( $reqScores as &$existingScore )
    {
      if( $existingScore['reqId'] === $reqId )
      {
        $existingScore['score'] = $score;
        $found = true;
        break;
      }
    }
    
    if( !$found )
    {
      $reqScores[] = [
        'reqId' => $reqId,
        'score' => $score
      ];
    }
    
    $this->set('reqScores', $reqScores);
    return $this->save();
  }

  public function getItemScore( string $itemId ) : int
  {
    $itemScores = $this->get('itemScores', []);
    
    foreach( $itemScores as $score )
    {
      if( $score['itemId'] === $itemId )
      {
        return $score['score'];
      }
    }
    
    return 0;
  }

  public function setItemScore( string $itemId, int $score ) : bool
  {
    $itemScores = $this->get('itemScores', []);
    $found = false;
    
    // Update existing score or add new one
    foreach( $itemScores as &$existingScore )
    {
      if( $existingScore['itemId'] === $itemId )
      {
        $existingScore['score'] = $score;
        $found = true;
        break;
      }
    }
    
    if( !$found )
    {
      $itemScores[] = [
        'itemId' => $itemId,
        'score' => $score
      ];
    }
    
    $this->set('itemScores', $itemScores);
    return $this->save();
  }

  public function followRequirement( string $reqId ) : bool
  {
    $followedReqIds = $this->get('followedReqIds', []);
    
    if( !in_array($reqId, $followedReqIds) )
    {
      $followedReqIds[] = $reqId;
      $this->set('followedReqIds', $followedReqIds);
      return $this->save();
    }
    
    return true;
  }

  public function unfollowRequirement( string $reqId ) : bool
  {
    $followedReqIds = $this->get('followedReqIds', []);
    
    if( in_array($reqId, $followedReqIds) )
    {
      $followedReqIds = array_filter($followedReqIds, function($id) use ($reqId) {
        return $id !== $reqId;
      });
      $this->set('followedReqIds', array_values($followedReqIds));
      return $this->save();
    }
    
    return true;
  }

  public function followItem( string $itemId ) : bool
  {
    $followedItemIds = $this->get('followedItemIds', []);
    
    if( !in_array($itemId, $followedItemIds) )
    {
      $followedItemIds[] = $itemId;
      $this->set('followedItemIds', $followedItemIds);
      return $this->save();
    }
    
    return true;
  }

  public function unfollowItem( string $itemId ) : bool
  {
    $followedItemIds = $this->get('followedItemIds', []);
    
    if( in_array($itemId, $followedItemIds) )
    {
      $followedItemIds = array_filter($followedItemIds, function($id) use ($itemId) {
        return $id !== $itemId;
      });
      $this->set('followedItemIds', array_values($followedItemIds));
      return $this->save();
    }
    
    return true;
  }

  public function followUser( string $userId ) : bool
  {
    $followedUserIds = $this->get('followedUserIds', []);
    
    if( !in_array($userId, $followedUserIds) )
    {
      $followedUserIds[] = $userId;
      $this->set('followedUserIds', $followedUserIds);
      return $this->save();
    }
    
    return true;
  }

  public function unfollowUser( string $userId ) : bool
  {
    $followedUserIds = $this->get('followedUserIds', []);
    
    if( in_array($userId, $followedUserIds) )
    {
      $followedUserIds = array_filter($followedUserIds, function($id) use ($userId) {
        return $id !== $userId;
      });
      $this->set('followedUserIds', array_values($followedUserIds));
      return $this->save();
    }
    
    return true;
  }
}

<?php

namespace Marsbase\Models;

use Marsbase\Core\Config;
use Marsbase\Core\Model;

class Requirement extends Model
{
  protected function getBasePath() : string
  {
    return Config::getInstance()->getRequirementsPath();
  }

  public static function create( array $reqData, string $creatorId ) : ?Requirement
  {
    // Generate unique ID
    $id = self::generateId();
    
    // Create new requirement
    $requirement = new self($id);
    
    // Set requirement data
    $requirement->data = $reqData;
    $requirement->data['id'] = $id;
    $requirement->data['createdBy'] = $creatorId;
    
    // Initialize arrays
    $requirement->data['parentIds'] = $reqData['parentIds'] ?? [];
    $requirement->data['childIds'] = $reqData['childIds'] ?? [];
    $requirement->data['relatedIds'] = $reqData['relatedIds'] ?? [];
    $requirement->data['userIds'] = $reqData['userIds'] ?? [$creatorId];
    $requirement->data['itemIds'] = $reqData['itemIds'] ?? [];
    $requirement->data['images'] = $reqData['images'] ?? [];
    
    // Set status to proposed by default
    if( !isset($reqData['status']) )
    {
      $requirement->data['status'] = 'proposed';
    }
    
    // Set creation time
    $requirement->data['modifiedAt'] = date('Y-m-d H:i:s');
    
    // Save requirement
    if( $requirement->save() )
    {
      // If this is a child requirement, update parent
      if( !empty($reqData['parentIds']) )
      {
        foreach( $reqData['parentIds'] as $parentId )
        {
          $parent = new self($parentId);
          if( $parent->exists() )
          {
            $childIds = $parent->get('childIds', []);
            if( !in_array($id, $childIds) )
            {
              $childIds[] = $id;
              $parent->set('childIds', $childIds);
              $parent->save();
            }
          }
        }
      }
      
      return $requirement;
    }
    
    return null;
  }

  public function addChild( string $childId ) : bool
  {
    $childIds = $this->get('childIds', []);
    
    if( !in_array($childId, $childIds) )
    {
      $childIds[] = $childId;
      $this->set('childIds', $childIds);
      
      // Also update the child to include this as parent
      $child = new self($childId);
      if( $child->exists() )
      {
        $parentIds = $child->get('parentIds', []);
        if( !in_array($this->id, $parentIds) )
        {
          $parentIds[] = $this->id;
          $child->set('parentIds', $parentIds);
          $child->save();
        }
      }
      
      return $this->save();
    }
    
    return true;
  }

  public function removeChild( string $childId ) : bool
  {
    $childIds = $this->get('childIds', []);
    
    if( in_array($childId, $childIds) )
    {
      $childIds = array_filter($childIds, function($id) use ($childId) {
        return $id !== $childId;
      });
      $this->set('childIds', array_values($childIds));
      
      // Also update the child to remove this as parent
      $child = new self($childId);
      if( $child->exists() )
      {
        $parentIds = $child->get('parentIds', []);
        $parentIds = array_filter($parentIds, function($id) {
          return $id !== $this->id;
        });
        $child->set('parentIds', array_values($parentIds));
        $child->save();
      }
      
      return $this->save();
    }
    
    return true;
  }

  public function addRelated( string $relatedId ) : bool
  {
    $relatedIds = $this->get('relatedIds', []);
    
    if( !in_array($relatedId, $relatedIds) )
    {
      $relatedIds[] = $relatedId;
      $this->set('relatedIds', $relatedIds);
      
      // Also update the related requirement to include this as related
      $related = new self($relatedId);
      if( $related->exists() )
      {
        $otherRelatedIds = $related->get('relatedIds', []);
        if( !in_array($this->id, $otherRelatedIds) )
        {
          $otherRelatedIds[] = $this->id;
          $related->set('relatedIds', $otherRelatedIds);
          $related->save();
        }
      }
      
      return $this->save();
    }
    
    return true;
  }

  public function removeRelated( string $relatedId ) : bool
  {
    $relatedIds = $this->get('relatedIds', []);
    
    if( in_array($relatedId, $relatedIds) )
    {
      $relatedIds = array_filter($relatedIds, function($id) use ($relatedId) {
        return $id !== $relatedId;
      });
      $this->set('relatedIds', array_values($relatedIds));
      
      // Also update the related requirement to remove this as related
      $related = new self($relatedId);
      if( $related->exists() )
      {
        $otherRelatedIds = $related->get('relatedIds', []);
        $otherRelatedIds = array_filter($otherRelatedIds, function($id) {
          return $id !== $this->id;
        });
        $related->set('relatedIds', array_values($otherRelatedIds));
        $related->save();
      }
      
      return $this->save();
    }
    
    return true;
  }

  public function addSolution( string $itemId ) : bool
  {
    $itemIds = $this->get('itemIds', []);
    
    if( !in_array($itemId, $itemIds) )
    {
      $itemIds[] = $itemId;
      $this->set('itemIds', $itemIds);
      return $this->save();
    }
    
    return true;
  }

  public function removeSolution( string $itemId ) : bool
  {
    $itemIds = $this->get('itemIds', []);
    
    if( in_array($itemId, $itemIds) )
    {
      $itemIds = array_filter($itemIds, function($id) use ($itemId) {
        return $id !== $itemId;
      });
      $this->set('itemIds', array_values($itemIds));
      return $this->save();
    }
    
    return true;
  }

  public function canUserEdit( string $userId ) : bool
  {
    $userIds = $this->get('userIds', []);
    return in_array($userId, $userIds);
  }

  public function addEditor( string $userId ) : bool
  {
    $userIds = $this->get('userIds', []);
    
    if( !in_array($userId, $userIds) )
    {
      $userIds[] = $userId;
      $this->set('userIds', $userIds);
      return $this->save();
    }
    
    return true;
  }

  public function removeEditor( string $userId ) : bool
  {
    $userIds = $this->get('userIds', []);
    
    if( in_array($userId, $userIds) )
    {
      $userIds = array_filter($userIds, function($id) use ($userId) {
        return $id !== $userId;
      });
      $this->set('userIds', array_values($userIds));
      return $this->save();
    }
    
    return true;
  }

  public function calculateScore() : int
  {
    $score = 0;
    $users = User::findAll();
    
    foreach( $users as $user )
    {
      $score += $user->getRequirementScore($this->id);
    }
    
    return $score;
  }
}

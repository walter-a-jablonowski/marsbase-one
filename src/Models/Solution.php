<?php

namespace Marsbase\Models;

use Marsbase\Core\Config;
use Marsbase\Core\Model;

class Solution extends Model
{
  protected function getBasePath() : string
  {
    return Config::getInstance()->getSolutionsPath();
  }

  public static function create( array $solutionData, string $creatorId ) : ?Solution
  {
    // Generate unique ID
    $id = self::generateId();
    
    // Create new solution
    $solution = new self($id);
    
    // Set solution data
    $solution->data = $solutionData;
    $solution->data['id'] = $id;
    $solution->data['createdBy'] = $creatorId;
    
    // Initialize arrays
    $solution->data['requirementIds'] = $solutionData['requirementIds'] ?? [];
    $solution->data['images'] = $solutionData['images'] ?? [];
    $solution->data['contributions'] = $solutionData['contributions'] ?? [];
    
    // Set creation time
    $solution->data['modifiedAt'] = date('Y-m-d H:i:s');
    
    // Save solution
    if( $solution->save() )
    {
      // Update requirements to include this solution
      if( !empty($solutionData['requirementIds']) )
      {
        foreach( $solutionData['requirementIds'] as $reqId )
        {
          $req = new Requirement($reqId);
          if( $req->exists() )
          {
            $req->addSolution($id);
          }
        }
      }
      
      return $solution;
    }
    
    return null;
  }

  public function addRequirement( string $reqId ) : bool
  {
    $reqIds = $this->get('requirementIds', []);
    
    if( !in_array($reqId, $reqIds) )
    {
      $reqIds[] = $reqId;
      $this->set('requirementIds', $reqIds);
      
      // Also update the requirement to include this solution
      $req = new Requirement($reqId);
      if( $req->exists() )
      {
        $req->addSolution($this->id);
      }
      
      return $this->save();
    }
    
    return true;
  }

  public function removeRequirement( string $reqId ) : bool
  {
    $reqIds = $this->get('requirementIds', []);
    
    if( in_array($reqId, $reqIds) )
    {
      $reqIds = array_filter($reqIds, function($id) use ($reqId) {
        return $id !== $reqId;
      });
      $this->set('requirementIds', array_values($reqIds));
      
      // Also update the requirement to remove this solution
      $req = new Requirement($reqId);
      if( $req->exists() )
      {
        $req->removeSolution($this->id);
      }
      
      return $this->save();
    }
    
    return true;
  }

  public function addContribution( string $userId, float $amount ) : bool
  {
    $contributions = $this->get('contributions', []);
    
    $contributions[] = [
      'userId' => $userId,
      'timestamp' => date('Y-m-d H:i:s'),
      'amount' => $amount
    ];
    
    $this->set('contributions', $contributions);
    return $this->save();
  }

  public function calculateFunding() : float
  {
    $funding = 0;
    $contributions = $this->get('contributions', []);
    
    foreach( $contributions as $contribution )
    {
      $funding += $contribution['amount'];
    }
    
    return $funding;
  }

  public function calculateScore() : int
  {
    $score = 0;
    $users = User::findAll();
    
    foreach( $users as $user )
    {
      $score += $user->getItemScore($this->id);
    }
    
    return $score;
  }

  public function canUserEdit( string $userId ) : bool
  {
    return $this->get('createdBy') === $userId;
  }
}

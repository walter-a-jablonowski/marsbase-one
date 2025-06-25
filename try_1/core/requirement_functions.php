<?php
/**
 * Requirement utility functions
 *
 * Note: Core functions like getAllRequirements(), getRequirement(), and saveRequirement() are already
 * defined in core/functions.php. This file extends those with additional
 * requirement-specific functionality.
 */

/**
 * Get all requirements with scores
 * 
 * This extends the core getAllRequirements() function by calculating scores for each requirement
 */
function getAllRequirementsWithScores()
{
  $requirements = [];
  $requirementsDir = DATA_DIR . '/requirements';
  
  if( !is_dir($requirementsDir) )
  {
    return $requirements;
  }
  
  $files = scandir($requirementsDir);
  
  foreach( $files as $file )
  {
    if( $file === '.' || $file === '..' )
    {
      continue;
    }
    
    $filePath = $requirementsDir . '/' . $file;
    
    if( is_file($filePath) && pathinfo($filePath, PATHINFO_EXTENSION) === 'yaml' )
    {
      $requirement = loadYamlFile($filePath);
      
      if( !empty($requirement) )
      {
        // Calculate score
        $requirement['score'] = calculateScore($requirement);
        $requirements[] = $requirement;
      }
    }
  }
  
  return $requirements;
}

/**
 * Get requirement by ID with score calculated
 */
function getRequirementWithScore($id)
{
  $filePath = DATA_DIR . '/requirements/' . $id . '.yaml';
  
  if( !file_exists($filePath) )
  {
    return null;
  }
  
  $requirement = loadYamlFile($filePath);
  
  if( !empty($requirement) )
  {
    // Calculate score
    $requirement['score'] = calculateScore($requirement);
  }
  
  return $requirement;
}

// Note: saveRequirement function is already defined in core/functions.php

/**
 * Delete requirement
 */
function deleteRequirement($id)
{
  $filePath = DATA_DIR . '/requirements/' . $id . '.yaml';
  
  if( file_exists($filePath) )
  {
    return unlink($filePath);
  }
  
  return false;
}

/**
 * Get child requirements
 */
function getChildRequirements($parentId)
{
  $children = [];
  $allRequirements = getAllRequirementsWithScores();
  
  foreach( $allRequirements as $req )
  {
    if( isset($req['parentId']) && $req['parentId'] === $parentId )
    {
      $children[] = $req;
    }
  }
  
  return $children;
}

/**
 * Get requirement hierarchy
 * Returns an array of requirements with their children
 */
function getRequirementHierarchy()
{
  $allRequirements = getAllRequirementsWithScores();
  $topLevel = [];
  
  // First, find all top-level requirements
  foreach( $allRequirements as $req )
  {
    if( empty($req['parentId']) )
    {
      $req['children'] = [];
      $topLevel[$req['id']] = $req;
    }
  }
  
  // Then, add children to their parents
  foreach( $allRequirements as $req )
  {
    if( !empty($req['parentId']) && isset($topLevel[$req['parentId']]) )
    {
      $topLevel[$req['parentId']]['children'][] = $req;
    }
  }
  
  // Sort top-level by score
  uasort($topLevel, function($a, $b) {
    return ($b['score'] ?? 0) - ($a['score'] ?? 0);
  });
  
  // Sort children by score
  foreach( $topLevel as &$parent )
  {
    usort($parent['children'], function($a, $b) {
      return ($b['score'] ?? 0) - ($a['score'] ?? 0);
    });
  }
  
  return array_values($topLevel);
}

/**
 * Get related requirements
 */
function getRelatedRequirements($requirementId)
{
  $requirement = getRequirementWithScore($requirementId);
  
  if( empty($requirement) || empty($requirement['relatedIDs']) )
  {
    return [];
  }
  
  $related = [];
  
  foreach( $requirement['relatedIDs'] as $relatedId )
  {
    $relatedReq = getRequirementWithScore($relatedId);
    if( !empty($relatedReq) )
    {
      $related[] = $relatedReq;
    }
  }
  
  return $related;
}

/**
 * Get top requirements by score
 */
function getTopRequirements($limit = 5)
{
  $requirements = getAllRequirementsWithScores();
  
  // Sort by score
  usort($requirements, function($a, $b) {
    return ($b['score'] ?? 0) - ($a['score'] ?? 0);
  });
  
  return array_slice($requirements, 0, $limit);
}

/**
 * Get requirements created by user
 */
function getRequirementsByUser($userId)
{
  $requirements = [];
  $allRequirements = getAllRequirementsWithScores();
  
  foreach( $allRequirements as $req )
  {
    if( isset($req['createdBy']) && $req['createdBy'] === $userId )
    {
      $requirements[] = $req;
    }
  }
  
  return $requirements;
}

/**
 * Get requirements followed by user
 */
function getRequirementsFollowedByUser($userId)
{
  $user = getUser($userId);
  
  if( empty($user) || empty($user['requirementsFollowing']) )
  {
    return [];
  }
  
  $requirements = [];
  $allRequirements = getAllRequirementsWithScores();
  
  foreach( $allRequirements as $req )
  {
    if( in_array($req['id'], $user['requirementsFollowing']) )
    {
      $requirements[] = $req;
    }
  }
  
  return $requirements;
}

/**
 * Render requirement hierarchy as a nested list
 */
function renderRequirementTree($hierarchy, $currentId = null, $level = 0)
{
  $html = '<ul class="requirement-tree' . ($level === 0 ? ' top-level' : '') . '">';
  
  foreach( $hierarchy as $req )
  {
    $isActive = ($currentId === $req['id']);
    $hasChildren = !empty($req['children']);
    
    $html .= '<li class="' . ($isActive ? 'active' : '') . '">';
    $html .= '<a href="index.php?page=requirements&action=view&id=' . $req['id'] . '" class="' . ($isActive ? 'active' : '') . '">';
    $html .= '<span class="req-name">' . $req['name'] . '</span>';
    $html .= '<span class="req-score badge bg-mars ms-2">' . ($req['score'] ?? 0) . '</span>';
    $html .= '</a>';
    
    if( $hasChildren )
    {
      $html .= renderRequirementTree($req['children'], $currentId, $level + 1);
    }
    
    $html .= '</li>';
  }
  
  $html .= '</ul>';
  
  return $html;
}
?>

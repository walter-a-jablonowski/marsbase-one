<?php
use Marsbase\Core\Auth;
use Marsbase\Core\Utils;
use Marsbase\Models\Requirement;

// Check if user is logged in
$auth = Auth::getInstance();
if( !$auth->isLoggedIn() )
{
  Utils::jsonResponse(['success' => false, 'error' => 'You must be logged in to vote.']);
  exit;
}

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);
if( !$data )
{
  Utils::jsonResponse(['success' => false, 'error' => 'Invalid request data.']);
  exit;
}

// Validate input
$requirementId = $data['id'] ?? '';
$vote = $data['vote'] ?? '';

if( empty($requirementId) )
{
  Utils::jsonResponse(['success' => false, 'error' => 'Requirement ID is required.']);
  exit;
}

if( $vote !== 'up' && $vote !== 'down' && $vote !== 'none' )
{
  Utils::jsonResponse(['success' => false, 'error' => 'Invalid vote value.']);
  exit;
}

// Load requirement
$requirement = Requirement::findById($requirementId);
if( !$requirement )
{
  Utils::jsonResponse(['success' => false, 'error' => 'Requirement not found.']);
  exit;
}

// Get user
$user = $auth->getUser();
$userId = $user->getId();

// Get current score
$currentScore = $user->getRequirementScore($requirementId);

// Determine new score
$newScore = 0;
if( $vote === 'up' )
{
  $newScore = ($currentScore === 1) ? 0 : 1;
}
else if( $vote === 'down' )
{
  $newScore = ($currentScore === -1) ? 0 : -1;
}
// 'none' will set score to 0

// Update user's vote
$user->setRequirementScore($requirementId, $newScore);

// Save user data
if( !$user->save() )
{
  Utils::jsonResponse(['success' => false, 'error' => 'Failed to save vote.']);
  exit;
}

// Calculate new total score for the requirement
$totalScore = $requirement->calculateScore();

// Return success response
Utils::jsonResponse([
  'success' => true,
  'score' => $totalScore,
  'userScore' => $newScore
]);

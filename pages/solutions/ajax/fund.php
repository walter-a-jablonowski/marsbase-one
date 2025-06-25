<?php
use Marsbase\Core\Auth;
use Marsbase\Core\Utils;
use Marsbase\Models\Solution;

// Check if user is logged in
$auth = Auth::getInstance();
if( ! $auth->isLoggedIn() )
{
  Utils::jsonResponse(['success' => false, 'error' => 'You must be logged in to fund a project.']);
  exit;
}

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);
if( ! $data )
{
  Utils::jsonResponse(['success' => false, 'error' => 'Invalid request data.']);
  exit;
}

// Validate input
$solutionId = $data['solutionId'] ?? '';
$amount = $data['amount'] ?? 0;

if( empty($solutionId) )
{
  Utils::jsonResponse(['success' => false, 'error' => 'Solution ID is required.']);
  exit;
}

if( ! is_numeric($amount) || $amount <= 0 )
{
  Utils::jsonResponse(['success' => false, 'error' => 'Valid amount is required.']);
  exit;
}

// Load solution
$solution = Solution::findById($solutionId);
if( ! $solution )
{
  Utils::jsonResponse(['success' => false, 'error' => 'Solution not found.']);
  exit;
}

// Get current user
$user = $auth->getUser();
$userId = $user->getId();

// Add contribution
$contribution = [
  'userId' => $userId,
  'amount' => (float)$amount,
  'date' => date('Y-m-d H:i:s')
];

$solution->addContribution($contribution);

// Save solution data
if( ! $solution->save() )
{
  Utils::jsonResponse(['success' => false, 'error' => 'Failed to save contribution.']);
  exit;
}

// Calculate new total funding
$totalFunding = $solution->calculateTotalFunding();

// Return success response
Utils::jsonResponse([
  'success' => true,
  'totalFunding' => $totalFunding,
  'message' => 'Thank you for your contribution!'
]);

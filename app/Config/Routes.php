<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Auth Routes
$routes->post('api/auth/login', 'Auth::login');
$routes->post('api/auth/register', 'Auth::register');
$routes->get('api/auth/profile', 'Auth::profile');
$routes->post('api/auth/reset-password', 'Auth::resetPassword');
$routes->get('api/auth/logout', 'Auth::logout');

// Election Routes
$routes->get('api/elections', 'Election::index');
$routes->get('api/elections/(:num)', 'Election::show/$1');
$routes->get('api/elections/(:num)/candidates', 'Election::getCandidates/$1');
$routes->get('api/elections/(:num)/voters', 'Election::getEligibleVoters/$1');

// Vote Routes
$routes->post('api/votes', 'Vote::castVote');
$routes->get('api/votes/election/(:num)/status', 'Vote::hasVoted/$1');
$routes->get('api/votes/verify/(:num)', 'Vote::verifyVote/$1');
$routes->get('api/votes/election/(:num)/all', 'Vote::getElectionVotes/$1');
$routes->get('api/blockchain/status', 'Vote::checkBlockchainStatus');
$routes->get('api/blockchain/test-transaction', 'Vote::testBlockchainTransaction');

// Candidate Routes (Public API for candidate details)
$routes->get('api/candidates/(:num)', 'Admin::getCandidateDetail/$1');

// Verification Debug Routes - these help diagnose hash verification issues
$routes->get('api/debug/hash', 'VerificationDebug::testHash');
$routes->get('api/debug/verify/(:num)', 'VerificationDebug::debugVerifyVote/$1');

// Statistics Routes
$routes->get('api/statistics/election/(:num)', 'Statistics::getElectionResults/$1');
$routes->get('api/statistics/election/(:num)/realtime', 'Statistics::getRealtimeStats/$1');

// Admin Routes
$routes->group('api/admin', ['filter' => 'auth'], function($routes) {
    // User Management
    $routes->get('users', 'Admin::getUsers');
    $routes->get('users/(:num)', 'Admin::getUser/$1');
    $routes->post('users/(:num)/role', 'Admin::updateUserRole/$1');
    $routes->post('users/(:num)', 'Admin::updateUser/$1');
    
    // Academic Management
    $routes->post('faculties', 'Admin::createFaculty');
    $routes->get('faculties', 'Admin::getFaculties');
    $routes->get('faculties/(:num)', 'Admin::getFaculty/$1');
    $routes->put('faculties/(:num)', 'Admin::updateFaculty/$1');
    $routes->delete('faculties/(:num)', 'Admin::deleteFaculty/$1');
    
    $routes->post('departments', 'Admin::createDepartment');
    $routes->get('departments', 'Admin::getDepartments');
    $routes->get('departments/(:num)', 'Admin::getDepartment/$1');
    $routes->put('departments/(:num)', 'Admin::updateDepartment/$1');
    $routes->delete('departments/(:num)', 'Admin::deleteDepartment/$1');
    
    // Election Management
    $routes->post('elections', 'Admin::createElection');
    $routes->get('elections', 'Admin::getElections');
    $routes->get('elections/(:num)', 'Admin::getElection/$1');
    $routes->put('elections/(:num)', 'Admin::updateElection/$1');
    $routes->delete('elections/(:num)', 'Admin::deleteElection/$1');
    
    // Candidate Management
    $routes->post('candidates', 'Admin::addCandidate');
    $routes->post('candidates/paired', 'Admin::addPairedCandidates');
    $routes->get('candidates', 'Admin::getCandidates');
    $routes->get('candidates/election/(:num)', 'Admin::getCandidates/$1');
    $routes->get('candidates/(:num)', 'Admin::getCandidate/$1');
    $routes->post('candidates/(:num)', 'Admin::updateCandidate/$1');
    $routes->delete('candidates/(:num)', 'Admin::deleteCandidate/$1');
});

// Frontend Routes
$routes->get('/', 'Frontend::index');
$routes->get('login', 'Frontend::login');
$routes->get('register', 'Frontend::register');
$routes->get('logout', 'Frontend::logout');
$routes->get('dashboard', 'Frontend::dashboard');
$routes->get('elections', 'Frontend::elections');
$routes->get('election/(:num)', 'Frontend::electionDetail/$1');
$routes->get('verify-vote/(:num)', 'Frontend::verifyVote/$1');
$routes->get('verify-vote', 'Frontend::verifyVote');
$routes->get('profile', 'Frontend::profile');

// Candidate Routes
$routes->get('candidate/profile', 'Candidate::profile');
$routes->post('candidate/update/(:num)', 'Candidate::update/$1');

// Candidate Profile Routes (new implementation)
$routes->get('candidate-profile', 'CandidateProfile::index');
$routes->post('candidate-profile/update', 'CandidateProfile::update');
$routes->get('candidate-profile/switch/(:num)', 'CandidateProfile::switchCandidate/$1');

// Admin Routes
$routes->get('admin/dashboard', 'Frontend::dashboard');
$routes->get('admin/elections', 'Frontend::adminElections');
$routes->get('admin/elections/create', 'Frontend::createElection');
$routes->get('admin/elections/edit/(:num)', 'Frontend::editElection/$1');
$routes->get('admin/election/(:num)/candidates/create', 'Admin::createCandidate/$1');
$routes->post('admin/election/(:num)/candidates/store', 'Admin::storeCandidate/$1');
$routes->get('admin/users', 'Frontend::adminUsers');
$routes->get('admin/academic', 'Frontend::adminAcademic');
$routes->get('admin/reset-password', 'Frontend::adminResetPassword');

// AJAX Routes
$routes->get('api/departments/(:num)', 'Frontend::getDepartments/$1');

// Session Routes
$routes->post('set-session', 'Session::set');
$routes->get('logout', 'Session::logout');
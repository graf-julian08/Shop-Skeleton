<?php
/**
 * Logout Handler
 * Destroys session and redirects to login
 */

// Auth is already initialized in index.php
Auth::logout();

// Start new session for flash message
session_start();
$_SESSION['flash_message'] = 'Sie wurden erfolgreich abgemeldet.';

header('Location: ?page=login');
exit;

<?php
/**
 * ============================================
 * TWO-FACTOR AUTHENTICATION API
 * ============================================
 * Handles 2FA setup, enable, disable, and verify
 * ============================================
 */

// Load config and dependencies
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/system_settings.php';

Database::configure($database);
applySystemSettings();

require_once __DIR__ . '/../includes/TwoFactorAuth.php';
require_once __DIR__ . '/../includes/Auth.php';

Auth::init();

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        
        /**
         * Setup: Generate new secret + QR code + recovery codes
         * Requires: logged in
         */
        case 'setup':
            Auth::requireAuth();
            $setupData = Auth::setup2fa();
            
            if (empty($setupData)) {
                echo json_encode(['success' => false, 'error' => 'Setup fehlgeschlagen.']);
                exit;
            }
            
            echo json_encode([
                'success' => true,
                'data' => $setupData,
            ]);
            break;
        
        /**
         * Enable: Activate 2FA after verifying first code
         * Requires: logged in, POST code
         */
        case 'enable':
            Auth::requireAuth();
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'error' => 'POST erforderlich.']);
                exit;
            }
            
            $code = trim($_POST['code'] ?? '');
            
            if (empty($code)) {
                echo json_encode(['success' => false, 'error' => 'Bitte Code eingeben.']);
                exit;
            }
            
            if (Auth::enable2fa($code)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Zwei-Faktor-Authentifizierung wurde aktiviert.',
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'Ungültiger Code. Bitte versuchen Sie es erneut.',
                ]);
            }
            break;
        
        /**
         * Disable: Deactivate 2FA (requires password)
         * Requires: logged in, POST password
         */
        case 'disable':
            Auth::requireAuth();
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'error' => 'POST erforderlich.']);
                exit;
            }
            
            $password = $_POST['password'] ?? '';
            
            if (empty($password)) {
                echo json_encode(['success' => false, 'error' => 'Bitte Passwort eingeben.']);
                exit;
            }
            
            if (Auth::disable2fa($password)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Zwei-Faktor-Authentifizierung wurde deaktiviert.',
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'Falsches Passwort.',
                ]);
            }
            break;
        
        /**
         * Verify: Verify TOTP code during login (2FA pending state)
         * Requires: 2FA pending session
         */
        case 'verify':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'error' => 'POST erforderlich.']);
                exit;
            }
            
            if (!Auth::is2faPending()) {
                echo json_encode(['success' => false, 'error' => 'Keine 2FA-Verifizierung ausstehend.']);
                exit;
            }
            
            $code = trim($_POST['code'] ?? '');
            
            if (empty($code)) {
                echo json_encode(['success' => false, 'error' => 'Bitte Code eingeben.']);
                exit;
            }
            
            if (Auth::verify2fa($code)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Erfolgreich verifiziert.',
                    'redirect' => '?page=dashboard',
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'Ungültiger Code. Bitte versuchen Sie es erneut.',
                ]);
            }
            break;
        
        /**
         * Status: Check if 2FA is enabled for current user
         * Requires: logged in
         */
        case 'status':
            Auth::requireAuth();
            echo json_encode([
                'success' => true,
                'data' => [
                    'enabled' => Auth::has2faEnabled(),
                ],
            ]);
            break;
        
        default:
            echo json_encode(['success' => false, 'error' => 'Unbekannte Aktion.']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Interner Fehler: ' . $e->getMessage(),
    ]);
}

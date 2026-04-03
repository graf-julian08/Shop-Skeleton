<?php
/**
 * 2FA Code aus der Datenbank auslesen (nur für Entwicklung/Testen!)
 * 
 * Usage: Einfach im Browser aufrufen:
 * http://localhost:8080/admin/?page=login dann hier:
 * http://localhost:8080/get_2fa_code.php
 */

require_once __DIR__ . '/admin/config.php';
require_once __DIR__ . '/admin/includes/Database.php';

Database::configure($database);

header('Content-Type: text/html; charset=utf-8');

$code = Database::fetch(
    "SELECT c.code, c.expires_at, c.is_used, u.email 
     FROM auth_2fa_codes c 
     JOIN admin_users u ON c.admin_user_id = u.id 
     WHERE c.is_used = 0 AND c.expires_at > NOW() 
     ORDER BY c.created_at DESC 
     LIMIT 1"
);

echo "<!DOCTYPE html><html><head><title>2FA Code (DEV)</title>";
echo "<style>body{font-family:system-ui;background:#0f172a;color:#e2e8f0;display:flex;align-items:center;justify-content:center;height:100vh;margin:0}";
echo ".card{background:#1e293b;padding:40px;border-radius:16px;text-align:center;border:1px solid #334155}";
echo ".code{font-size:48px;font-weight:700;letter-spacing:12px;color:#7c3aed;font-family:monospace;margin:16px 0}";
echo ".info{color:#94a3b8;font-size:14px}";
echo ".warn{background:rgba(245,158,11,0.15);color:#fbbf24;padding:8px 16px;border-radius:8px;font-size:12px;margin-top:16px}</style></head><body>";

if ($code) {
    echo "<div class='card'>";
    echo "<h2>🔐 Aktueller 2FA-Code</h2>";
    echo "<div class='code'>" . htmlspecialchars($code['code']) . "</div>";
    echo "<div class='info'>Für: " . htmlspecialchars($code['email']) . "</div>";
    echo "<div class='info'>Gültig bis: " . htmlspecialchars($code['expires_at']) . "</div>";
    echo "<div class='warn'>⚠️ Diese Seite nur in der Entwicklung verwenden!</div>";
    echo "</div>";
}
else {
    echo "<div class='card'>";
    echo "<h2>Kein aktiver Code</h2>";
    echo "<div class='info'>Bitte zuerst auf der Login-Seite einloggen,<br>damit ein Code generiert wird.</div>";
    echo "<div class='info' style='margin-top:16px'><a href='/admin/?page=login' style='color:#7c3aed'>→ Zum Login</a></div>";
    echo "</div>";
}

echo "</body></html>";

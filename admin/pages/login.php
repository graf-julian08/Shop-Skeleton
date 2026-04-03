<?php
/**
 * LOGIN PAGE - 2FA FLOW
 * Schritt 1: E-Mail + Passwort -> Schritt 2: 6-stelliger 2FA-Code
 */
require_once __DIR__ . '/../includes/RateLimiter.php';

if (Auth::isFullyVerified()) {
    header('Location: ?page=dashboard');
    exit;
}

$error = '';
$success = '';
$step = 'password';
$ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

if (Auth::getAuthStatus() === 'pending_2fa') {
    $step = '2fa';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'login') {
        $rateCheck = RateLimiter::check($ip, 'login');
        if (!$rateCheck['allowed']) {
            $minutes = RateLimiter::getBlockMinutes($rateCheck['retry_after']);
            $error = "Zu viele Anmeldeversuche. Bitte warten Sie {$minutes} Minute(n).";
        } else {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            if (empty($email) || empty($password)) {
                $error = 'Bitte E-Mail und Passwort eingeben.';
            } else {
                $result = Auth::attempt($email, $password);
                if ($result['success']) {
                    RateLimiter::reset($ip, 'login');
                    $codeResult = Auth::generate2FACode();
                    $step = '2fa';
                    if ($codeResult['success']) {
                        $success = 'Ein Verifizierungscode wurde an Ihre E-Mail gesendet.';
                    } else {
                        $error = $codeResult['error'] ?? 'Code in DB gespeichert - SMTP nicht konfiguriert.';
                    }
                } else {
                    $rateResult = RateLimiter::record($ip, 'login');
                    $error = $result['error'];
                    if (!$rateResult['allowed']) {
                        $minutes = RateLimiter::getBlockMinutes($rateResult['retry_after']);
                        $error = "Zu viele Anmeldeversuche. Bitte warten Sie {$minutes} Minute(n).";
                    }
                }
            }
        }
    } elseif ($action === 'verify_2fa') {
        $step = '2fa';
        $rateCheck = RateLimiter::check($ip, '2fa_verify');
        if (!$rateCheck['allowed']) {
            $minutes = RateLimiter::getBlockMinutes($rateCheck['retry_after']);
            $error = "Zu viele Code-Versuche. Bitte warten Sie {$minutes} Minute(n).";
        } else {
            $code = trim($_POST['code'] ?? '');
            if (empty($code) || strlen($code) !== 6) {
                $error = 'Bitte geben Sie den 6-stelligen Code ein.';
            } else {
                $result = Auth::verify2FA($code);
                if ($result['success']) {
                    RateLimiter::reset($ip, '2fa_verify');
                    header('Location: ?page=dashboard');
                    exit;
                } else {
                    $rateResult = RateLimiter::record($ip, '2fa_verify');
                    $error = $result['error'];
                }
            }
        }
    } elseif ($action === 'resend_code') {
        $step = '2fa';
        if (Auth::getAuthStatus() === 'pending_2fa') {
            $codeResult = Auth::generate2FACode();
            $success = $codeResult['success'] ? 'Neuer Code gesendet.' : 'Fehler.';
        } else { $error = 'Session abgelaufen.'; $step = 'password'; }
    } elseif ($action === 'back_to_login') {
        Auth::logout(); session_start(); $step = 'password';
    }
}
$flash = $_SESSION['flash_message'] ?? ''; unset($_SESSION['flash_message']);
?>
<!DOCTYPE html><html lang="de"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Login - Admin Panel</title>
<link rel="stylesheet" href="assets/css/admin.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@600;700&display=swap" rel="stylesheet">
<style>*{box-sizing:border-box}body{min-height:100vh;display:flex;align-items:center;justify-content:center;background:#0a0e1a;font-family:'Inter',system-ui,sans-serif;margin:0}.lc{width:100%;max-width:440px;padding:20px}.card{background:rgba(30,41,59,.7);backdrop-filter:blur(24px);border-radius:20px;border:1px solid rgba(148,163,184,.1);padding:44px;box-shadow:0 20px 60px rgba(0,0,0,.4);position:relative;overflow:hidden}.card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#7c3aed,#6366f1,transparent)}.hdr{text-align:center;margin-bottom:32px}.ico{width:64px;height:64px;background:linear-gradient(135deg,#7c3aed,#6366f1);border-radius:18px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;box-shadow:0 8px 32px rgba(124,58,237,.3)}.ico .material-symbols-rounded{font-size:32px;color:#fff}.hdr h1{font-size:22px;font-weight:700;color:#f8fafc;margin:0 0 8px}.hdr p{color:#94a3b8;font-size:14px;margin:0;line-height:1.5}.msg{display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13px}.msg.e{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);color:#fca5a5}.msg.s{background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.2);color:#6ee7b7}.msg.i{background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.2);color:#93c5fd}.msg .material-symbols-rounded{font-size:18px;flex-shrink:0}.fg{margin-bottom:20px}.fl{display:block;margin-bottom:8px;font-weight:500;font-size:13px;color:#cbd5e1}.fw{position:relative}.fw .material-symbols-rounded{position:absolute;left:14px;top:50%;transform:translateY(-50%);font-size:18px;color:#64748b;pointer-events:none}.fi{width:100%;padding:13px 16px 13px 44px;border:1px solid rgba(148,163,184,.15);border-radius:10px;font-size:14px;font-family:'Inter',sans-serif;background:rgba(15,23,42,.5);color:#e2e8f0}.fi:focus{outline:none;border-color:#7c3aed;box-shadow:0 0 0 3px rgba(124,58,237,.15)}.fi::placeholder{color:#475569}.cw{display:flex;gap:8px;justify-content:center;margin:24px 0}.cd{width:52px;height:62px;text-align:center;font-size:24px;font-weight:700;font-family:'JetBrains Mono',monospace;background:rgba(15,23,42,.6);border:2px solid rgba(148,163,184,.15);border-radius:12px;color:#f8fafc}.cd:focus{outline:none;border-color:#7c3aed;box-shadow:0 0 0 3px rgba(124,58,237,.2)}.cd.filled{border-color:#7c3aed;background:rgba(124,58,237,.08)}.tm{text-align:center;font-size:13px;color:#94a3b8;margin-bottom:20px}.tm .t{color:#fbbf24;font-weight:600}.tm.exp .t{color:#ef4444}.b1{width:100%;padding:14px;font-size:15px;font-weight:600;font-family:'Inter',sans-serif;background:linear-gradient(135deg,#7c3aed,#6366f1);color:#fff;border:none;border-radius:10px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px}.b1:hover{transform:translateY(-1px);box-shadow:0 8px 24px rgba(124,58,237,.35)}.b1:disabled{opacity:.5;cursor:not-allowed;transform:none}.b1 .material-symbols-rounded{font-size:20px}.b2{width:100%;padding:12px;font-size:14px;font-weight:500;font-family:'Inter',sans-serif;background:transparent;color:#94a3b8;border:1px solid rgba(148,163,184,.15);border-radius:10px;cursor:pointer;margin-top:10px}.b2:hover{background:rgba(148,163,184,.05);color:#cbd5e1}.ft{text-align:center;margin-top:28px;font-size:12px;color:#475569}.bg{display:flex;align-items:center;justify-content:center;gap:6px;font-size:11px;color:#64748b;margin-top:20px}.bg .material-symbols-rounded{font-size:14px;color:#22c55e}</style></head>
<body><div class="lc"><div class="card">
<?php if ($step === 'password'): ?>
<div class="hdr"><div class="ico"><span class="material-symbols-rounded">storefront</span></div><h1>Admin Login</h1><p>Melden Sie sich an, um fortzufahren</p></div>
<?php if ($flash): ?><div class="msg i"><span class="material-symbols-rounded">info</span><?= htmlspecialchars($flash) ?></div><?php endif; ?>
<?php if ($error): ?><div class="msg e"><span class="material-symbols-rounded">error</span><?= htmlspecialchars($error) ?></div><?php endif; ?>
<form method="POST" action="?page=login" autocomplete="off"><input type="hidden" name="action" value="login">
<div class="fg"><label class="fl" for="email">E-Mail-Adresse</label><div class="fw"><span class="material-symbols-rounded">mail</span><input type="email" id="email" name="email" class="fi" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="admin@example.com" required autofocus></div></div>
<div class="fg"><label class="fl" for="password">Passwort</label><div class="fw"><span class="material-symbols-rounded">lock</span><input type="password" id="password" name="password" class="fi" placeholder="????????" required></div></div>
<button type="submit" class="b1"><span class="material-symbols-rounded">login</span>Anmelden</button></form>
<div class="bg"><span class="material-symbols-rounded">verified_user</span>2-Faktor-Authentifizierung</div>
<?php else: ?>
<div class="hdr"><div class="ico"><span class="material-symbols-rounded">security</span></div><h1>Verifizierung</h1><p>Code eingeben fuer<br><strong style="color:#e2e8f0"><?= htmlspecialchars($_SESSION['auth_email'] ?? '') ?></strong></p></div>
<?php if ($success): ?><div class="msg s"><span class="material-symbols-rounded">check_circle</span><?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="msg e"><span class="material-symbols-rounded">error</span><?= htmlspecialchars($error) ?></div><?php endif; ?>
<form method="POST" action="?page=login" id="vf" autocomplete="off"><input type="hidden" name="action" value="verify_2fa"><input type="hidden" name="code" id="ci" value="">
<div class="cw" id="cb"><input type="text" class="cd" maxlength="1" inputmode="numeric" data-i="0" autofocus><input type="text" class="cd" maxlength="1" inputmode="numeric" data-i="1"><input type="text" class="cd" maxlength="1" inputmode="numeric" data-i="2"><input type="text" class="cd" maxlength="1" inputmode="numeric" data-i="3"><input type="text" class="cd" maxlength="1" inputmode="numeric" data-i="4"><input type="text" class="cd" maxlength="1" inputmode="numeric" data-i="5"></div>
<div class="tm" id="tmr">Code gueltig fuer <span class="t" id="td">5:00</span></div>
<button type="submit" class="b1" id="vb" disabled><span class="material-symbols-rounded">verified</span>Code bestaetigen</button></form>
<form method="POST" action="?page=login"><input type="hidden" name="action" value="resend_code"><button type="submit" class="b2">Code erneut senden</button></form>
<form method="POST" action="?page=login"><input type="hidden" name="action" value="back_to_login"><button type="submit" class="b2">Zurueck zum Login</button></form>
<div class="bg"><span class="material-symbols-rounded">shield</span>Code via get_2fa_code.php ansehen</div>
<?php endif; ?>
<div class="ft">Admin Panel <?= date('Y') ?></div>
</div></div>
<script>(function(){var cb=document.getElementById('cb');if(!cb)return;var ds=cb.querySelectorAll('.cd'),ci=document.getElementById('ci'),vb=document.getElementById('vb'),vf=document.getElementById('vf');function u(){var c='';ds.forEach(function(d){c+=d.value});ci.value=c;vb.disabled=c.length!==6;ds.forEach(function(d){d.classList.toggle('filled',d.value.length>0)})}ds.forEach(function(d,i){d.addEventListener('input',function(){this.value=this.value.replace(/[^0-9]/g,'');if(this.value&&i<5)ds[i+1].focus();u()});d.addEventListener('keydown',function(e){if(e.key==='Backspace'&&!this.value&&i>0){ds[i-1].focus();ds[i-1].value='';u()}if(e.key==='Enter'){e.preventDefault();if(ci.value.length===6)vf.submit()}});d.addEventListener('paste',function(e){e.preventDefault();var p=(e.clipboardData||window.clipboardData).getData('text').replace(/\D/g,'');for(var j=0;j<6&&j<p.length;j++)ds[j].value=p[j];u();for(var k=0;k<ds.length;k++){if(!ds[k].value){ds[k].focus();return}}ds[5].focus()})});var td=document.getElementById('td'),tm=document.getElementById('tmr');if(td){var s=300;(function t(){var m=Math.floor(s/60),c=s%60;td.textContent=m+':'+(c<10?'0':'')+c;if(s<=0){tm.classList.add('exp');td.textContent='Abgelaufen';vb.disabled=true;return}s--;setTimeout(t,1000)})()}})();</script>
</body></html>

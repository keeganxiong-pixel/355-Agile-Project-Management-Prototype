<?php
// Login page controller
// - Redirects to board when already authenticated
// - Handles login form POST and sets user session on success
require_once '../src/auth.php';
startSession();
if (!empty($_SESSION['user_id'])) { header('Location: ' . appPath('index.php')); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle sign-in form submissions and verify the provided credentials.
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (login($username, $password)) {
        header('Location: ' . appPath('index.php'));
        exit;
    }
    // Keep the error message generic to avoid leaking whether the username exists.
    $error = 'Invalid username or password.';
}

// Show idle timeout notice
$reason = $_GET['reason'] ?? '';
$notice = '';
if ($reason === 'idle') {
    $notice = 'You were logged out due to 30 minutes of inactivity.';
} elseif ($reason === 'expired') {
    $notice = 'Your session expired after 2 hours. Please sign in again.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Agile Board — Login</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600;700&family=DM+Sans:wght@400;600;700&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{background:#0a0a0a;color:#e0e0e0;font-family:'IBM Plex Mono','Courier New',monospace;min-height:100vh;display:flex;align-items:center;justify-content:center}
.card{
    background:#111;border:1px solid #1e1e1e;
    border-radius:12px;
    padding:40px;width:360px;
    max-width:calc(100vw - 32px);
    display:flex;
    flex-direction:column;
    gap:24px;
    position:relative
}
.theme-toggle{
    position:absolute;
    top:18px;
    right:18px;
    background:#111;
    border:1px solid #333;
    color:#e0e0e0;
    border-radius:7px;
    padding:9px 13px;
    cursor:pointer;
    font-family:inherit;
    font-size:11px;
}
@media(max-width:420px){.card{padding:28px 20px;border-radius:10px}}
.logo{text-align:center}
.logo-mark{font-family:'DM Sans',sans-serif;font-size:22px;font-weight:700;color:#e0e0e0;letter-spacing:-.01em}
.logo-mark span{color:#e8734a}
.logo-sub{font-size:9px;letter-spacing:.18em;color:#444;text-transform:uppercase;margin-top:4px}
.form-group{display:flex;flex-direction:column;gap:6px}
label{font-size:9px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#888}
input{background:#0d0d0d;border:1px solid #333;border-radius:7px;color:#e0e0e0;font-family:inherit;font-size:12px;padding:10px 13px;outline:none;transition:border-color .15s;width:100%}
input:focus{border-color:#e8734a}
.btn{background:#e8734a;border:none;border-radius:7px;color:#fff;cursor:pointer;font-family:inherit;font-size:11px;font-weight:700;letter-spacing:.08em;padding:11px;transition:opacity .15s;width:100%}
.btn:hover{opacity:.85}
.error{background:#e84a4a18;border:1px solid #e84a4a66;border-radius:6px;color:#f07070;font-size:11px;padding:9px 12px;text-align:center}
.hint{background:#ffffff08;border:1px solid #2a2a2a;border-radius:6px;font-size:10px;color:#888;padding:10px 12px;line-height:1.8}
.hint strong{color:#bbb;font-weight:600}

body.light-mode{
    background:#eef2f7;
    color:#1f2937;
}

body.light-mode .card{
    background:#ffffff;
    border-color:#d6dde8;
}

body.light-mode .logo-mark{
    color:#1f2937;
}

body.light-mode .logo-sub,
body.light-mode label{
    color:#64748b;
}

body.light-mode input{
    background:#ffffff;
    border-color:#cbd5e1;
    color:#1f2937;
}

body.light-mode input::placeholder{
    color:#94a3b8;
}

body.light-mode .theme-toggle{
    background:#ffffff;
    border:1px solid #cbd5e1;
    color:#1f2937;
}
</style>
</head>
<body>
<div class="card">

<button type="button" id="themeToggle"
    class="theme-toggle"
    style="position:absolute;top:16px;right:16px;">
    Light Mode
</button>

    <div class="logo">
        <div class="logo-mark"><span>▸</span> Agile Board</div>
        <div class="logo-sub">Demo</div>
    </div>

    <?php if ($notice): ?>
    <div class="error" style="background:#e8c84a18;border-color:#e8c84a66;color:#e8c84a"><?= htmlspecialchars($notice) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <div style="display:flex;flex-direction:column;gap:14px">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="alex" autocomplete="username" required
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" autocomplete="current-password" required>
            </div>
            <button type="submit" class="btn">Sign In →</button>
        </div>
    </form>

    <div style="text-align:center;font-size:10px;color:#555">
        New here? <a href="<?= htmlspecialchars(appPath('register.php')) ?>" style="color:#e8734a;text-decoration:none">Create an account →</a>
    </div>
</div>
<script>
const toggle = document.getElementById('themeToggle');

function getSavedTheme() {
    return localStorage.getItem('theme') || localStorage.getItem('lastDarkTheme') || 'dark';
}

function applyTheme(id) {
    document.body.classList.toggle('light-mode', id === 'light');
    localStorage.setItem('theme', id);
    if (id !== 'light') {
        localStorage.setItem('lastDarkTheme', id);
    }
    toggle.textContent = id === 'light' ? 'Dark Mode' : 'Light Mode';
}

applyTheme(getSavedTheme());

toggle.addEventListener('click', () => {
    const current = getSavedTheme();
    const next = current === 'light'
        ? (localStorage.getItem('lastDarkTheme') || 'dark')
        : 'light';
    applyTheme(next);
});
</script>
</body>
</html>

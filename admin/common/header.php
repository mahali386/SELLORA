<?php
// DigitalMohan - Admin Layout Header 
require_once __DIR__ . '/../../common/config.php';
$theme = $_COOKIE['theme'] ?? 'light';
?>
<!DOCTYPE html>
<html lang="en" class="<?= $theme === 'dark' ? 'dark' : '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>DigitalMohan Admin Control Room</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            light: '#0284c7',
                            dark: '#38bdf8'
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- ChartJS library for elegant dashboard diagnostics reporting (only admin graphs) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Global fetch interceptor to support any subfolder deployment or standard PHP hosting without URL rewrite (.htaccess)
        (function() {
            const originalFetch = window.fetch;
            window.fetch = function(input, init) {
                let url = typeof input === 'string' ? input : (input instanceof URL ? input.href : (input && input.url ? input.url : ''));
                const isNodeEnv = window.location.port === '3000' || window.location.hostname.includes('run.app') || window.location.hostname.includes('localhost') || window.location.hostname.includes('github.dev') || window.location.hostname.includes('gitpod.io');
                if (!isNodeEnv && (url.startsWith('/api/') || url.includes('/api/') || url.startsWith('api/')) && !url.includes('api.php')) {
                    const isAdmin = window.location.pathname.includes('/admin/');
                    const apiScript = isAdmin ? '../api.php' : 'api.php';
                    let apiRoute = '';
                    const apiIdx = url.indexOf('/api/');
                    if (apiIdx !== -1) {
                        apiRoute = url.substring(apiIdx);
                    } else {
                        const apiMatch = url.match(/api\/.*/);
                        if (apiMatch) {
                            apiRoute = '/' + apiMatch[0];
                        } else {
                            apiRoute = url;
                        }
                    }
                    const separator = apiScript.includes('?') ? '&' : '?';
                    url = apiScript + separator + 'route=' + encodeURIComponent(apiRoute);
                    if (typeof input === 'string') {
                        input = url;
                    } else if (input instanceof URL) {
                        input = new URL(url, window.location.href);
                    } else if (input && input.url) {
                        return originalFetch(new Request(url, input), init);
                    }
                }
                return originalFetch(input, init);
            };
        })();
    </script>
    
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            user-select: none !important;
            -webkit-user-select: none !important;
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 min-h-screen pb-20 transition-colors duration-200">

<!-- Fixed Top Navigation Header -->
<header class="sticky top-0 z-40 bg-white/70 dark:bg-slate-900/70 backdrop-blur-md border-b border-slate-250/50 dark:border-white/5 transition-all">
    <div class="max-w-md mx-auto px-4 py-3.5 flex items-center justify-between">
        <a href="index.php" class="flex items-center gap-2 outline-none">
            <span class="w-2.5 h-2.5 bg-red-500 rounded-full animate-ping"></span>
            <span class="font-display font-extrabold text-xl tracking-tight bg-gradient-to-r from-slate-900 to-slate-700 dark:from-white dark:to-slate-300 bg-clip-text text-transparent">DigitalMohan Admin</span>
        </a>
        
        <div class="flex items-center gap-2">
            <!-- Theme toggle button -->
            <button class="w-9 h-9 rounded-xl flex items-center justify-center bg-slate-100 dark:bg-slate-800 text-amber-500 dark:text-blue-400 hover:scale-105 transition-all outline-none" onclick="toggleAdminThemeMode()">
                <i id="theme-admin-icon" class="fas <?= $theme === 'dark' ? 'fa-sun' : 'fa-moon' ?>"></i>
            </button>
            <a href="../index.php" class="w-9 h-9 rounded-xl flex items-center justify-center bg-sky-500/10 text-sky-500 text-xs hover:scale-105 transition-all outline-none" title="Visit Front Store">
                <i class="fas fa-arrow-up-right-from-square"></i>
            </a>
        </div>
    </div>
</header>

<script>
function triggerVibe(length = 30) {
    if (navigator.vibrate) navigator.vibrate(length);
}

function toggleAdminThemeMode() {
    triggerVibe(40);
    const html = document.documentElement;
    const isDark = html.classList.toggle('dark');
    document.cookie = `theme=${isDark ? 'dark' : 'light'};path=/;max-age=31536000`;
    
    const icon = document.getElementById('theme-admin-icon');
    if (isDark) {
        icon.className = 'fas fa-sun';
    } else {
        icon.className = 'fas fa-moon';
    }
}

const activeAdminSessionKey = localStorage.getItem("digitalmohan_current_admin") ? "digitalmohan_current_admin" : "sellora_current_admin";
function getSessionAdmin() {
    const data = localStorage.getItem(activeAdminSessionKey);
    return data ? JSON.parse(data) : null;
}
</script>

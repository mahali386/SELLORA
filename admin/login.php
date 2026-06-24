<?php
// DigitalMohan - Admin Login Center Gateway
require_once __DIR__ . '/../common/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DigitalMohan Admin Sign In</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        // Global fetch interceptor to support any subfolder deployment or standard PHP hosting without URL rewrite (.htaccess)
        (function() {
            const originalFetch = window.fetch;
            window.fetch = function(input, init) {
                let url = typeof input === 'string' ? input : (input instanceof URL ? input.href : (input && input.url ? input.url : ''));
                if ((url.startsWith('/api/') || url.includes('/api/') || url.startsWith('api/')) && !url.includes('api.php')) {
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
</head>
<body class="bg-slate-950 font-sans min-h-screen flex items-center justify-center p-6 text-slate-100">

    <div class="max-w-md w-full bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-2xl relative overflow-hidden">
        
        <span class="absolute -top-16 -right-16 w-32 h-32 bg-sky-500/15 blur-3xl rounded-full"></span>
        <span class="absolute -bottom-16 -left-16 w-32 h-32 bg-indigo-500/15 blur-3xl rounded-full"></span>

        <!-- Header -->
        <div class="text-center mb-6">
            <div class="inline-flex w-14 h-14 bg-sky-500/10 text-sky-400 border border-sky-500/20 rounded-2xl items-center justify-center text-2xl mb-3">
                <i class="fas fa-shield-halved"></i>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">Admin Gateway</h1>
            <p class="text-xs text-slate-400 mt-1">Authorized access to central sales control room panels</p>
        </div>

        <form id="admin-login-form" onsubmit="handleAdminLoginSubmit(event)" class="space-y-4">
            <div>
                <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1.5">Username</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-3 text-slate-500"><i class="fas fa-shield text-sm"></i></span>
                    <input type="text" id="admin-username" required placeholder="Enter Username ID" class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-xs font-semibold text-slate-100 outline-none focus:ring-2 focus:ring-sky-500">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1.5">Password Token</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-3 text-slate-500"><i class="fas fa-key text-sm"></i></span>
                    <input type="password" id="admin-password" required placeholder="Enter Admin Password Token" class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-xs font-semibold text-slate-100 outline-none focus:ring-2 focus:ring-sky-500">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-sky-600 to-indigo-500 hover:brightness-110 text-white rounded-xl font-bold text-xs shadow-lg active:scale-95 transition-all outline-none flex items-center justify-center gap-2">
                    <span>Unlock Control Room</span>
                    <i class="fas fa-arrow-right-long"></i>
                </button>
            </div>
        </form>

        <div class="text-center mt-6">
            <a href="../login.php" class="text-xs text-slate-400 hover:text-sky-500 font-semibold transition-colors">
                <i class="fas fa-chevron-left mr-1.5 text-[9px]"></i>Back to Customer Store
            </a>
        </div>
    </div>

<div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-3 pointer-events-none max-w-sm w-full px-4 sm:px-0"></div>

<script>
// Lightweight mock Toast for admin page boundaries
const Toast = {
    show: function(msg, type='success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `p-4 rounded-xl shadow-lg border border-white/5 backdrop-blur-md text-xs font-bold ${type === 'error' ? 'bg-red-500' : 'bg-emerald-500'} text-white transition-all transform duration-300`;
        toast.textContent = msg;
        container.appendChild(toast);
        setTimeout(() => toast.remove(), 2500);
    },
    success: function(m) { this.show(m, 'success'); },
    error: function(m) { this.show(m, 'error'); }
};

function handleAdminLoginSubmit(e) {
    if (e) e.preventDefault();
    const username = document.getElementById('admin-username').value;
    const password = document.getElementById('admin-password').value;
    
    fetch('/api/auth/admin-login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, password })
    })
    .then(async res => {
        if (!res.ok) {
            const err = await res.json();
            throw new Error(err.error || 'Server rejection');
        }
        return res.json();
    })
    .then(data => {
        if (data.success) {
            localStorage.setItem("digitalmohan_current_admin", JSON.stringify(data.admin));
            localStorage.setItem("sellora_current_admin", JSON.stringify(data.admin));
            Toast.success("Control room unlocked successfully! Loading stats.");
            setTimeout(() => {
                window.location.href = "index.php";
            }, 800);
        }
    })
    .catch(err => {
        Toast.error(err.message);
    });
}
</script>
</body>
</html>

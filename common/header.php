<?php
// Sellora - Custom Header
require_once __DIR__ . '/config.php';
$theme = $_COOKIE['theme'] ?? 'light';
?>
<!DOCTYPE html>
<html lang="en" class="<?= $theme === 'dark' ? 'dark' : '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    
    <!-- HIGH IMPACT PROFESSIONAL SEO ENGINE (DigitalMohan Ranking Optimizer) -->
    <title>DigitalMohan - Download Premium Digital Products & Prompt Packs</title>
    <meta name="description" content="DigitalMohan is the leading marketplace for high-quality digital products, master ChatGPT prompts directories, PWA resume templates, IIT-JEE revised formulae, and designer Canva assets. Download premium kits instantly.">
    <meta name="keywords" content="DigitalMohan, ChatGPT prompts pack, premium resume formats, Canva template, IIT JEE physics sheets, digital download folder directory, best prompts package, buy resumes digital downloads">
    <meta name="author" content="DigitalMohan">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="copyright" content="DigitalMohan Inc.">
    
    <!-- Open Graph Social Media Indexing Protocols -->
    <meta property="og:locale" content="en_US">
    <meta property="og:type" content="website">
    <meta property="og:title" content="DigitalMohan - Premium Digital Product Hub & Templates Directory">
    <meta property="og:description" content="Download high-performance ChatGPT prompt kits, ATS recruiter-approved resume assets, physics formulas revision sheets, and Canva bundles on DigitalMohan instantly.">
    <meta property="og:url" content="https://<?= $_SERVER['HTTP_HOST'] ?? 'digitalmohan.com' ?>/index.php">
    <meta property="og:site_name" content="DigitalMohan">
    <meta property="og:image" content="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=1200&q=80">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    
    <!-- Twitter Social Cards Matrix -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="DigitalMohan - Premium Digital Product Hub">
    <meta name="twitter:description" content="Maximize productivity. Copy paste chatgpt prompts folder, ATS resume links, revision formulae, and editable Canva layouts on DigitalMohan.">
    <meta name="twitter:image" content="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=1200&q=80">

    <!-- Preconnect CDNs to eliminate loading latency and fix initial blank screen delay -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://cdn.tailwindcss.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Structured Schema Content (Google Rich Snippets SEO Booster) -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "name": "DigitalMohan",
      "url": "https://<?= $_SERVER['HTTP_HOST'] ?? 'digitalmohan.com' ?>/",
      "description": "Premium digital templates, copy-paste prompts, resume layouts, and mock notes directory.",
      "potentialAction": {
        "@type": "SearchAction",
        "target": "https://<?= $_SERVER['HTTP_HOST'] ?? 'digitalmohan.com' ?>/products.php?search={search_term_string}",
        "query-input": "required name=search_term_string"
      }
    }
    </script>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "DigitalMohan",
      "url": "https://<?= $_SERVER['HTTP_HOST'] ?? 'digitalmohan.com' ?>/",
      "logo": "https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=200&q=80",
      "contactPoint": {
        "@type": "ContactPoint",
        "email": "support@digitalmohan.com",
        "contactType": "customer service"
      }
    }
    </script>
    
    <!-- PWA configuration link -->
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#0284C7">
    
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
    
    <!-- Tailwind CSS (via CDN) with Dark Mode Strategy -->
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
                    },
                    animation: {
                        'shimmer': 'shimmer 1.5s infinite linear',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome CDN for High Quality Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Anti-piracy - disable text selection on major contents */
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            user-select: none !important;
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            /* Disable pinch zoom */
            touch-action: pan-x pan-y;
        }
        
        /* Glassmorphism Classes */
        .glass-panel-light {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.04);
        }
        
        .glass-panel-dark {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.25);
        }

        /* Pull to refresh */
        .pull-indicator {
            transition: height 0.2s ease, opacity 0.2s ease;
        }

        /* Swiper scroll bar hide */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none; /* IE and Edge */
            scrollbar-width: none; /* Firefox */
        }

        /* Premium Micro-Interactives Click Particle CSS */
        .click-particle {
            position: fixed;
            width: 5px;
            height: 5px;
            border-radius: 50%;
            pointer-events: none;
            z-index: 1000000;
            opacity: 0.95;
            transform: translate(-50%, -50%);
            animation: particleFly 0.5s cubic-bezier(0.1, 0.8, 0.3, 1) forwards;
        }

        @keyframes particleFly {
            0% {
                transform: translate(-50%, -50%) scale(1);
                opacity: 0.95;
            }
            100% {
                transform: translate(calc(-50% + var(--dx)), calc(-50% + var(--dy))) scale(0);
                opacity: 0;
            }
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 min-h-screen pb-24 transition-colors duration-300">

<?php
// Decide inline color theme parameters dynamically based on current client state
$splashBg = ($theme === 'dark') ? '#0f172a' : '#f8fafc';
$splashTitle = ($theme === 'dark') ? '#e2e8f0' : '#0f172a';
$splashSub = ($theme === 'dark') ? '#64748b' : '#64748b';
?>
<!-- Instant Elegant Splash Loader -->
<script>
if (sessionStorage.getItem('sellora_splash_shown')) {
    document.write('<style>#app-splash-screen { display: none !important; opacity: 0 !important; visibility: hidden !important; pointer-events: none !important; }</style>');
}
</script>
<div id="app-splash-screen" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: <?= $splashBg ?>; z-index: 999999; display: flex; flex-direction: column; align-items: center; justify-content: center; transition: opacity 0.15s ease, visibility 0.15s ease; font-family: system-ui, -apple-system, sans-serif;">
    <div style="display: flex; flex-direction: column; align-items: center; gap: 14px; text-align: center;">
        <div style="width: 50px; height: 50px; border-radius: 16px; background: linear-gradient(135deg, #38bdf8 0%, #4f46e5 100%); display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px -5px rgba(56, 189, 248, 0.4); animation: splashVibe 1.6s infinite ease-in-out;">
            <svg style="width: 24px; height: 24px; fill: white;" viewBox="0 0 24 24">
                <path d="M12 2L2 7l10 5 10-5-10-5zm0 10.5L3 8v6l9 4.5 9-4.5V8l-9 4.5zm0 6L4.5 14v4l7.5 3.5 7.5-3.5v-4l-7.5 4z"/>
            </svg>
        </div>
        <h1 style="color: <?= $splashTitle ?>; font-size: 24px; font-weight: 800; letter-spacing: -0.025em; margin: 0; margin-top: 8px;">DigitalMohan</h1>
        <p style="color: <?= $splashSub ?>; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.15em; margin: 0;">Premium Digital Hub</p>
        <div style="width: 32px; height: 3px; background: #38bdf8; border-radius: 9px; margin-top: 6px; position: relative; overflow: hidden;">
            <div style="position: absolute; width: 14px; height: 100%; background: #4f46e5; border-radius: 9px; left: -14px; animation: splashLoading 1s infinite linear;"></div>
        </div>
    </div>
</div>
<script>
// High Speed Instant-Open & Fail-safe Splash Screen Dismissal
(function() {
    function dismissSplashGracefully() {
        const splash = document.getElementById('app-splash-screen');
        if (splash && splash.style.opacity !== '0') {
            splash.style.opacity = '0';
            splash.style.visibility = 'hidden';
            setTimeout(() => {
                try { splash.remove(); } catch(e) {}
            }, 160);
        }
        try {
            sessionStorage.setItem('sellora_splash_shown', '1');
        } catch (e) {}
    }
    // Dismiss extremely fast (10ms) for high-frequency butter-smooth opening feel
    setTimeout(dismissSplashGracefully, 10);
    // Extra fail-safe in case of any thread-blocks
    window.addEventListener('load', dismissSplashGracefully);
})();
</script>
<style>
@keyframes splashVibe {
    0%, 100% { transform: scale(1); rotate: 0deg; }
    50% { transform: scale(1.08); rotate: 5deg; }
}
@keyframes splashLoading {
    0% { left: -14px; }
    100% { left: 32px; }
}
</style>

<!-- Pull to Refresh Banner Simulator -->
<div id="pull-refresh" class="pull-indicator w-full h-0 opacity-0 bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden">
    <div class="flex items-center gap-2 text-primary-light dark:text-primary-dark font-medium text-sm py-2">
        <i class="fas fa-sync-alt animate-spin text-lg"></i>
        <span>Pull to reload content...</span>
    </div>
</div>

<!-- Offline Banner Detector -->
<div id="offline-banner" class="hidden fixed top-0 left-0 w-full bg-red-500 text-white text-xs font-semibold py-1.5 px-4 text-center z-50 pointer-events-none flex items-center justify-center gap-2 shadow-md">
    <i class="fas fa-wifi-slash animate-bounce"></i>
    <span>You're currently offline. Core payments and live listings are read-only.</span>
</div>

<!-- Fixed Top Navigation Header -->
<header class="sticky top-0 z-40 bg-white/70 dark:bg-slate-900/70 backdrop-blur-md border-b border-slate-200/50 dark:border-white/5 transition-colors duration-300">
    <div class="max-w-md mx-auto px-4 py-3 flex items-center justify-between">
        
        <!-- Left: Hamburger icon -->
        <button id="menu-btn" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-200 transition-all focus:outline-none" onclick="toggleSidebar()">
            <i class="fas fa-bars-staggered text-lg"></i>
        </button>
        
        <!-- Center Brand Title -->
        <a href="index.php" class="flex items-center gap-1.5 focus:outline-none">
            <span class="w-3.5 h-3.5 rounded-full bg-sky-500 animate-pulse-slow"></span>
            <span class="font-display font-bold text-2xl tracking-tight bg-gradient-to-r from-sky-600 to-indigo-500 dark:from-sky-400 dark:to-indigo-400 bg-clip-text text-transparent app-name-display">DigitalMohan</span>
        </a>
        
        <!-- Right Icons: Theme Toggle & Notification -->
        <div class="flex items-center gap-2">
            <!-- Theme Mode Toggle -->
            <button id="theme-btn" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 text-amber-500 dark:text-blue-400 hover:scale-105 transition-all focus:outline-none" onclick="toggleThemeMode()">
                <i class="fas <?= $theme === 'dark' ? 'fa-sun' : 'fa-moon' ?> text-lg"></i>
            </button>
            
            <!-- Notification bell link -->
            <button onclick="openNotificationModal()" class="relative w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:scale-105 transition-all focus:outline-none">
                <i class="fas fa-bell text-lg"></i>
                <span id="unread-count" class="absolute top-1.5 right-1.5 w-4 h-4 text-[9px] font-extrabold flex items-center justify-center bg-sky-500 text-white rounded-full scale-90 border border-white dark:border-slate-800 overflow-hidden">1</span>
            </button>
        </div>
    </div>
    
    <!-- Sticky AJAX Live Suggestions Search Bar -->
    <div class="max-w-md mx-auto px-4 pb-2">
        <div class="relative flex items-center">
            <span class="absolute left-3 text-slate-400 dark:text-slate-500">
                <i class="fas fa-search text-sm"></i>
            </span>
            <input type="text" id="live-search" placeholder="Search templates, formula sheets, prompt packs..." class="w-full pl-9 pr-9 py-2 rounded-xl bg-slate-100 dark:bg-slate-800/80 text-sm border-0 focus:ring-2 focus:ring-sky-500 text-slate-700 dark:text-slate-200 placeholder:text-slate-400 focus:bg-white dark:focus:bg-slate-800 transition-all outline-none" oninput="handleLiveSearch(this.value)">
            <button id="clear-search" class="hidden absolute right-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 outline-none" onclick="clearLiveSearch()">
                <i class="fas fa-times-circle text-sm"></i>
            </button>
        </div>
        
        <!-- Live AJAX Suggestions Container -->
        <div id="search-suggestion-box" class="hidden absolute left-4 right-4 mt-2 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50 rounded-2xl shadow-xl overflow-hidden z-50">
            <div id="suggestion-list" class="divide-y divide-slate-100 dark:divide-slate-700 max-h-60 overflow-y-auto"></div>
        </div>
    </div>

</header>

    <!-- GLOBAL NOTIFICATIONS MODAL OVERLAY -->
    <div id="notification-modal" class="hidden fixed inset-x-0 bottom-0 top-0 mx-auto max-w-md z-[100] bg-slate-950/70 backdrop-blur-md flex items-center justify-center p-4">
        <div class="w-full max-w-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-3xl p-5 shadow-2xl relative flex flex-col max-h-[85vh]">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-white/5">
                <div>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-850 dark:text-white">Alert Center</h3>
                    <p class="text-[9px] text-slate-450 dark:text-slate-500 font-bold uppercase tracking-widest">Personal Inbox & Broadcasts</p>
                </div>
                <button onclick="closeNotificationModal()" class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-white transition-all outline-none">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            
            <div id="modal-notifications-list" class="flex-grow overflow-y-auto py-4 space-y-3 no-scrollbar min-h-[160px] max-h-[45vh]">
                <!-- Filled dynamically via JS -->
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-white/5 flex gap-2">
                <button onclick="dismissAllModalNotifications()" class="flex-1 py-2.5 border border-slate-200 dark:border-white/10 hover:bg-slate-50 dark:hover:bg-slate-850/45 text-slate-700 dark:text-slate-350 font-bold text-[10px] rounded-xl active:scale-95 transition-all outline-none">
                    Dismiss All
                </button>
                <button onclick="closeNotificationModal()" class="flex-1 py-2.5 bg-slate-850 hover:bg-slate-800 dark:bg-sky-500 text-white font-bold text-[10px] rounded-xl active:scale-95 transition-all outline-none">
                    Close
                </button>
            </div>
        </div>
    </div>

<!-- Anti-Piracy, Right Click Lock and Drag Blocker JS -->
<script>
// Prevent long presses, selections, drag codes
document.addEventListener('contextmenu', e => e.preventDefault());
document.addEventListener('dragstart', e => e.preventDefault());

// Prevent keyboard shortcuts (F12, DevTools window key matches)
document.addEventListener('keydown', e => {
    // F12 keycode check
    if (e.keyCode === 123 || e.key === 'F12') {
        e.preventDefault();
        return false;
    }
    // Ctrl+Shift+I, J, C or Ctrl+U (view source check)
    if (e.ctrlKey && (e.shiftKey && (e.key === 'I' || e.key === 'J' || e.key === 'C') || e.key === 'u' || e.key === 'U')) {
        e.preventDefault();
        return false;
    }
});

// MULTI-CURRENCY GLOBAL EXCHANGE PROTOCOL
window.CURRENCY_METRICS = {
    INR: { rate: 1, symbol: "₹", name: "INR" },
    USD: { rate: 0.012, symbol: "$", name: "USD" },
    EUR: { rate: 0.011, symbol: "€", name: "EUR" }
};

window.getCurrentCurrency = function() {
    return localStorage.getItem("digitalmohan_currency") || "INR";
};

window.setCurrentCurrency = function(curr) {
    if (window.CURRENCY_METRICS[curr]) {
        localStorage.setItem("digitalmohan_currency", curr);
        window.dispatchEvent(new CustomEvent("currencychange", { detail: curr }));
    }
};

window.formatPrice = function(amountInINR) {
    if (amountInINR === 0) return "Free";
    const curr = window.getCurrentCurrency();
    const metric = window.CURRENCY_METRICS[curr] || window.CURRENCY_METRICS.INR;
    if (curr === "INR") {
        return metric.symbol + amountInINR;
    } else {
        const converted = (amountInINR * metric.rate).toFixed(2);
        return metric.symbol + converted;
    }
};

// Web Audio API Satisfying Click Synthesizer
window.playClickVibeSound = function(type = 'click') {
    try {
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (!AudioCtx) return;
        const ctx = new AudioCtx();
        
        if (type === 'bubble') {
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.frequency.setValueAtTime(450, ctx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(1300, ctx.currentTime + 0.07);
            gain.gain.setValueAtTime(0.04, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.07);
            osc.start();
            osc.stop(ctx.currentTime + 0.07);
        } else if (type === 'success') {
            const osc1 = ctx.createOscillator();
            const osc2 = ctx.createOscillator();
            const gain = ctx.createGain();
            osc1.connect(gain);
            osc2.connect(gain);
            gain.connect(ctx.destination);
            
            osc1.frequency.setValueAtTime(523.25, ctx.currentTime); // C5
            osc1.frequency.setValueAtTime(659.25, ctx.currentTime + 0.08); // E5
            osc2.frequency.setValueAtTime(783.99, ctx.currentTime); // G5
            osc2.frequency.setValueAtTime(1046.50, ctx.currentTime + 0.08); // C6
            
            gain.gain.setValueAtTime(0.05, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.22);
            osc1.start();
            osc2.start();
            osc1.stop(ctx.currentTime + 0.22);
            osc2.stop(ctx.currentTime + 0.22);
        } else {
            // Standard crisp tick click
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.frequency.setValueAtTime(290, ctx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(90, ctx.currentTime + 0.04);
            gain.gain.setValueAtTime(0.03, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.04);
            osc.start();
            osc.stop(ctx.currentTime + 0.04);
        }
    } catch(e) {}
};

// Global click event listener for particles and sound
document.addEventListener('click', e => {
    const target = e.target;
    const isInteractive = target.closest('button') || target.closest('a') || target.closest('input') || target.closest('select') || target.classList.contains('cursor-pointer');
    if (!isInteractive) return;

    // Trigger haptic sound
    const isPrimaryCTA = target.closest('.bg-gradient-to-r') || target.classList.contains('bg-sky-600') || target.classList.contains('bg-indigo-600');
    playClickVibeSound(isPrimaryCTA ? 'success' : 'click');

    // Scatter colorful premium glitter stars
    createSparkleParticles(e.clientX, e.clientY);
});

function createSparkleParticles(x, y) {
    const particleCount = 7;
    for (let i = 0; i < particleCount; i++) {
        const p = document.createElement('div');
        p.className = 'click-particle';
        p.style.left = `${x}px`;
        p.style.top = `${y}px`;
        
        const angle = Math.random() * Math.PI * 2;
        const velocity = 20 + Math.random() * 30;
        const dx = Math.cos(angle) * velocity;
        const dy = Math.sin(angle) * velocity;
        
        p.style.setProperty('--dx', `${dx}px`);
        p.style.setProperty('--dy', `${dy}px`);
        
        const colors = ['#38bdf8', '#818cf8', '#34d399', '#f43f5e', '#fbbf24'];
        p.style.background = colors[Math.floor(Math.random() * colors.length)];
        
        document.body.appendChild(p);
        setTimeout(() => { try { p.remove(); } catch(err) {} }, 500);
    }
}

// Vibration trigger for tactile feedback on click/press
function triggerVibe(length = 35) {
    if (navigator.vibrate) {
        navigator.vibrate(length);
    }
    // Also trigger sound
    playClickVibeSound('click');
}

// Global active current user session setup from storage helpers
const activeSessionKey = localStorage.getItem("digitalmohan_current_user") ? "digitalmohan_current_user" : "sellora_current_user";
function getSessionUser() {
    const data = localStorage.getItem(activeSessionKey);
    return data ? JSON.parse(data) : null;
}

// Intercept affiliate referral code parameters from URL
(function() {
    const params = new URLSearchParams(window.location.search);
    const ref = params.get('ref') || params.get('aff');
    if (ref) {
        const cleanedRef = ref.trim();
        localStorage.setItem('affiliate_referral', cleanedRef);
        
        // Log click metric to server
        fetch('/api/affiliate/click', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ref: cleanedRef })
        })
        .then(res => res.json())
        .then(data => {
            console.log('Affiliate click beacon logged.', data);
        })
        .catch(err => {
            console.warn('Affiliate click log offline.', err);
        });
    }
})();

// Global Image Optimization Helper: Converts heavy Unsplash URLs to compressed WebP and sets responsive sizes
window.getOptimizedImageUrl = function(url, width = 400) {
    if (!url) return 'https://images.unsplash.com/photo-1541963463532-d68292c34b19?w=400&q=70&fm=webp';
    if (url.startsWith('data:image')) return url; // Base64 images are kept as is
    if (url.includes('unsplash.com')) {
        try {
            const urlObj = new URL(url);
            urlObj.searchParams.set('fm', 'webp');
            urlObj.searchParams.set('q', '72');
            urlObj.searchParams.set('w', width.toString());
            return urlObj.toString();
        } catch (e) {
            return url;
        }
    }
    return url;
};

// Optimized Live Search handler with 350ms debounce and server-side limit queries to save megabytes of response volume
let searchTimeout;
function handleLiveSearch(query) {
    const box = document.getElementById('search-suggestion-box');
    const list = document.getElementById('suggestion-list');
    const clearBtn = document.getElementById('clear-search');
    
    if (!query || query.trim().length === 0) {
        box.classList.add('hidden');
        clearBtn.classList.add('hidden');
        return;
    }
    
    clearBtn.classList.remove('hidden');
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        // Performance Fix: Send query and limit directly to server-side search API to protect client bandwidth
        fetch(`/api/products?search=${encodeURIComponent(query.toLowerCase())}&limit=5`)
            .then(res => res.json())
            .then(result => {
                // Backend returns either paginated {products: []} or raw list
                const searchResults = result.products || result;
                
                if (searchResults.length === 0) {
                    list.innerHTML = `
                        <div class="p-4 text-center text-xs text-slate-400 dark:text-slate-500">
                            No match found for "${query}"
                        </div>
                    `;
                } else {
                    list.innerHTML = searchResults.map(p => `
                        <a href="product_detail.php?id=${p.id}" class="flex items-center gap-3 p-3 hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors pointer-events-auto">
                            <img src="${window.getOptimizedImageUrl(p.image, 100)}" class="w-10 h-10 object-cover rounded-lg" loading="lazy">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-semibold truncate text-slate-800 dark:text-slate-200">${p.title}</h4>
                                <span class="text-xs text-primary-light dark:text-primary-dark font-medium">${window.formatPrice(p.price)}</span>
                            </div>
                            <i class="fas fa-chevron-right text-slate-300 text-xs"></i>
                        </a>
                    `).join('');
                }
                box.classList.remove('hidden');
            });
    }, 350);
}

function clearLiveSearch() {
    const searchInput = document.getElementById('live-search');
    searchInput.value = '';
    document.getElementById('search-suggestion-box').classList.add('hidden');
    document.getElementById('clear-search').classList.add('hidden');
}

// Global pull-to-refresh
let startY = 0;
document.addEventListener('touchstart', e => {
    if (window.scrollY === 0) {
        startY = e.touches[0].pageY;
    }
}, {passive: true});

document.addEventListener('touchmove', e => {
    const y = e.touches[0].pageY;
    const dragDistance = y - startY;
    const banner = document.getElementById('pull-refresh');
    if (window.scrollY === 0 && dragDistance > 0 && dragDistance < 120) {
        banner.style.height = dragDistance + 'px';
        banner.style.opacity = (dragDistance / 120).toString();
    }
}, {passive: true});

document.addEventListener('touchend', e => {
    const banner = document.getElementById('pull-refresh');
    if (parseInt(banner.style.height) > 60) {
        banner.style.height = '60px';
        triggerVibe(80);
        setTimeout(() => {
            banner.style.height = '0px';
            banner.style.opacity = '0';
            // Trigger local listing reload smoothly
            window.location.reload();
        }, 1100);
    } else {
        banner.style.height = '0px';
        banner.style.opacity = '0';
    }
});

// Theme Selector
function toggleThemeMode() {
    triggerVibe(40);
    const html = document.documentElement;
    const isDark = html.classList.toggle('dark');
    document.cookie = `theme=${isDark ? 'dark' : 'light'};path=/;max-age=31536000`;
    
    const icon = document.getElementById('theme-btn').querySelector('i');
    if (isDark) {
        icon.className = 'fas fa-sun text-lg';
    } else {
        icon.className = 'fas fa-moon text-lg';
    }
}

// Notification center controls
function openNotificationModal() {
    triggerVibe(30);
    const user = getSessionUser();
    const modal = document.getElementById('notification-modal');
    const container = document.getElementById('modal-notifications-list');
    
    if (!modal || !container) return;
    modal.classList.remove('hidden');
    
    const fetchId = user ? user.id : 0;
    container.innerHTML = `<div class="text-center py-6 text-slate-400"><i class="fas fa-spinner animate-spin text-sm"></i></div>`;
    
    fetch(`/api/notifications/${fetchId}`)
        .then(res => res.json())
        .then(notifications => {
            if (notifications.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-bell-slash text-slate-300 dark:text-slate-700 text-3xl mb-2"></i>
                        <p class="text-[10px] text-slate-400 italic">No alerts found. Outbox completely tidy!</p>
                        ${!user ? `<p class="text-[9px] text-slate-500 mt-2">Sign in to view personal purchase alerts.</p>
                        <a href="login.php" onclick="closeNotificationModal()" class="inline-block mt-2 px-3 py-1.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-[10px] rounded-lg active:scale-95 transition-all">Sign In</a>` : ''}
                    </div>
                `;
                return;
            }
            
            notifications.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
            
            let html = notifications.map(n => `
                <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-850 border border-slate-100 dark:border-white/5 relative ${n.is_read === 0 ? 'border-l-4 border-l-sky-500' : ''}">
                    <h5 class="text-xs font-bold text-slate-850 dark:text-slate-200 leading-snug">${n.title}</h5>
                    <p class="text-[10px] text-slate-505 dark:text-slate-400 mt-1 leading-normal font-semibold">${n.message}</p>
                    <span class="text-[8px] text-slate-400 block mt-1.5">${new Date(n.created_at).toLocaleTimeString() || 'Just now'}</span>
                </div>
            `).join('');
            
            if (!user) {
                html += `
                    <div class="text-center pt-3 border-t border-slate-150 dark:border-white/5">
                        <p class="text-[9px] text-slate-500 font-bold">Sign in for personalized order alerts.</p>
                        <a href="login.php" onclick="closeNotificationModal()" class="inline-block mt-1.5 px-3 py-1 bg-sky-500 text-white rounded-lg text-[9px] font-bold">Sign In</a>
                    </div>
                `;
            }
            container.innerHTML = html;
        })
        .catch(() => {
            container.innerHTML = `<p class="text-xs text-red-500 text-center py-4">Error sync alerts.</p>`;
        });
}

function closeNotificationModal() {
    triggerVibe(15);
    const modal = document.getElementById('notification-modal');
    if (modal) modal.classList.add('hidden');
}

function dismissAllModalNotifications() {
    triggerVibe(40);
    const user = getSessionUser();
    if (!user) {
        closeNotificationModal();
        return;
    }
    
    fetch('/api/notifications/read', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ userId: user.id })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Toast.success("All notifications dismissed successfully!");
            openNotificationModal(); // Refresh container status
            const badge = document.getElementById('unread-count');
            if (badge) badge.classList.add('hidden');
        }
    });
}

// Network Online detector
window.addEventListener('online', () => {
    document.getElementById('offline-banner').classList.add('hidden');
    Toast.success("Network connection restored!");
});
window.addEventListener('offline', () => {
    document.getElementById('offline-banner').classList.remove('hidden');
    Toast.error("You are now offline. Running on cache.");
});

// Load real-time stats count and dynamic branding settings
document.addEventListener('DOMContentLoaded', () => {
    if (!navigator.onLine) {
        document.getElementById('offline-banner').classList.remove('hidden');
    }
    
    // Fetch and bind dynamic app branding name
    fetch('/api/settings')
        .then(res => res.json())
        .then(set => {
            if (set && set.app_name) {
                document.title = document.title.replace("DigitalMohan", set.app_name);
                document.querySelectorAll('.app-name-display').forEach(el => {
                    el.textContent = set.app_name;
                });
                document.querySelectorAll('.app-hub-name-display').forEach(el => {
                    el.textContent = set.app_name + ' Hub';
                });
            }
        })
        .catch(() => {});

    const user = getSessionUser();
    if (user) {
        // Automatic Abandoned Wishlist/Cart Check
        fetch(`/api/wishlist/check-abandoned/${user.id}`)
            .then(res => res.json())
            .then(data => {
                if (data && data.has_abandoned && data.product) {
                    // Show a gorgeous non-obstructive toast or notification card
                    setTimeout(() => {
                        if (typeof Toast !== 'undefined') {
                            Toast.info(`🛒 Wishlist Alert: "${data.product.title}" is waiting for you! Use discount code SAVE50 to get 50% off and checkout for ₹${data.product.price}! Click profile to buy.`, 8000);
                        }
                    }, 4000);
                }
            })
            .catch(() => {});

        fetch(`/api/notifications/${user.id}`)
            .then(res => res.json())
            .then(data => {
                const unread = data.filter(n => n.is_read === 0).length;
                const badge = document.getElementById('unread-count');
                if (badge) {
                    if (unread > 0) {
                        badge.textContent = unread;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }
                
                // Show dynamic active push Toast Alert for newly broadcasted messages
                if (data.length > 0) {
                    const lastSeenId = parseInt(localStorage.getItem('sellora_last_seen_notification') || '0');
                    const maxId = Math.max(...data.map(n => n.id));
                    if (lastSeenId === 0) {
                        localStorage.setItem('sellora_last_seen_notification', maxId.toString());
                    } else if (maxId > lastSeenId) {
                        const newNotifications = data.filter(n => n.id > lastSeenId);
                        newNotifications.forEach((n, idx) => {
                            setTimeout(() => {
                                if (typeof Toast !== 'undefined') {
                                    Toast.show(n.title + ": " + n.message, n.title.includes('📢') || n.title.includes('Campaign') ? 'success' : 'info', 6000);
                                }
                            }, 500 + idx * 2000);
                        });
                        localStorage.setItem('sellora_last_seen_notification', maxId.toString());
                    }
                }
            })
            .catch(() => {});
    } else {
        fetch('/api/notifications/0')
            .then(res => res.json())
            .then(data => {
                const unread = data.filter(n => n.is_read === 0).length;
                const badge = document.getElementById('unread-count');
                if (badge) {
                    if (unread > 0) {
                        badge.textContent = unread;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }
                
                // Guests active broadcast toast alerts
                if (data.length > 0) {
                    const lastSeenId = parseInt(localStorage.getItem('sellora_last_seen_notification') || '0');
                    const maxId = Math.max(...data.map(n => n.id));
                    if (lastSeenId === 0) {
                        localStorage.setItem('sellora_last_seen_notification', maxId.toString());
                    } else if (maxId > lastSeenId) {
                        const newNotifications = data.filter(n => n.id > lastSeenId);
                        newNotifications.forEach((n, idx) => {
                            setTimeout(() => {
                                if (typeof Toast !== 'undefined') {
                                    Toast.show(n.title + ": " + n.message, n.title.includes('📢') || n.title.includes('Campaign') ? 'success' : 'info', 6000);
                                }
                            }, 500 + idx * 2000);
                        });
                        localStorage.setItem('sellora_last_seen_notification', maxId.toString());
                    }
                }
            })
            .catch(() => {
                const badge = document.getElementById('unread-count');
                if (badge) badge.classList.add('hidden');
            });
    }

    // Ultimate Instant Speed Splash Fade-Out
    setTimeout(() => {
        const splash = document.getElementById('app-splash-screen');
        if (splash) {
            splash.style.opacity = '0';
            splash.style.visibility = 'hidden';
            setTimeout(() => {
                splash.remove();
            }, 160);
        }
    }, 5);
});
</script>

<!-- Dynamic and Modern Bottom Bar Nav for PWAs and Android WebViews -->
<div class="fixed bottom-0 left-0 right-0 z-40 bg-white/80 dark:bg-slate-900/85 backdrop-blur-lg border-t border-slate-200/50 dark:border-white/5 pb-safe rounded-t-3xl shadow-[0_-8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_-8px_30px_rgb(0,0,0,0.2)] transition-all duration-300">
    <div class="max-w-md mx-auto px-6 h-16 flex items-center justify-between relative">
        
        <!-- Home Navigation item -->
        <a href="index.php" id="nav-home" class="flex flex-col items-center justify-center flex-1 h-full text-slate-400 dark:text-slate-500 hover:text-sky-500 dark:hover:text-sky-400 font-medium transition-all duration-200 outline-none relative" onclick="triggerVibe(20)">
            <i class="fas fa-house text-lg mb-1"></i>
            <span class="text-[10px] tracking-tight">Home</span>
            <!-- Simple Active Circle Indicator Slot -->
            <span class="nav-dot hidden absolute bottom-1 w-1 h-1 rounded-full bg-sky-500"></span>
        </a>
        
        <!-- Products Navigation item -->
        <a href="products.php" id="nav-products" class="flex flex-col items-center justify-center flex-1 h-full text-slate-400 dark:text-slate-500 hover:text-sky-500 dark:hover:text-sky-400 font-medium transition-all duration-200 outline-none relative" onclick="triggerVibe(20)">
            <i class="fas fa-cubes text-lg mb-1"></i>
            <span class="text-[10px] tracking-tight">Products</span>
            <span class="nav-dot hidden absolute bottom-1 w-1 h-1 rounded-full bg-sky-500"></span>
        </a>
        
        <!-- Downloads Navigation item -->
        <a href="mydownloads.php" id="nav-downloads" class="flex flex-col items-center justify-center flex-1 h-full text-slate-400 dark:text-slate-500 hover:text-sky-500 dark:hover:text-sky-400 font-medium transition-all duration-200 outline-none relative" onclick="triggerVibe(20)">
            <i class="fas fa-circle-down text-lg mb-1"></i>
            <span class="text-[10px] tracking-tight">Downloads</span>
            <span class="nav-dot hidden absolute bottom-1 w-1 h-1 rounded-full bg-sky-500"></span>
        </a>
        
        <!-- Wishlist Navigation item -->
        <a href="wishlist.php" id="nav-wishlist" class="flex flex-col items-center justify-center flex-1 h-full text-slate-400 dark:text-slate-500 hover:text-sky-500 dark:hover:text-sky-400 font-medium transition-all duration-200 outline-none relative" onclick="triggerVibe(20)">
            <i class="fas fa-heart text-lg mb-1"></i>
            <span class="text-[10px] tracking-tight">Wishlist</span>
            <span class="nav-dot hidden absolute bottom-1 w-1 h-1 rounded-full bg-sky-500"></span>
        </a>
        
        <!-- Profile Navigation item -->
        <a href="profile.php" id="nav-profile" class="flex flex-col items-center justify-center flex-1 h-full text-slate-400 dark:text-slate-500 hover:text-sky-500 dark:hover:text-sky-400 font-medium transition-all duration-200 outline-none relative" onclick="triggerVibe(20)">
            <i class="fas fa-circle-user text-lg mb-1"></i>
            <span class="text-[10px] tracking-tight font-medium">Profile</span>
            <span class="nav-dot hidden absolute bottom-1 w-1 h-1 rounded-full bg-sky-500"></span>
        </a>
        
    </div>
</div>

<script>
// Automatically Highlight Active Tab Based on current file URL
document.addEventListener('DOMContentLoaded', () => {
    const p = window.location.pathname.split('/').pop() || 'index.php';
    let activeId = 'nav-home';
    
    if (p.includes('products.php')) {
        activeId = 'nav-products';
    } else if (p.includes('mydownloads.php') || p.includes('download.php')) {
        activeId = 'nav-downloads';
    } else if (p.includes('wishlist.php')) {
        activeId = 'nav-wishlist';
    } else if (p.includes('profile.php') || p.includes('help.php')) {
        activeId = 'nav-profile';
    } else if (p.includes('index.php')) {
        activeId = 'nav-home';
    }
    
    const activeEl = document.getElementById(activeId);
    if (activeEl) {
        activeEl.classList.remove('text-slate-400', 'dark:text-slate-500');
        activeEl.classList.add('text-sky-500', 'dark:text-sky-400', 'scale-105');
        // Show indicator dot
        const dot = activeEl.querySelector('.nav-dot');
        if (dot) dot.classList.remove('hidden');
    }
    
    // Manage auto android physical exit confirmation
    let exitPressTime = 0;
    window.addEventListener('popstate', (e) => {
        const path = window.location.pathname.split('/').pop() || 'index.php';
        if (path === 'index.php' || path === 'login.php') {
            const now = Date.now();
            if (now - exitPressTime < 2000) {
                // Exit app mockup
                alert("Closing DigitalMohan mobile wrapper.");
            } else {
                exitPressTime = now;
                Toast.info("Press back button again to exit DigitalMohan application.");
                // Prevent real immediate backward navigation on mobile shell (Mock)
                history.pushState(null, null, window.location.href);
            }
        }
    });
    
    // Enable state trigger to lock navigation history for exit check
    history.pushState(null, null, window.location.href);

    // Dynamic Viral Ebook Popup Initialization
    initViralEbookPopup();
    
    // Dynamic WhatsApp Group Join Popup Initialization
    initWhatsAppGroupPopup();
});

// Viral WhatsApp Ebook Verification Engine
function initViralEbookPopup() {
    fetch('/api/settings')
        .then(res => res.json())
        .then(set => {
            window.viralPopupSettings = set;
            const enabled = (set.viral_popup_enabled === undefined || set.viral_popup_enabled === 1 || set.viral_popup_enabled === "1" || set.viral_popup_enabled === true);
            if (!enabled) {
                const btn = document.getElementById('floating-viral-gift-btn');
                if (btn) btn.classList.add('hidden');
                return;
            } else {
                const btn = document.getElementById('floating-viral-gift-btn');
                if (btn) btn.classList.remove('hidden');
            }

            const title = set.viral_popup_title || "Growth Marketing Secrets";
            const desc = set.viral_popup_description || "Free Premium Ebook unlock karne ke liye ye link WhatsApp par share karein.";

            const textTitle = document.getElementById('viral-ebook-title');
            if (textTitle) textTitle.innerText = title;

            const textRules = document.getElementById('viral-ebook-rules');
            if (textRules) {
                textRules.innerHTML = `🎁 Free Premium Ebook unlock karne ke liye ye link WhatsApp par share karein. <span class="font-extrabold text-sky-500">${desc}</span>. Self-sharing aur same phone numbers strictly disallowed hain. Server validation check karega.`;
            }

            const successResource = document.getElementById('viral-ebook-success-resource');
            if (successResource) {
                successResource.innerHTML = `<p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-wider font-extrabold font-black">Unlocked Resource:</p>
                    <p class="font-bold text-slate-705 dark:text-slate-200 flex items-center gap-1.5"><i class="fas fa-file-pdf text-red-500 text-xs"></i> ${title}</p>
                    <p class="text-[9px] text-slate-400 dark:text-slate-500 leading-normal">Premium license key has been bound to your device key storage successfully.</p>`;
            }

            // Only auto-show popup once per session after 2.5 seconds if not already unlocked
            setTimeout(() => {
                const isUnlocked = localStorage.getItem('viral_ebook_unlocked') === 'true';
                const hasSessionSeen = sessionStorage.getItem('viral_ebook_popup_shown') === 'true';
                if (!isUnlocked && !hasSessionSeen) {
                    openViralEbookModal();
                    sessionStorage.setItem('viral_ebook_popup_shown', 'true');
                }
            }, 2500);
        })
        .catch(err => {
            console.error("Error loading viral settings:", err);
            // Fallback manual schedule
            setTimeout(() => {
                const isUnlocked = localStorage.getItem('viral_ebook_unlocked') === 'true';
                const hasSessionSeen = sessionStorage.getItem('viral_ebook_popup_shown') === 'true';
                if (!isUnlocked && !hasSessionSeen) {
                    openViralEbookModal();
                    sessionStorage.setItem('viral_ebook_popup_shown', 'true');
                }
            }, 2500);
        });
}

function openViralEbookModal() {
    const modal = document.getElementById('viral-ebook-modal-backdrop');
    if (!modal) return;
    
    // Toggle active classes
    modal.classList.remove('opacity-0', 'pointer-events-none');
    modal.classList.add('opacity-100');
    
    const card = document.getElementById('viral-ebook-card');
    if (card) {
        card.classList.remove('scale-90');
        card.classList.add('scale-100');
    }
    
    // Refresh modal states based on current unlock status
    const isUnlocked = localStorage.getItem('viral_ebook_unlocked') === 'true';
    updateViralEbookUI(isUnlocked);
}

function closeViralEbookModal() {
    const modal = document.getElementById('viral-ebook-modal-backdrop');
    if (!modal) return;
    
    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0', 'pointer-events-none');
    
    const card = document.getElementById('viral-ebook-card');
    if (card) {
        card.classList.remove('scale-100');
        card.classList.add('scale-90');
    }
}

function updateViralEbookUI(unlocked) {
    const lockBadge = document.getElementById('ebook-lock-badge');
    const formSection = document.getElementById('ebook-form-section');
    const verifySection = document.getElementById('ebook-verify-section');
    const successSection = document.getElementById('ebook-success-section');
    
    if (unlocked) {
        if (lockBadge) {
            lockBadge.innerHTML = '<span class="px-2.5 py-1 rounded-full text-[10px] font-black tracking-widest bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 uppercase flex items-center gap-1.5"><i class="fas fa-lock-open text-xs"></i> Unlocked</span>';
        }
        if (formSection) formSection.classList.add('hidden');
        if (verifySection) verifySection.classList.add('hidden');
        if (successSection) successSection.classList.remove('hidden');
    } else {
        if (lockBadge) {
            lockBadge.innerHTML = '<span class="px-2.5 py-1 rounded-full text-[10px] font-black tracking-widest bg-red-500/10 text-red-500 border border-red-500/20 uppercase flex items-center gap-1.5 animate-pulse"><i class="fas fa-lock text-xs"></i> Locked</span>';
        }
        if (formSection) formSection.classList.remove('hidden');
        if (verifySection) verifySection.classList.add('hidden');
        if (successSection) successSection.classList.add('hidden');
    }
}

let verificationTimer = null;
function startWhatsAppEbookShareProcess() {
    const senderInput = document.getElementById('viral-sender-no');
    const recipientInput = document.getElementById('viral-recipient-no');
    const errorEl = document.getElementById('viral-error-msg');
    
    if (!senderInput || !recipientInput || !errorEl) return;
    
    const sender = senderInput.value.trim();
    const recipient = recipientInput.value.trim();
    
    // Validation
    const phoneRegex = /^[6789]\d{9}$/;
    
    errorEl.classList.add('hidden');
    errorEl.innerText = "";
    
    if (!phoneRegex.test(sender)) {
        errorEl.innerText = "❌ Apna valid 10-digit WhatsApp number dalein!";
        errorEl.classList.remove('hidden');
        if (navigator.vibrate) navigator.vibrate([50, 50]);
        return;
    }
    
    if (!phoneRegex.test(recipient)) {
        errorEl.innerText = "❌ Recipient ya dost ka valid 10-digit number dalein!";
        errorEl.classList.remove('hidden');
        if (navigator.vibrate) navigator.vibrate([50, 50]);
        return;
    }
    
    // Rule: khud ke number per share na kar paye
    if (sender === recipient) {
        errorEl.innerText = "⚠️ Aap khud ke hi number par share nahi kar sakte! Kripya doosre dost ya group ka number dalein.";
        errorEl.classList.remove('hidden');
        if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
        return;
    }
    
    // Fire Vibration
    if (navigator.vibrate) navigator.vibrate(30);
    
    // Open WhatsApp Share immediately
    const settings = window.viralPopupSettings || {};
    const title = settings.viral_popup_title || "Growth Marketing Secrets";
    const encodedMessage = encodeURIComponent(`🎁 Hey! I retrieved the Premium ChatGPT Prompt folder & Canva Layout sheets of DigitalMohan! Claim your Free Premium Bonus Marketing Ebook "${title}" now before it expires here: https://${window.location.host}/index.php`);
    const waUrl = `https://api.whatsapp.com/send?phone=91${recipient}&text=${encodedMessage}`;
    window.open(waUrl, '_blank');
    
    // Swap view to verification progress console
    const formSection = document.getElementById('ebook-form-section');
    const verifySection = document.getElementById('ebook-verify-section');
    
    formSection.classList.add('hidden');
    verifySection.classList.remove('hidden');
    
    // Live realistic security gateway verification ticker
    const steps = [
        { progress: 10, text: "Connecting to secure local WhatsApp network... 📡" },
        { progress: 28, text: "Initializing unique device token handshake... 🔐" },
        { progress: 45, text: "Strict verification: checking unique phone numbers... 🕵️" },
        { progress: 62, text: "Checking message packet delivery status... ⏳" },
        { progress: 85, text: "Analyzing WhatsApp API response headers... 🛡️" },
        { progress: 100, text: "Verified! Message delivered to unique recipient cell. 🔥" }
    ];
    
    const progressBar = document.getElementById('verify-progress-bar');
    const progressPerc = document.getElementById('verify-progress-percentage');
    const logConsole = document.getElementById('verify-log-console');
    
    let currentStepIdx = 0;
    progressBar.style.width = '0%';
    progressPerc.innerText = '0%';
    logConsole.innerText = "Launching verification system...";
    
    if (verificationTimer) clearInterval(verificationTimer);
    
    const duration = 9000; // 9 seconds verification for ultra high fidelity realistic feel
    const intervalTime = 1500;
    
    verificationTimer = setInterval(() => {
        if (currentStepIdx < steps.length) {
            const step = steps[currentStepIdx];
            progressBar.style.width = `${step.progress}%`;
            progressPerc.innerText = `${step.progress}%`;
            logConsole.innerText = step.text;
            
            // Subtle sound click or pulse vibration
            if (navigator.vibrate) navigator.vibrate(15);
            
            currentStepIdx++;
        } else {
            clearInterval(verificationTimer);
            // Complete Unlock!
            localStorage.setItem('viral_ebook_unlocked', 'true');
            localStorage.setItem('viral_ebook_verified_recipient', recipient);
            updateViralEbookUI(true);
            
            // Explode confetti effect
            triggerConfettiExplosion();
        }
    }, intervalTime);
}

function resetViralEbookData() {
    localStorage.removeItem('viral_ebook_unlocked');
    localStorage.removeItem('viral_ebook_verified_recipient');
    
    const senderInput = document.getElementById('viral-sender-no');
    const recipientInput = document.getElementById('viral-recipient-no');
    if (senderInput) senderInput.value = "";
    if (recipientInput) recipientInput.value = "";
    
    updateViralEbookUI(false);
}

// Sparkly Micro-Confetti System
function triggerConfettiExplosion() {
    if (navigator.vibrate) navigator.vibrate([100, 50, 100, 50, 300]);
    for(let i=0; i<35; i++){
        const p = document.createElement('div');
        p.className = 'click-particle';
        p.style.backgroundColor = `hsl(${Math.random() * 360}, 100%, 50%)`;
        p.style.setProperty('--dx', `${(Math.random() - 0.5) * 350}px`);
        p.style.setProperty('--dy', `${(Math.random() - 0.5) * 350}px`);
        p.style.left = '50%';
        p.style.top = '50%';
        document.body.appendChild(p);
        setTimeout(() => p.remove(), 600);
    }
}
</script>

<!-- Floating Premium Gift Box Action Trigger Button -->
<button 
    id="floating-viral-gift-btn" 
    class="fixed bottom-20 right-4 z-40 bg-gradient-to-r from-emerald-500 to-sky-600 hover:from-emerald-600 hover:to-sky-700 shadow-[0_8px_30px_rgb(16,185,129,0.3)] hover:shadow-[0_8px_35px_rgb(2,132,199,0.4)] scale-100 hover:scale-[1.08] active:scale-95 text-white flex items-center gap-2.5 px-4 h-11 text-xs font-black uppercase tracking-wider rounded-full border border-white/20 transition-all duration-300 cursor-pointer animate-bounce group"
    onclick="openViralEbookModal(); triggerVibe(30);"
>
    <span class="relative flex h-3.5 w-3.5">
        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
        <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-amber-500 items-center justify-center text-[9px] text-white font-bold">🎁</span>
    </span>
    <span>Free Ebook</span>
</button>

<!-- Premium Interactive Glass Viral Ebook Unlocking Modal -->
<div 
    id="viral-ebook-modal-backdrop" 
    class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-[1000] flex items-center justify-center p-4 transition-all duration-300 opacity-0 pointer-events-none"
    style="user-select: none; -webkit-user-select: none;"
>
    <!-- Modal Central Container Card -->
    <div 
        id="viral-ebook-card" 
        class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-sm overflow-hidden border border-slate-150 dark:border-slate-800 shadow-2xl relative transform scale-90 transition-all duration-300 flex flex-col max-h-[90vh]"
    >
        <!-- Card Backdrop Gradient Sparkles Header -->
        <div class="p-5 bg-gradient-to-r from-sky-600 via-indigo-600 to-sky-700 text-white relative">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-white/10 via-transparent to-transparent"></div>
            
            <div class="flex items-center justify-between relative z-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center text-xl shadow-inner animate-pulse">
                        🎁
                    </div>
                    <div>
                        <h3 class="font-bold text-sm tracking-tight">Viral Bonus Unlocking</h3>
                        <p class="text-[10px] text-sky-200 uppercase tracking-widest font-black">Free Premium Ebook</p>
                    </div>
                </div>
                
                <!-- Close Button -->
                <button 
                    onclick="closeViralEbookModal(); triggerVibe(15);" 
                    class="w-8 h-8 rounded-full bg-black/10 hover:bg-black/25 flex items-center justify-center text-white text-sm transition-all focus:outline-none"
                >
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Scrollable Modal Content Panel -->
        <div class="p-5 space-y-4 overflow-y-auto no-scrollbar max-h-[60vh] text-left">
            <!-- 1. Central Locked Status Banner -->
            <div class="flex items-center justify-between bg-slate-50 dark:bg-slate-850 border border-slate-100 dark:border-slate-800/60 p-3.5 rounded-2xl">
                <div class="flex items-center gap-2">
                    <span class="text-xl">📕</span>
                    <div>
                        <h4 id="viral-ebook-title" class="text-xs font-bold text-slate-800 dark:text-slate-200">Growth Marketing Secrets</h4>
                        <p class="text-[9px] text-slate-400 dark:text-slate-500 font-medium">Size: 4.8 MB • PDF Version</p>
                    </div>
                </div>
                <!-- Status Badge -->
                <div id="ebook-lock-badge">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black tracking-widest bg-red-500/10 text-red-500 border border-red-500/20 uppercase flex items-center gap-1.5 animate-pulse">
                        <i class="fas fa-lock text-xs"></i> Locked
                    </span>
                </div>
            </div>

            <div class="space-y-2">
                <h5 class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Instructions / नियम:</h5>
                <p id="viral-ebook-rules" class="text-[11px] leading-relaxed text-slate-500 dark:text-slate-400">
                    🎁 Free Premium Ebook unlock karne ke liye ye link WhatsApp par share karein. <span class="font-extrabold text-sky-500">Self-sharing aur same phone numbers strictly disallowed hain</span>. Server validation check karega.
                </p>
            </div>

            <!-- 2a. Input Sharing Form Fields -->
            <div id="ebook-form-section" class="space-y-4">
                <!-- User's Sender Number input -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black uppercase text-slate-450 dark:text-slate-500 tracking-wider flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                        Apna WhatsApp Number (Your Number)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold font-mono text-slate-400 dark:text-slate-550">+91</span>
                        <input 
                            type="tel" 
                            id="viral-sender-no" 
                            placeholder="Apna 10-digit number dalein..." 
                            maxlength="10"
                            class="w-full h-11 pl-12 pr-4 text-xs font-semibold font-mono border border-slate-250 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 placeholder-slate-400 rounded-xl focus:border-green-500 focus:ring-1 focus:ring-green-500/20 focus:outline-none transition-all"
                        />
                    </div>
                </div>

                <!-- Receiver/Friend Number input -->
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black uppercase text-slate-450 dark:text-slate-500 tracking-wider flex items-center gap-11 justify-between">
                        <span class="flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Dost ya Group Number (Recipient Number)
                        </span>
                        <span class="text-[9px] text-red-500 font-bold lowercase normal-case tracking-normal">No self-share</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold font-mono text-slate-400 dark:text-slate-550">+91</span>
                        <input 
                            type="tel" 
                            id="viral-recipient-no" 
                            placeholder="Dost ya group ka Number dalein..." 
                            maxlength="10"
                            class="w-full h-11 pl-12 pr-4 text-xs font-semibold font-mono border border-slate-250 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 placeholder-slate-400 rounded-xl focus:border-green-500 focus:ring-1 focus:ring-green-500/20 focus:outline-none transition-all"
                        />
                    </div>
                </div>

                <!-- Error feedback placeholder -->
                <div id="viral-error-msg" class="text-red-500 dark:text-red-400 text-[11px] font-bold text-center bg-red-500/5 border border-red-500/10 p-2.5 rounded-xl hidden"></div>

                <!-- Dynamic Share to verify button -->
                <button 
                    onclick="startWhatsAppEbookShareProcess(); triggerVibe(40);" 
                    class="w-full h-12 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-black uppercase text-[11px] tracking-wider rounded-2xl flex items-center justify-center gap-2 shadow-lg shadow-emerald-500/20 active:scale-[0.98] transition-all cursor-pointer"
                >
                    <i class="fab fa-whatsapp text-lg"></i>
                    Share & Verify On WhatsApp
                </button>
            </div>

            <!-- 2b. Live Handshake Verification Console -->
            <div id="ebook-verify-section" class="space-y-4 hidden">
                <div class="flex flex-col items-center justify-center py-6 text-center space-y-3">
                    <div class="relative flex items-center justify-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-2 border-slate-200 border-t-emerald-500"></div>
                        <span class="absolute text-xs">📡</span>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-100">Analyzing Network Route Packet...</h4>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">Checking WhatsApp delivery metadata logs</p>
                    </div>
                </div>

                <!-- Progress indicators -->
                <div class="space-y-1">
                    <div class="flex justify-between text-[10px] font-mono font-bold text-slate-450 dark:text-slate-500">
                        <span>HANDSHAKE PROGRESS</span>
                        <span id="verify-progress-percentage">45%</span>
                    </div>
                    <div class="w-full h-2 bg-slate-150 dark:bg-slate-950 rounded-full overflow-hidden">
                        <div id="verify-progress-bar" class="h-full bg-gradient-to-r from-emerald-500 to-sky-500 rounded-full transition-all duration-300" style="width: 45%;"></div>
                    </div>
                </div>

                <!-- Terminal logs console -->
                <div id="verify-log-console" class="bg-black text-emerald-400 font-mono text-[9px] p-4 rounded-xl shadow-inner min-h-[44px] flex items-center border border-slate-800/80 leading-relaxed text-left">
                    Comparing sender phone uniqueness...
                </div>
            </div>

            <!-- 2c. Ebook Success Download Center -->
            <div id="ebook-success-section" class="space-y-4 text-center hidden">
                <div class="flex flex-col items-center justify-center py-4 space-y-2">
                    <div class="w-14 h-14 bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 flex items-center justify-center text-3xl rounded-full shadow-inner animate-pulse">
                        🎉
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">Unlocking Verified!</h4>
                        <p class="text-[10px] text-emerald-500 font-bold">Thank you for sharing with unique recipient!</p>
                    </div>
                </div>

                <div id="viral-ebook-success-resource" class="bg-slate-50 dark:bg-slate-950/60 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-850/65 text-left text-[11px] space-y-1">
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-wider font-extrabold font-black">Unlocked Resource:</p>
                    <p class="font-bold text-slate-705 dark:text-slate-200 flex items-center gap-1.5"><i class="fas fa-file-pdf text-red-500 text-xs"></i> Growth Marketing Secrets</p>
                    <p class="text-[9px] text-slate-400 dark:text-slate-500 leading-normal">Premium license key has been bound to your device key storage successfully.</p>
                </div>

                <!-- Direct download file links -->
                <a 
                    href="api.php?route=/api/downloads/file&id=999" 
                    target="_blank"
                    class="w-full h-12 bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-600 hover:to-indigo-700 text-white font-black uppercase text-[11px] tracking-widest rounded-2xl flex items-center justify-center gap-2 shadow-xl shadow-sky-500/20 active:scale-[0.98] transition-all cursor-pointer"
                    onclick="triggerVibe(30);"
                >
                    <i class="fas fa-file-pdf text-lg"></i>
                    Download Free Ebook Now
                </a>

                <!-- Reset button to show off testing -->
                <button 
                    onclick="resetViralEbookData(); triggerVibe(30);"
                    class="text-[9px] uppercase tracking-wider font-black text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"
                >
                    <i class="fas fa-sync text-[8px] mr-1"></i> Reset Share Status (Test Mode)
                </button>
            </div>
        </div>
        
        <!-- Safety Footer -->
        <div class="bg-slate-50 dark:bg-slate-950/40 p-3 border-t border-slate-100 dark:border-slate-850 text-center text-[9px] text-slate-400 dark:text-slate-500 font-medium">
            🔒 Fully encrypted sharing validation matrix. DigitalMohan Verification protocols.
        </div>
    </div>
</div>

<!-- Floating WhatsApp Group Trigger Button -->
<button 
    id="floating-wa-group-btn" 
    class="fixed bottom-20 left-4 z-40 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 shadow-[0_8px_30px_rgb(34,197,94,0.3)] hover:shadow-[0_8px_35px_rgb(16,185,129,0.4)] scale-100 hover:scale-[1.08] active:scale-95 text-white flex items-center justify-center w-11 h-11 rounded-full border border-white/20 transition-all duration-300 cursor-pointer group"
    onclick="openWhatsAppGroupModal(); triggerVibe(30);"
    title="Join WhatsApp Group"
>
    <i class="fab fa-whatsapp text-xl"></i>
    <span class="absolute -top-1 -right-1 flex h-3 w-3">
        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
    </span>
</button>

<!-- Premium Interactive Glass WhatsApp Group Join Modal -->
<div 
    id="whatsapp-group-modal-backdrop" 
    class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-[1000] flex items-center justify-center p-4 transition-all duration-300 opacity-0 pointer-events-none"
    style="user-select: none; -webkit-view-select: none;"
>
    <!-- Modal Central Container Card -->
    <div 
        id="whatsapp-group-card" 
        class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-sm overflow-hidden border border-slate-150 dark:border-slate-800 shadow-2xl relative transform scale-90 transition-all duration-300 flex flex-col max-h-[90vh]"
    >
        <!-- Card Backdrop Gradient Sparkles Header -->
        <div class="p-5 bg-gradient-to-r from-emerald-500 via-teal-600 to-emerald-600 text-white relative">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-white/10 via-transparent to-transparent"></div>
            
            <div class="flex items-center justify-between relative z-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center text-xl shadow-inner animate-pulse">
                        <i class="fab fa-whatsapp text-xl"></i>
                    </div>
                    <div>
                        <h3 id="wa-modal-title" class="font-bold text-sm tracking-tight">VIP Community Invite</h3>
                        <p class="text-[10px] text-emerald-100 uppercase tracking-widest font-black">Official Group Link</p>
                    </div>
                </div>
                
                <!-- Close Button -->
                <button 
                    onclick="closeWhatsAppGroupModal(); triggerVibe(15);" 
                    class="w-8 h-8 rounded-full bg-black/10 hover:bg-black/25 flex items-center justify-center text-white text-sm transition-all focus:outline-none"
                    aria-label="Close"
                >
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Elegant shrinking auto-close progress indicator -->
        <div id="wa-progress-container" class="w-full h-[3px] bg-slate-150 dark:bg-slate-800 relative hidden">
            <div id="wa-progress-bar" class="h-full bg-emerald-500 transition-all ease-linear" style="width: 100%;"></div>
        </div>

        <!-- Modal Content Panel -->
        <div class="p-5 space-y-4 overflow-y-auto no-scrollbar text-left text-slate-700 dark:text-slate-300">
            <div class="bg-emerald-500/5 border border-emerald-500/10 p-3.5 rounded-2xl">
                <p id="wa-modal-description" class="text-xs font-semibold leading-relaxed text-slate-700 dark:text-slate-350">
                    Get instant high-quality templates, free resume tools, and direct support updates daily. Join 10,000+ members!
                </p>
            </div>

            <!-- Perks Checklist list structure -->
            <div class="space-y-2.5">
                <h4 class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-550 tracking-wider">EXCLUSIVE MEMBER PERKS:</h4>
                <ul class="space-y-1.5 text-[11px] font-medium text-slate-650 dark:text-slate-400">
                    <li class="flex items-start gap-2.5">
                        <span class="text-emerald-500 mt-0.5"><i class="fas fa-circle-check text-xs"></i></span>
                        <div>
                            <strong class="text-slate-800 dark:text-slate-200">Direct Admin Support:</strong> Ask questions anytime
                        </div>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="text-emerald-500 mt-0.5"><i class="fas fa-circle-check text-xs"></i></span>
                        <div>
                            <strong class="text-slate-800 dark:text-slate-200">Free Weekly Resources:</strong> Prompt packs, Canva formats, PDFs
                        </div>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="text-emerald-500 mt-0.5"><i class="fas fa-circle-check text-xs"></i></span>
                        <div>
                            <strong class="text-slate-800 dark:text-slate-200">No Spam Policy:</strong> Strictly high-value digital updates
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Action Button -->
            <button 
                onclick="redirectToWhatsAppGroup(); triggerVibe(40);" 
                class="w-full h-12 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-black uppercase text-[11px] tracking-widest rounded-2xl flex items-center justify-center gap-2 shadow-lg shadow-emerald-500/20 active:scale-[0.98] transition-all cursor-pointer"
            >
                <i class="fab fa-whatsapp text-lg"></i>
                Join Group Now
            </button>
        </div>
        
        <!-- Safety Footer -->
        <div class="bg-slate-50 dark:bg-slate-950/40 p-3 border-t border-slate-100 dark:border-slate-850 text-center text-[9px] text-slate-400 dark:text-slate-500 font-medium animate-pulse">
            🚪 You can leave or mute the community group at any time. Privacy guaranteed.
        </div>
    </div>
</div>

<script>
let whatsappGroupLink = "https://chat.whatsapp.com/GjMockGrpLnk2026Sellora";
let whatsappGroupAutoClose = 10000;
let whatsappAutoCloseTimeout = null;

function initWhatsAppGroupPopup() {
    fetch('/api/settings')
        .then(res => res.json())
        .then(set => {
            const enabled = (set.whatsapp_group_enabled === undefined || set.whatsapp_group_enabled === 1 || set.whatsapp_group_enabled === "1" || set.whatsapp_group_enabled === true);
            if (!enabled) {
                const btn = document.getElementById('floating-wa-group-btn');
                if (btn) btn.classList.add('hidden');
                return;
            } else {
                const btn = document.getElementById('floating-wa-group-btn');
                if (btn) btn.classList.remove('hidden');
            }

            whatsappGroupLink = set.whatsapp_group_link || "https://chat.whatsapp.com/GjMockGrpLnk2026Sellora";
            whatsappGroupAutoClose = set.whatsapp_group_autoclose !== undefined ? parseInt(set.whatsapp_group_autoclose) : 10000;
            const title = set.whatsapp_group_title || "Join Our Premium WhatsApp Community! 🚀";
            const desc = set.whatsapp_group_description || "Get instant high-quality templates, free resume tools, and direct support updates daily. Join 10,000+ members!";
            const delay = parseInt(set.whatsapp_group_delay) || 5000;

            const tHeading = document.getElementById('wa-modal-title');
            if (tHeading) tHeading.innerText = title;

            const tDesc = document.getElementById('wa-modal-description');
            if (tDesc) tDesc.innerText = desc;

            // Trigger auto show popup with specified delay
            setTimeout(() => {
                const isJoinedAlready = localStorage.getItem('whatsapp_group_joined') === 'true';
                const seenThisSession = sessionStorage.getItem('whatsapp_group_shown') === 'true';
                if (!isJoinedAlready && !seenThisSession) {
                    openWhatsAppGroupModal();
                    sessionStorage.setItem('whatsapp_group_shown', 'true');
                }
            }, delay);
        })
        .catch(err => {
            console.error("Error loading WhatsApp group settings:", err);
            // Fallback default trigger
            setTimeout(() => {
                const isJoinedAlready = localStorage.getItem('whatsapp_group_joined') === 'true';
                const seenThisSession = sessionStorage.getItem('whatsapp_group_shown') === 'true';
                if (!isJoinedAlready && !seenThisSession) {
                    openWhatsAppGroupModal();
                    sessionStorage.setItem('whatsapp_group_shown', 'true');
                }
            }, 5000);
        });
}

function openWhatsAppGroupModal() {
    const modal = document.getElementById('whatsapp-group-modal-backdrop');
    if (!modal) return;
    
    // Clear any active auto close timeouts
    if (whatsappAutoCloseTimeout) {
        clearTimeout(whatsappAutoCloseTimeout);
        whatsappAutoCloseTimeout = null;
    }

    modal.classList.remove('opacity-0', 'pointer-events-none');
    modal.classList.add('opacity-100');
    
    const card = document.getElementById('whatsapp-group-card');
    if (card) {
        card.classList.remove('scale-90');
        card.classList.add('scale-100');
    }

    // Configure Auto-Close progress bar & triggers
    const container = document.getElementById('wa-progress-container');
    const bar = document.getElementById('wa-progress-bar');
    if (container && bar) {
        if (whatsappGroupAutoClose > 0) {
            container.classList.remove('hidden');
            bar.style.transition = 'none';
            bar.style.width = '100%';
            
            // Allow layout painting, then start shrink transition
            setTimeout(() => {
                bar.style.transition = `width ${whatsappGroupAutoClose}ms linear`;
                bar.style.width = '0%';
            }, 50);

            whatsappAutoCloseTimeout = setTimeout(() => {
                closeWhatsAppGroupModal();
            }, whatsappGroupAutoClose);
        } else {
            container.classList.add('hidden');
        }
    }
}

function closeWhatsAppGroupModal() {
    // Clear timeout safe
    if (whatsappAutoCloseTimeout) {
        clearTimeout(whatsappAutoCloseTimeout);
        whatsappAutoCloseTimeout = null;
    }

    const modal = document.getElementById('whatsapp-group-modal-backdrop');
    if (!modal) return;
    
    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0', 'pointer-events-none');
    
    const card = document.getElementById('whatsapp-group-card');
    if (card) {
        card.classList.remove('scale-100');
        card.classList.add('scale-90');
    }

    // Restore bar width cleanly
    const bar = document.getElementById('wa-progress-bar');
    if (bar) {
        bar.style.transition = 'none';
        bar.style.width = '100%';
    }
}

function redirectToWhatsAppGroup() {
    localStorage.setItem('whatsapp_group_joined', 'true');
    window.open(whatsappGroupLink, '_blank');
    closeWhatsAppGroupModal();
}
</script>
</body>
</html>

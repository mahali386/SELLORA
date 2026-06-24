<?php
// Sellora - Auth Registration and Login Panel
require_once __DIR__ . '/common/config.php';
$csrfToken = generateCSRFToken();

// Direct Redirect if session exists on server
if (isUserLoggedIn()) {
    header('Location: index.php');
    exit;
}
?>
<?php include __DIR__ . '/common/header.php'; ?>

<main class="max-w-md mx-auto px-4 pt-8">
    
    <!-- Visual Brand Greeting Card -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-3xl bg-sky-500/15 text-sky-500 text-3xl mb-4 hover:scale-110 transition-transform">
            <i class="fas fa-boxes-packing"></i>
        </div>
        <h1 class="text-3xl font-display font-extrabold tracking-tight">Unlock Digital Assets</h1>
        <p class="text-slate-400 dark:text-slate-500 text-sm mt-1">Get instant delivery for Premium Templates, Prompt Packs & Notes</p>
    </div>

    <!-- Glassmorphic Auth Tab container Card -->
    <div class="rounded-3xl border border-slate-200/50 dark:border-white/5 bg-white/70 dark:bg-slate-900/60 p-6 shadow-xl backdrop-blur-md">
        
        <!-- Toggle button tabs layout -->
        <div class="flex p-1 rounded-2xl bg-slate-100 dark:bg-slate-800/80 mb-6">
            <button id="toggle-login" class="flex-1 py-2.5 rounded-xl text-sm font-semibold transition-all focus:outline-none bg-white dark:bg-slate-700 text-slate-800 dark:text-white" onclick="switchAuthTab('login')">
                Sign In
            </button>
            <button id="toggle-signup" class="flex-1 py-2.5 rounded-xl text-sm font-semibold transition-all focus:outline-none text-slate-400 dark:text-slate-400" onclick="switchAuthTab('signup')">
                Sign Up
            </button>
        </div>

        <input type="hidden" id="csrf-field" value="<?= $csrfToken ?>">

        <!-- INLINE LOGIN FORM -->
        <form id="form-login" onsubmit="submitLoginForm(event)" class="space-y-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1.5">Email Address</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-3 text-slate-400"><i class="fas fa-envelope text-sm"></i></span>
                    <input type="email" id="login-email" required placeholder="name@domain.com" class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800/80 border-0 focus:ring-2 focus:ring-sky-500 text-sm text-slate-700 dark:text-slate-200 outline-none">
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1.5">Secret Key Password</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-3 text-slate-400"><i class="fas fa-lock text-sm"></i></span>
                    <input type="password" id="login-password" required placeholder="Enter login password" class="w-full pl-10 pr-10 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800/80 border-0 focus:ring-2 focus:ring-sky-500 text-sm text-slate-700 dark:text-slate-200 outline-none">
                    <button type="button" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600 focus:outline-none" onclick="togglePasswordView('login-password')">
                        <i id="eye-login-password" class="fas fa-eye text-sm"></i>
                    </button>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 bg-gradient-to-r from-sky-600 to-indigo-500 hover:brightness-110 text-white rounded-xl font-bold text-sm shadow-lg hover:shadow-indigo-500/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2 outline-none">
                    <span>Access Account Dashboard</span>
                    <i class="fas fa-arrow-right-long text-sm"></i>
                </button>
            </div>
        </form>

        <!-- INLINE SIGNUP FORM -->
        <form id="form-signup" onsubmit="submitSignupForm(event)" class="hidden space-y-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1.5">Full Name</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-3 text-slate-400"><i class="fas fa-user text-sm"></i></span>
                    <input type="text" id="signup-name" required placeholder="Elon Musk" class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800/80 border-0 focus:ring-2 focus:ring-sky-500 text-sm text-slate-700 dark:text-slate-200 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1.5">Phone Number</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-3 text-slate-400"><i class="fas fa-phone text-sm"></i></span>
                    <input type="tel" id="signup-phone" required placeholder="9876543210" pattern="[0-9]{10}" title="Ten digits mobile number" class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800/80 border-0 focus:ring-2 focus:ring-sky-500 text-sm text-slate-700 dark:text-slate-200 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1.5">Email Address</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-3 text-slate-400"><i class="fas fa-envelope text-sm"></i></span>
                    <input type="email" id="signup-email" required placeholder="yourname@gmail.com" class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800/80 border-0 focus:ring-2 focus:ring-sky-500 text-sm text-slate-700 dark:text-slate-200 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1.5">Password Key</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-3 text-slate-400"><i class="fas fa-key text-sm"></i></span>
                    <input type="password" id="signup-password" required placeholder="Choose a safe password" class="w-full pl-10 pr-10 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800/80 border-0 focus:ring-2 focus:ring-sky-500 text-sm text-slate-700 dark:text-slate-200 outline-none">
                    <button type="button" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600 focus:outline-none" onclick="togglePasswordView('signup-password')">
                        <i id="eye-signup-password" class="fas fa-eye text-sm"></i>
                    </button>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 bg-gradient-to-r from-teal-600 to-sky-500 hover:brightness-110 text-white rounded-xl font-bold text-sm shadow-lg hover:shadow-teal-500/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2 outline-none">
                    <span>Create Secure Profile</span>
                    <i class="fas fa-sparkles text-sm"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Live Admin Route redirection card -->
    <div class="mt-8 text-center">
        <a href="admin/login.php" class="p-2.5 rounded-xl font-semibold text-xs border border-slate-200/50 dark:border-white/5 hover:bg-slate-100 dark:hover:bg-slate-800/80 hover:text-sky-500 transition-all">
            <i class="fas fa-shield-halved mr-1.5"></i>
            Admin Center Gateway
        </a>
    </div>
</main>

<script>
function switchAuthTab(tab) {
    triggerVibe(30);
    const logBtn = document.getElementById('toggle-login');
    const sigBtn = document.getElementById('toggle-signup');
    const logFrm = document.getElementById('form-login');
    const sigFrm = document.getElementById('form-signup');
    
    if (tab === 'login') {
        logBtn.className = "flex-1 py-2.5 rounded-xl text-sm font-semibold transition-all focus:outline-none bg-white dark:bg-slate-700 text-slate-800 dark:text-white";
        sigBtn.className = "flex-1 py-2.5 rounded-xl text-sm font-semibold transition-all focus:outline-none text-slate-400 dark:text-slate-400";
        logFrm.classList.remove('hidden');
        sigFrm.classList.add('hidden');
    } else {
        sigBtn.className = "flex-1 py-2.5 rounded-xl text-sm font-semibold transition-all focus:outline-none bg-white dark:bg-slate-700 text-slate-800 dark:text-white";
        logBtn.className = "flex-1 py-2.5 rounded-xl text-sm font-semibold transition-all focus:outline-none text-slate-400 dark:text-slate-400";
        sigFrm.classList.remove('hidden');
        logFrm.classList.add('hidden');
    }
}

function togglePasswordView(fieldId) {
    triggerVibe(20);
    const input = document.getElementById(fieldId);
    const eye = document.getElementById('eye-' + fieldId);
    if (input.type === 'password') {
        input.type = 'text';
        eye.className = 'fas fa-eye-slash text-sm';
    } else {
        input.type = 'password';
        eye.className = 'fas fa-eye text-sm';
    }
}

function submitLoginForm(event) {
    event.preventDefault();
    triggerVibe(50);
    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;
    
    fetch('/api/auth/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password })
    })
    .then(async res => {
        if (!res.ok) {
            const err = await res.json();
            throw new Error(err.error || 'Authenication failed');
        }
        return res.json();
    })
    .then(data => {
        if (data.success) {
            localStorage.setItem(activeSessionKey, JSON.stringify(data.user));
            Toast.success(`Welcome back ${data.user.name}!`);
            const urlParams = new URLSearchParams(window.location.search);
            const redirectUrl = urlParams.get('redirect') || 'index.php';
            setTimeout(() => {
                window.location.href = redirectUrl;
            }, 800);
        }
    })
    .catch(err => {
        Toast.error(err.message);
    });
}

function submitSignupForm(event) {
    event.preventDefault();
    triggerVibe(60);
    const name = document.getElementById('signup-name').value;
    const phone = document.getElementById('signup-phone').value;
    const email = document.getElementById('signup-email').value;
    const password = document.getElementById('signup-password').value;
    
    fetch('/api/auth/register', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name, phone, email, password })
    })
    .then(async res => {
        if (!res.ok) {
            const err = await res.json();
            throw new Error(err.error || 'Server error on creation');
        }
        return res.json();
    })
    .then(data => {
        if (data.success) {
            localStorage.setItem(activeSessionKey, JSON.stringify(data.user));
            Toast.success('Profile created successfully! Loading dashboard.');
            
            // Welcome notification initialization
            fetch('/api/notifications/bulk', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    user_id: data.user.id,
                    title: "Welcome to Sellora! 🚀",
                    message: "You have verified your account profile. Search files to secure instant download permissions immediately."
                })
            }).catch(() => {});
            
            const urlParams = new URLSearchParams(window.location.search);
            const redirectUrl = urlParams.get('redirect') || 'index.php';
            setTimeout(() => {
                window.location.href = redirectUrl;
            }, 800);
        }
    })
    .catch(err => {
        Toast.error(err.message);
    });
}
</script>

<?php include __DIR__ . '/common/bottom.php'; ?>
<?php include __DIR__ . '/common/toast.php'; ?>
<?php include __DIR__ . '/common/sidebar.php'; ?>

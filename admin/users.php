<?php
// Sellora - Admin Users blocklist profiles and custom alert dispatch
require_once __DIR__ . '/../common/config.php';
?>
<?php include __DIR__ . '/common/header.php'; ?>

<!-- MAIN USERS DICTIONARY -->
<main class="max-w-md mx-auto px-4 pt-4 pb-20">
    
    <div class="mb-5">
        <h1 class="text-xl font-display font-black text-slate-850 dark:text-white">Profile Accounts</h1>
        <p class="text-[10px] text-slate-450 dark:text-slate-500 font-bold uppercase tracking-wider">Active users and block permissions</p>
    </div>

    <!-- NOTIFICATION TARGET OVERLAY -->
    <div id="alert-modal" class="hidden fixed inset-0 z-50 bg-slate-950/75 backdrop-blur-md flex items-center justify-center p-6 text-slate-850 dark:text-slate-100">
        <div class="max-w-sm w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-2xl relative">
            <h3 class="text-sm font-black uppercase tracking-wider text-slate-550 dark:text-slate-400 mb-4 pb-2 border-b border-slate-100 dark:border-slate-800">Send Target Push Alert</h3>
            
            <input type="hidden" id="alert-user-id" value="">
            <div class="space-y-4 text-xs">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1.5">Alert Subject Title</label>
                    <input type="text" id="alert-title-input" placeholder="e.g. Account security warning option" class="w-full px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1.5 font-sans">Message notification body</label>
                    <textarea id="alert-body-input" rows="3" placeholder="Describe the notification trigger..." class="w-full p-3 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none"></textarea>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-2.5">
                <button onclick="dismissAlertModal()" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 rounded-xl text-xs font-bold text-slate-650 dark:text-slate-300">Discard</button>
                <button onclick="dispatchCustomUserAlert()" class="px-5 py-2 bg-sky-500 hover:bg-sky-600 rounded-xl text-xs font-bold text-white shadow-md">Post Trigger</button>
            </div>
        </div>
    </div>

    <!-- USERS DIRECTORY ROWS -->
    <div id="users-feed" class="space-y-3.5">
        <!-- Shimmer loaders -->
        <div class="h-16 rounded-2xl bg-slate-200 dark:bg-slate-850 animate-pulse"></div>
        <div class="h-16 rounded-2xl bg-slate-200 dark:bg-slate-850 animate-pulse"></div>
    </div>

</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const admin = getSessionAdmin();
    if (!admin) { window.location.href="login.php"; return; }
    loadAdminUsersDirectory();
});

function loadAdminUsersDirectory() {
    fetch('/api/users')
        .then(res => res.json())
        .then(users => {
            const container = document.getElementById('users-feed');
            
            if (users.length === 0) {
                container.innerHTML = `<p class="text-xs text-slate-400 italic text-center py-4">No consumer accounts registered inside system database.</p>`;
                return;
            }
            
            container.innerHTML = users.map(u => `
                <div class="p-3.5 rounded-2xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 shadow-sm backdrop-blur-md flex items-center justify-between">
                    <div class="min-w-0 pr-2">
                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate leading-snug">${u.name}</h4>
                        <span class="text-[10px] text-slate-450 dark:text-slate-500 font-semibold block mt-0.5">${u.email}</span>
                    </div>
                    
                    <div class="flex items-center gap-1.5">
                        <!-- Direct notify button -->
                        <button onclick="triggerAlertModal(${u.id})" class="w-8 h-8 rounded-lg bg-sky-500/10 text-sky-500 hover:bg-sky-500 hover:text-white flex items-center justify-center transition-all outline-none" title="Send direct push alerts">
                            <i class="fas fa-bell text-[10px]"></i>
                        </button>

                        <!-- Block toggler -->
                        <button onclick="toggleUserActivationBlock(${u.id}, '${u.status}')" class="px-2.5 py-1.5 text-[10px] font-black uppercase rounded-lg ${u.status === 'active' ? 'bg-slate-100 hover:bg-red-500 hover:text-white dark:bg-slate-800 text-slate-500 dark:text-slate-400' : 'bg-red-100 hover:bg-emerald-500 hover:text-white dark:bg-red-950/40 text-red-600 dark:text-red-400'} flex items-center justify-center transition-all outline-none">
                            ${u.status === 'active' ? 'Block' : 'Unblock'}
                        </button>
                    </div>
                </div>
            `).join('');
        });
}

function triggerAlertModal(userId) {
    triggerVibe(30);
    document.getElementById('alert-user-id').value = userId;
    document.getElementById('alert-title-input').value = '';
    document.getElementById('alert-body-input').value = '';
    document.getElementById('alert-modal').classList.remove('hidden');
}

function dismissAlertModal() {
    triggerVibe(20);
    document.getElementById('alert-modal').classList.add('hidden');
}

function dispatchCustomUserAlert() {
    triggerVibe(60);
    const userId = document.getElementById('alert-user-id').value;
    const title = document.getElementById('alert-title-input').value;
    const message = document.getElementById('alert-body-input').value;
    
    if (!title || !message) {
        Toast.error("Alert inputs cannot be blank.");
        return;
    }
    
    fetch('/api/notifications/create', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ userId: parseInt(userId), title, message })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Toast.success("Target push notification logged successfully!");
            dismissAlertModal();
        } else {
            Toast.error("Failed alert posting.");
        }
    });
}

function toggleUserActivationBlock(id, status) {
    triggerVibe(50);
    const updatedStatus = status === 'active' ? 'blocked' : 'active';
    
    if (updatedStatus === 'blocked' && !confirm("Do you want to block this user? They will lose access to active vault decryption sheets instantly!")) {
        return;
    }
    
    fetch('/api/users/toggle', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, status: updatedStatus })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Toast.success("User access state altered!");
            loadAdminUsersDirectory();
        } else {
            Toast.error(data.error);
        }
    });
}
</script>

<!-- Custom Toast -->
<div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-3 pointer-events-none max-w-sm w-full px-4 sm:px-0"></div>
<script>
const Toast = {
    show: function(m, type='success') {
        const container = document.getElementById('toast-container');
        const t = document.createElement('div');
        t.className = `p-4 rounded-xl shadow-lg border border-white/5 backdrop-blur-md text-xs font-bold ${type === 'error' ? 'bg-red-500' : 'bg-emerald-500'} text-white transition-all transform duration-300`;
        t.textContent = m;
        container.appendChild(t);
        setTimeout(() => t.remove(), 2200);
    },
    success: function(m) { this.show(m, 'success'); },
    error: function(m) { this.show(m, 'error'); }
};
</script>

<?php include __DIR__ . '/common/bottom.php'; ?>

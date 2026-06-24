<?php
// Sellora - Admin Customer Messages Queries & Newsletter Campaign Dispatcher
require_once __DIR__ . '/../common/config.php';
?>
<?php include __DIR__ . '/common/header.php'; ?>

<main class="max-w-md mx-auto px-4 pt-4 pb-24">
    <!-- Screen Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-xl font-display font-black text-slate-850 dark:text-white">Customer Support Hub</h1>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Queries & Newsletter Center</p>
        </div>
        <a href="settings.php" class="w-9 h-9 rounded-xl flex items-center justify-center bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-200" onclick="triggerVibe(15)">
            <i class="fas fa-chevron-left text-xs"></i>
        </a>
    </div>

    <!-- Administrative Tabs -->
    <div class="flex bg-slate-100 dark:bg-slate-800 p-1 rounded-2xl mb-6">
        <button id="tab-queries" onclick="switchAdminTab('queries')" class="flex-1 py-2 text-xs font-bold rounded-xl transition-all cursor-pointer bg-white dark:bg-slate-900 text-sky-500 shadow-sm">
            Queries
        </button>
        <button id="tab-newsletter" onclick="switchAdminTab('newsletter')" class="flex-1 py-2 text-xs font-bold rounded-xl transition-all cursor-pointer text-slate-550 dark:text-slate-400">
            Newsletter
        </button>
        <button id="tab-pushes" onclick="switchAdminTab('pushes')" class="flex-1 py-2 text-xs font-bold rounded-xl transition-all cursor-pointer text-slate-550 dark:text-slate-400">
            Push Alert
        </button>
    </div>

    <!-- TAB 1: SUPPORT TICKETS & QUERIES LIST -->
    <div id="panel-queries" class="space-y-4">
        <h3 class="text-xs font-black uppercase text-slate-400 tracking-wider">Ticket Queries Inbox</h3>
        
        <div id="queries-loading" class="h-44 flex items-center justify-center bg-white dark:bg-slate-850 rounded-2xl border border-slate-100 dark:border-white/5 shadow-sm">
            <i class="fas fa-spinner animate-spin text-sky-500 mr-2"></i>
            <span class="text-xs text-slate-400 font-medium">Fetching help tickets...</span>
        </div>

        <div id="queries-empty" class="hidden p-6 text-center bg-white dark:bg-slate-850 rounded-2xl border border-slate-100 dark:border-white/5 text-slate-400 text-xs">
            <i class="fas fa-clipboard-check text-2xl mb-2 text-emerald-500"></i>
            <p>Every query ticket is fully solved and responded! Great work.</p>
        </div>

        <div id="queries-list" class="space-y-4">
            <!-- Populated dynamically -->
        </div>
    </div>

    <!-- TAB 2: EMAILS NEWSLETTER CAMPAIGNS -->
    <div id="panel-newsletter" class="hidden space-y-6">
        <!-- Subscribers Stats card -->
        <div class="p-5 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-md relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 opacity-20 text-6xl"><i class="fas fa-mail-bulk"></i></div>
            <span class="block text-[10px] uppercase font-bold text-indigo-100">Live Audiences</span>
            <h2 id="subscriber-count" class="text-3xl font-display font-black my-1">0</h2>
            <p class="text-[10px] text-indigo-100 leading-normal">Active email subscribers tracking new launcher promo campaigns.</p>
        </div>

        <!-- Launch Newsletter Campaign Form -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-850 border border-slate-100 dark:border-white/5 space-y-4 shadow-sm">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-7 h-7 rounded-lg bg-sky-500/10 text-sky-50 flex items-center justify-center text-xs">
                    <i class="fas fa-bullhorn text-sky-500"></i>
                </div>
                <div>
                    <h3 class="font-bold text-xs text-slate-900 dark:text-white">Broadcast Campaign Mailer</h3>
                    <p class="text-[9px] text-slate-400">Forces newsletter dispatch directly</p>
                </div>
            </div>

            <form id="newsletter-campaign-form" class="space-y-3 text-xs font-semibold" onsubmit="handleSendCampaign(event)">
                <div class="space-y-1">
                    <label class="font-bold text-slate-500 text-[10px] uppercase">Newsletter Subject Line</label>
                    <input type="text" id="camp-subject" required placeholder="🎁 Huge 50% discount on newly launched Canva carousel templates!" class="w-full h-10 bg-slate-50 dark:bg-slate-900 border border-slate-200/60 dark:border-white/5 rounded-xl px-3 text-xs focus:outline-none focus:border-sky-500 transition-all text-slate-800 dark:text-slate-150" />
                </div>
                <div class="space-y-1">
                    <label class="font-bold text-slate-500 text-[10px] uppercase">HTML/Plain Body text</label>
                    <textarea id="camp-body" required rows="4" placeholder="Dear subscriber,\n\nWe are thrilled to launch our brand-new Canva template folder directory! Use exclusive promo SAVE50 for 50% checkout discount today..." class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200/60 dark:border-white/5 rounded-xl p-3 text-xs focus:outline-none focus:border-sky-500 transition-all text-slate-800 dark:text-slate-150 leading-relaxed"></textarea>
                </div>
                <button type="submit" class="w-full h-10 bg-sky-600 hover:bg-sky-500 text-white font-semibold rounded-xl text-xs transition-all cursor-pointer shadow-md">
                    Send Campaign Mailer
                </button>
            </form>
        </div>

        <!-- Directory of subscribers block -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-850 border border-slate-100 dark:border-white/5 space-y-3 shadow-sm">
            <h3 class="text-xs font-black uppercase text-slate-400 tracking-wide border-b border-slate-100 dark:border-slate-800 pb-2">Subscriber Emails</h3>
            <div id="subscribers-list-view" class="max-h-40 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800 text-xs text-slate-600 dark:text-slate-450 font-semibold space-y-1.5">
                <!-- Rendered dynamically -->
            </div>
        </div>
    </div>

    <!-- TAB 3: PUSH BROADCAST ALERTS -->
    <div id="panel-pushes" class="hidden space-y-4">
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-850 border border-slate-100 dark:border-white/5 space-y-4 shadow-sm">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-7 h-7 rounded-lg bg-pink-500/10 text-pink-50 flex items-center justify-center text-xs">
                    <i class="fas fa-paper-plane text-pink-500"></i>
                </div>
                <div>
                    <h3 class="font-bold text-xs text-slate-900 dark:text-white">Broadcast Global Push Notification</h3>
                    <p class="text-[9px] text-slate-400">Pushes inline & OS popups to all accounts</p>
                </div>
            </div>

            <form id="admin-push-broad-form" class="space-y-3 text-xs font-semibold" onsubmit="handleSendPushBulk(event)">
                <div class="space-y-1">
                    <label class="font-bold text-slate-500 text-[10px] uppercase">Notification Title</label>
                    <input type="text" id="push-title" required placeholder="⚠️ Complete your secure checkout!" class="w-full h-10 bg-slate-50 dark:bg-slate-950/20 border border-slate-205 dark:border-white/5 rounded-xl px-3 text-xs focus:outline-none focus:border-sky-500 transition-all text-slate-800 dark:text-slate-150" />
                </div>
                <div class="space-y-1">
                    <label class="font-bold text-slate-500 text-[10px] uppercase">Alert message body</label>
                    <textarea id="push-body" required rows="3" placeholder="Get 50% discount instantly! Code: SAVE50" class="w-full bg-slate-50 dark:bg-slate-950/20 border border-slate-205 dark:border-white/5 rounded-xl p-3 text-xs focus:outline-none focus:border-sky-500 transition-all text-slate-800 dark:text-slate-150 leading-relaxed"></textarea>
                </div>
                <button type="submit" class="w-full h-10 bg-pink-600 hover:bg-pink-500 text-white font-semibold rounded-xl text-xs transition-shadow cursor-pointer shadow-md">
                    Broadcast Alert
                </button>
            </form>
        </div>
    </div>
</main>

<script>
let globalQueries = [];

function switchAdminTab(t) {
    triggerVibe(20);
    const tabs = ['queries', 'newsletter', 'pushes'];
    tabs.forEach(tab => {
        const btn = document.getElementById(`tab-${tab}`);
        const panel = document.getElementById(`panel-${tab}`);
        if (tab === t) {
            btn.className = "flex-1 py-2 text-xs font-bold rounded-xl transition-all cursor-pointer bg-white dark:bg-slate-900 text-sky-500 shadow-sm";
            panel.classList.remove('hidden');
        } else {
            btn.className = "flex-1 py-2 text-xs font-bold rounded-xl transition-all cursor-pointer text-slate-400 dark:text-slate-500";
            panel.classList.add('hidden');
        }
    });
}

async function loadSupportData() {
    try {
        const queryRes = await fetch('/api/support/queries');
        const queries = await queryRes.json();
        globalQueries = queries;

        const subRes = await fetch('/api/newsletter/subscribers');
        const subscribers = await subRes.json();

        // Render Queries
        const loading = document.getElementById('queries-loading');
        const empty = document.getElementById('queries-empty');
        const qc = document.getElementById('queries-list');

        loading.classList.add('hidden');
        
        if (queries.length === 0) {
            empty.classList.remove('hidden');
            qc.innerHTML = '';
        } else {
            empty.classList.add('hidden');
            qc.innerHTML = queries.reverse().map(q => `
                <div class="bg-white dark:bg-slate-850 border border-slate-100 dark:border-white/5 rounded-2xl p-5 shadow-sm space-y-3">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-bold text-slate-800 dark:text-slate-100 text-xs">${q.subject}</h4>
                            <p class="text-[10px] text-slate-400 mt-0.5">${q.name} (${q.email})</p>
                        </div>
                        <span class="text-[9px] px-2.5 py-1.5 rounded-full font-bold ${q.is_responded ? 'bg-emerald-500/10 text-emerald-500' : 'bg-rose-500/10 text-rose-500 animate-pulse'}">
                            ${q.is_responded ? 'Responded' : 'Active Ticket'}
                        </span>
                    </div>
                    
                    <p class="text-xs text-slate-505 dark:text-slate-400 bg-slate-50 dark:bg-slate-900/60 p-3 rounded-xl leading-relaxed italic border border-slate-100 dark:border-white/5 select-text">
                        "${q.message}"
                    </p>

                    ${q.is_responded ? `
                        <div class="bg-emerald-500/5 dark:bg-emerald-500/10 border border-emerald-500/10 p-3 rounded-xl text-xs space-y-1">
                            <span class="text-[9px] font-extrabold text-emerald-500 uppercase tracking-wider block">Admin Support Response:</span>
                            <p class="text-slate-650 dark:text-slate-350 select-text font-serif leading-relaxed">"${q.reply_text}"</p>
                        </div>
                    ` : `
                        <div id="reply-box-${q.id}" class="space-y-2">
                            <textarea id="reply-text-${q.id}" rows="2" placeholder="Type support answer response here... (Dispatches real simulated email reply!)" class="w-full text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200/60 dark:border-white/5 rounded-xl p-3 focus:outline-none focus:border-sky-500 transition-all text-slate-800 dark:text-slate-150"></textarea>
                            <button onclick="submitQueryReply(${q.id})" class="w-full h-9 bg-sky-650 bg-sky-600 hover:bg-sky-500 text-white font-bold text-[11px] rounded-xl transition-all cursor-pointer shadow-sm">
                                Dispatch Response
                            </button>
                        </div>
                    `}
                </div>
            `).join('');
        }

        // Render Subscribers
        document.getElementById('subscriber-count').innerText = subscribers.length;
        const subListContainer = document.getElementById('subscribers-list-view');
        if (subscribers.length === 0) {
            subListContainer.innerHTML = `<span class="block text-center text-slate-400 py-4 font-normal">No email subscribers found.</span>`;
        } else {
            subListContainer.innerHTML = subscribers.map(s => `
                <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-white/5 select-text">
                    <span>${s.email}</span>
                    <span class="text-[10px] text-slate-400 font-mono">${new Date(s.subscribed_at).toLocaleDateString()}</span>
                </div>
            `).join('');
        }

    } catch (e) {
        console.error(e);
    }
}

async function submitQueryReply(id) {
    const replyText = document.getElementById(`reply-text-${id}`).value.trim();
    if (!replyText) return;

    if (navigator.vibrate) navigator.vibrate(40);

    try {
        const response = await fetch('/api/support/reply', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, reply_text: replyText })
        });
        const data = await response.json();
        
        if (data.success) {
            Toast.success("Response dispatched & emailed successfully!");
            loadSupportData();
        } else {
            Toast.error("Failed to solve tickets.");
        }
    } catch(err) {
        Toast.error("Network issue.");
    }
}

async function handleSendCampaign(e) {
    e.preventDefault();
    const subject = document.getElementById('camp-subject').value;
    const body = document.getElementById('camp-body').value;

    if (navigator.vibrate) navigator.vibrate(50);

    try {
        const response = await fetch('/api/newsletter/send-campaign', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ subject, body })
        });
        const data = await response.json();
        
        if (data.success) {
            Toast.success(`Campaign broadcasted to ${data.sent_count} accounts!`);
            document.getElementById('newsletter-campaign-form').reset();
        } else {
            Toast.error("Failed executing newsletter broadcast.");
        }
    } catch(err) {
        Toast.error("Disconnection error.");
    }
}

async function handleSendPushBulk(e) {
    e.preventDefault();
    const title = document.getElementById('push-title').value;
    const message = document.getElementById('push-body').value;

    if (navigator.vibrate) navigator.vibrate(50);

    try {
        const response = await fetch('/api/notifications/bulk', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: 'all', title, message })
        });
        const d = await response.json();
        
        if (d.success) {
            // Also invoke browser desktop notifications block if permitted
            if (Notification.permission === 'granted') {
                new Notification(title, { body: message });
            }
            
            Toast.success("Broad alert notifications pushed successfully!");
            document.getElementById('admin-push-broad-form').reset();
        } else {
            Toast.error("Failed pushing alerts.");
        }
    } catch(err) {
        Toast.error("Network issue.");
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Check custom permissions first
    const adm = getSessionAdmin();
    if (!adm) {
        window.location.href = "login.php";
        return;
    }
    
    loadSupportData();
    
    // Request notification clearance standard 
    if (Notification.permission !== 'granted' && Notification.permission !== 'denied') {
        Notification.requestPermission();
    }
});
</script>

<?php include __DIR__ . '/common/bottom.php'; ?>
<?php include __DIR__ . '/../common/toast.php'; ?>

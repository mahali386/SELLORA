<?php
// Sellora - Support Center and FAQ Hub
require_once __DIR__ . '/common/config.php';
?>
<?php include __DIR__ . '/common/header.php'; ?>

<!-- MAIN SECURE HELP PROFILE -->
<main class="max-w-md mx-auto px-4 pt-4 pb-24">
    
    <!-- Support center branding cards -->
    <div class="p-6 rounded-3xl bg-gradient-to-br from-indigo-900 to-sky-900 text-white shadow-md text-center mb-6 relative overflow-hidden">
        <div class="inline-flex w-14 h-14 rounded-2xl bg-white/10 items-center justify-center text-xl border border-white/15 mb-3">
            <i class="fas fa-headset"></i>
        </div>
        <h1 class="text-xl font-display font-black tracking-tight">Active Help Support</h1>
        <p class="text-xs text-slate-300 mt-1 max-w-xs mx-auto leading-relaxed">Have issues regarding payment gateway validation? Seek immediate help coordinate releases.</p>
        
        <!-- Contact quick tags -->
        <div class="mt-5 grid grid-cols-2 gap-3">
            <div class="p-3 rounded-xl bg-white/10 border border-white/5 backdrop-blur-md">
                <span class="text-[10px] block font-semibold text-slate-350">Customer Email</span>
                <a href="mailto:support@digitalmohan.com" class="text-xs font-black hover:underline block truncate mt-0.5">support@digitalmohan.com</a>
            </div>
            <div class="p-3 rounded-xl bg-white/10 border border-white/5 backdrop-blur-md">
                <span class="text-[10px] block font-semibold text-slate-350">Direct Hotline</span>
                <a href="tel:+919876543210" class="text-xs font-black hover:underline block truncate mt-0.5">+91 98765 43210</a>
            </div>
        </div>
    </div>

    <!-- PURE JAVASCRIPT ACCORDIONS FAQ SYSTEM -->
    <div class="space-y-3.5">
        <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2">Frequently Asked Questions</h4>
        
        <!-- FAQ 1 -->
        <div class="rounded-2xl border border-slate-200/50 dark:border-white/5 bg-white/70 dark:bg-slate-900/60 p-4 transition-all duration-300 backdrop-blur-md">
            <button onclick="toggleFAQAccordionRow(this)" class="w-full text-left flex items-center justify-between font-bold text-xs text-slate-800 dark:text-slate-200 focus:outline-none">
                <span>When and how do I receive bought digital products?</span>
                <i class="fas fa-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
            </button>
            <div class="faq-content max-h-0 overflow-hidden transition-all duration-300 ease-out opacity-0">
                <p class="pt-3 text-[11px] leading-relaxed text-slate-550 dark:text-slate-400 font-semibold">
                    Instantly! Once your simulated Razorpay transaction succeeds, your invoice transitions automatically on the server, unlocking downloads. Head directly inside the <span class="text-sky-500 font-bold">Downloads Vault</span> in the footer options to release ZIP formula sheets securely.
                </p>
            </div>
        </div>

        <!-- FAQ 2 -->
        <div class="rounded-2xl border border-slate-200/50 dark:border-white/5 bg-white/70 dark:bg-slate-900/60 p-4 transition-all duration-300 backdrop-blur-md">
            <button onclick="toggleFAQAccordionRow(this)" class="w-full text-left flex items-center justify-between font-bold text-xs text-slate-800 dark:text-slate-200 focus:outline-none">
                <span>Is there any limit to the download count attempts?</span>
                <i class="fas fa-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
            </button>
            <div class="faq-content max-h-0 overflow-hidden transition-all duration-300 ease-out opacity-0">
                <p class="pt-3 text-[11px] leading-relaxed text-slate-550 dark:text-slate-400 font-semibold">
                    For overall asset protection against bots and distributed hotlinking, we enforce a standard rate configuration of max 1 file decrypt challenge per hour limit per token holder. This prevents link-sharing and protects our premium creators from piracy.
                </p>
            </div>
        </div>

        <!-- FAQ 3 -->
        <div class="rounded-2xl border border-slate-200/50 dark:border-white/5 bg-white/70 dark:bg-slate-900/60 p-4 transition-all duration-300 backdrop-blur-md">
            <button onclick="toggleFAQAccordionRow(this)" class="w-full text-left flex items-center justify-between font-bold text-xs text-slate-800 dark:text-slate-200 focus:outline-none">
                <span>Why am I getting "Blocked Access" indicators?</span>
                <i class="fas fa-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
            </button>
            <div class="faq-content max-h-0 overflow-hidden transition-all duration-300 ease-out opacity-0">
                <p class="pt-3 text-[11px] leading-relaxed text-slate-550 dark:text-slate-400 font-semibold">
                    Our digital protection system automatically scans client request configurations. If we track multiple IPs downloading identical ZIP archives using your signature hash key, the system permanently blocks profile credentials. Contact support above to appeal locks.
                </p>
            </div>
        </div>

        <!-- FAQ 4 -->
        <div class="rounded-2xl border border-slate-200/50 dark:border-white/5 bg-white/70 dark:bg-slate-900/60 p-4 transition-all duration-300 backdrop-blur-md">
            <button onclick="toggleFAQAccordionRow(this)" class="w-full text-left flex items-center justify-between font-bold text-xs text-slate-800 dark:text-slate-200 focus:outline-none">
                <span>Can I appeal a failed checkout order charge?</span>
                <i class="fas fa-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
            </button>
            <div class="faq-content max-h-0 overflow-hidden transition-all duration-300 ease-out opacity-0">
                <p class="pt-3 text-[11px] leading-relaxed text-slate-550 dark:text-slate-400 font-semibold">
                    Yes. Any simulated order status resulting in "Failed" is audited immediately. If the transaction gateway logs double deductions, the server releases your key authorization within 24 hours automatically. Log support tickets if issues persist.
                </p>
            </div>
        </div>
    </div>

    <!-- 1. WHATSAPP & TICKET SUPPORT MODULES -->
    <div class="mt-6 space-y-4">
        <!-- WhatsApp Connect Card -->
        <a href="https://api.whatsapp.com/send?phone=+919876543210&text=Hi%20DigitalMohan%20Support!%20I%20have%20a%20question%20regarding%2520digital%2520downloads." target="_blank" onclick="triggerVibe(30)" class="flex items-center justify-between p-5 rounded-2xl bg-gradient-to-r from-emerald-500/10 to-teal-500/10 border border-emerald-500/20 hover:border-emerald-500/40 transition-all outline-none">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg animate-bounce">
                    <i class="fab fa-whatsapp"></i>
                </div>
                <div>
                    <h3 class="font-bold text-xs text-slate-900 dark:text-white">Instant WhatsApp Support</h3>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Available 24x7 for checkout queries</p>
                </div>
            </div>
            <i class="fas fa-arrow-right text-xs text-emerald-500"></i>
        </a>

        <!-- GEMINI AI support chatbot panel -->
        <div class="bg-white dark:bg-slate-850 rounded-2xl p-5 border border-slate-150 dark:border-white/5 shadow-sm space-y-3">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-7 h-7 rounded-lg bg-sky-500/10 text-sky-500 flex items-center justify-center text-xs">
                    <i class="fas fa-robot text-sky-500"></i>
                </div>
                <div>
                    <h3 class="font-bold text-xs text-slate-900 dark:text-white">DigitalMohan Support Agent AI</h3>
                    <p class="text-[9px] text-slate-400">Powered by Gemini 3.5 Flash</p>
                </div>
            </div>
            
            <!-- Chat history viewer -->
            <div id="ai-chat-history" class="h-44 overflow-y-auto bg-slate-50 dark:bg-slate-900 rounded-xl p-3.5 space-y-3.5 text-xs border border-slate-100 dark:border-white/5">
                <div class="chat-bubble flex gap-2">
                    <div class="w-5 h-5 rounded-md bg-sky-500 text-white flex items-center justify-center text-[9px] flex-shrink-0 font-bold">AI</div>
                    <div class="bg-sky-50 dark:bg-sky-950/30 text-slate-700 dark:text-slate-300 p-2.5 rounded-2xl rounded-tl-none font-medium text-[11px] leading-relaxed">
                        Hello! I am your DigitalMohan support agent. Drop any questions regarding formatting, PDF receipts, refund regulations, or templates here! ⚡
                    </div>
                </div>
            </div>

            <!-- Input area -->
            <form id="ai-chat-form" class="flex gap-2" onsubmit="handleAIChatSubmit(event)">
                <input 
                    type="text" 
                    id="ai-chat-input" 
                    placeholder="Ask AI Support..." 
                    class="flex-1 h-10 bg-slate-50 dark:bg-slate-900 border border-slate-200/60 dark:border-white/5 rounded-xl px-3 text-xs focus:border-sky-500 focus:outline-none transition-all text-slate-800 dark:text-slate-150"
                    required
                />
                <button 
                    type="submit" 
                    id="ai-chat-submit-btn"
                    class="w-10 h-10 bg-sky-600 hover:bg-sky-500 disabled:opacity-40 text-white rounded-xl flex items-center justify-center text-xs shadow-md transition-all active:scale-95 cursor-pointer"
                >
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>

        <!-- Submit Support Ticket Query Form -->
        <div class="bg-white dark:bg-slate-850 rounded-2xl p-5 border border-slate-150 dark:border-white/5 shadow-sm space-y-4">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-7 h-7 rounded-lg bg-indigo-500/10 text-indigo-500 flex items-center justify-center text-xs">
                    <i class="fas fa-envelope-open-text"></i>
                </div>
                <div>
                    <h3 class="font-bold text-xs text-slate-900 dark:text-white">Submit a Ticket Query</h3>
                    <p class="text-[9px] text-slate-400">Our team replies in less than 4 hours</p>
                </div>
            </div>

            <form id="support-ticket-form" class="space-y-3 text-xs" onsubmit="handleSupportTicketSubmit(event)">
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="font-bold text-slate-500 text-[10px] uppercase">Your Name</label>
                        <input type="text" id="ticket-name" required class="w-full h-10 bg-slate-50 dark:bg-slate-900 border border-slate-200/60 dark:border-white/5 rounded-xl px-3 text-xs focus:outline-none focus:border-sky-500 transition-all text-slate-800 dark:text-slate-150" />
                    </div>
                    <div class="space-y-1">
                        <label class="font-bold text-slate-500 text-[10px] uppercase">Your Email</label>
                        <input type="email" id="ticket-email" required class="w-full h-10 bg-slate-50 dark:bg-slate-900 border border-slate-200/60 dark:border-white/5 rounded-xl px-3 text-xs focus:outline-none focus:border-sky-500 transition-all text-slate-800 dark:text-slate-150" />
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="font-bold text-slate-500 text-[10px] uppercase">Subject</label>
                    <input type="text" id="ticket-subject" required placeholder="Question about receipt / download crash..." class="w-full h-10 bg-slate-50 dark:bg-slate-900 border border-slate-200/60 dark:border-white/5 rounded-xl px-3 text-xs focus:outline-none focus:border-sky-500 transition-all text-slate-800 dark:text-slate-150" />
                </div>
                <div class="space-y-1">
                    <label class="font-bold text-slate-500 text-[10px] uppercase">Detailed Message</label>
                    <textarea id="ticket-message" required rows="3" placeholder="Tell us what you need help with..." class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200/60 dark:border-white/5 rounded-xl p-3 text-xs focus:outline-none focus:border-sky-500 transition-all text-slate-800 dark:text-slate-150 leading-relaxed"></textarea>
                </div>
                <button type="submit" class="w-full h-10 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl text-xs shadow-md transition-all active:scale-[0.98] mt-2 cursor-pointer">
                    Submit Help Ticket
                </button>
            </form>
        </div>
    </div>

    <!-- LEGAL CORNER LINKS -->
    <div class="mt-6 p-4 rounded-3xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/50 dark:border-white/5 flex justify-around items-center text-xs text-slate-500 font-bold backdrop-blur-md">
        <a href="privacy.php" onclick="triggerVibe(20)" class="hover:text-sky-500 hover:underline flex items-center gap-1.5 transition-all text-slate-650 dark:text-slate-300">
            <i class="fas fa-shield-halved text-[11px] text-slate-450"></i>
            <span>Privacy Policy</span>
        </a>
        <div class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-700"></div>
        <a href="terms.php" onclick="triggerVibe(20)" class="hover:text-sky-500 hover:underline flex items-center gap-1.5 transition-all text-slate-650 dark:text-slate-300">
            <i class="fas fa-scale-balanced text-[11px] text-slate-450"></i>
            <span>Terms & Conditions</span>
        </a>
    </div>

</main>

<script>
let chatHistoryList = [
    { role: 'model', text: "Hello! I am your DigitalMohan support agent. Drop any questions regarding formatting, PDF receipts, refund regulations, or templates here! ⚡" }
];

async function handleAIChatSubmit(e) {
    e.preventDefault();
    const input = document.getElementById('ai-chat-input');
    const container = document.getElementById('ai-chat-history');
    const btn = document.getElementById('ai-chat-submit-btn');
    const txt = input.value.trim();
    if (!txt) return;

    if (navigator.vibrate) navigator.vibrate(30);

    input.value = '';
    btn.disabled = true;

    // Append user bubble
    appendBubble('user', txt);
    chatHistoryList.push({ role: 'user', text: txt });

    // Loading indicator element
    const loadId = appendLoadingBubble();
    container.scrollTop = container.scrollHeight;

    try {
        const response = await fetch('/api/support-chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                message: txt,
                history: chatHistoryList.slice(0, -1) // Excluding last message to avoid overlap
            })
        });
        const data = await response.json();
        
        removeLoadingBubble(loadId);
        appendBubble('model', data.response);
        chatHistoryList.push({ role: 'model', text: data.response });
    } catch (err) {
        removeLoadingBubble(loadId);
        appendBubble('model', "Apologies! I hit a snag connecting with the DigitalMohan support frame. Please re-submit or try our ticket dispatcher!");
    } finally {
        btn.disabled = false;
        container.scrollTop = container.scrollHeight;
    }
}

function appendBubble(role, txt) {
    const container = document.getElementById('ai-chat-history');
    const b = document.createElement('div');
    b.className = "chat-bubble flex gap-2";
    
    if (role === 'user') {
        b.innerHTML = `
            <div class="flex-1 text-right">
                <span class="inline-block bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-200 p-2.5 rounded-2xl rounded-tr-none font-medium text-[11px] leading-relaxed select-text">${txt}</span>
            </div>
            <div class="w-5 h-5 rounded-md bg-slate-400 text-white flex items-center justify-center text-[9px] flex-shrink-0 font-bold uppercase">ME</div>
        `;
    } else {
        b.innerHTML = `
            <div class="w-5 h-5 rounded-md bg-sky-500 text-white flex items-center justify-center text-[9px] flex-shrink-0 font-bold">AI</div>
            <div class="bg-sky-50 dark:bg-sky-950/30 text-slate-700 dark:text-slate-300 p-2.5 rounded-2xl rounded-tl-none font-medium text-[11px] leading-relaxed select-text">${txt}</div>
        `;
    }
    container.appendChild(b);
}

function appendLoadingBubble() {
    const container = document.getElementById('ai-chat-history');
    const b = document.createElement('div');
    const id = "load-" + Date.now();
    b.id = id;
    b.className = "chat-bubble flex gap-2 animate-pulse";
    b.innerHTML = `
        <div class="w-5 h-5 rounded-md bg-sky-500 text-white flex items-center justify-center text-[9px] flex-shrink-0 font-bold">AI</div>
        <div class="bg-slate-100 dark:bg-slate-800/50 text-slate-400 p-2 rounded-xl text-[10px]">Processing message...</div>
    `;
    container.appendChild(b);
    return id;
}

function removeLoadingBubble(id) {
    const el = document.getElementById(id);
    if (el) el.remove();
}

async function handleSupportTicketSubmit(e) {
    e.preventDefault();
    const name = document.getElementById('ticket-name').value;
    const email = document.getElementById('ticket-email').value;
    const subject = document.getElementById('ticket-subject').value;
    const message = document.getElementById('ticket-message').value;

    if (navigator.vibrate) navigator.vibrate(50);

    try {
        const response = await fetch('/api/support/query', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, email, subject, message })
        });
        const data = await response.json();
        
        if (data.success) {
            Toast.success("Support ticket submitted! Clear response emailed soon.");
            document.getElementById('support-ticket-form').reset();
        } else {
            Toast.error("Failed submitting ticket. Try WhatsApp.");
        }
    } catch(err) {
        Toast.error("Network issue. Failed submitting.");
    }
}

function toggleFAQAccordionRow(btn) {
    triggerVibe(30);
    const container = btn.parentElement;
    const content = container.querySelector('.faq-content');
    const icon = btn.querySelector('i');
    
    const isClosed = content.style.maxHeight === '0px' || !content.style.maxHeight;
    
    // Close other FAQ rows
    const allFAQ = document.querySelectorAll('.faq-content');
    allFAQ.forEach(el => {
        el.style.maxHeight = '0px';
        el.style.opacity = '0';
        el.parentElement.querySelector('i').classList.remove('rotate-180');
    });

    if (isClosed) {
        content.style.maxHeight = content.scrollHeight + 'px';
        content.style.opacity = '1';
        icon.classList.add('rotate-180');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Fill credentials automatically if logged in 
    const u = getSessionUser();
    if (u) {
        document.getElementById('ticket-name').value = u.name;
        document.getElementById('ticket-email').value = u.email;
    }
});

function getSessionUser() {
    const activeSessionKey = localStorage.getItem("digitalmohan_current_user") ? "digitalmohan_current_user" : "sellora_current_user";
    const data = localStorage.getItem(activeSessionKey);
    return data ? JSON.parse(data) : null;
}
</script>

<?php include __DIR__ . '/common/footer.php'; ?>
<?php include __DIR__ . '/common/bottom.php'; ?>
<?php include __DIR__ . '/common/toast.php'; ?>
<?php include __DIR__ . '/common/sidebar.php'; ?>

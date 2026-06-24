<!-- Comprehensive and Aesthetic Footer for DigitalMohan -->
<footer class="bg-slate-900 text-slate-300 border-t border-slate-800 pt-12 pb-24 px-4 mt-16 transition-colors duration-200">
    <div class="max-w-md mx-auto space-y-10">
        
        <!-- Newsletter Subscription Module -->
        <div id="footer-newsletter" class="bg-slate-800/60 border border-slate-700/50 rounded-3xl p-6 shadow-xl relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-24 h-24 bg-sky-500/10 rounded-full blur-xl"></div>
            <h3 class="font-display font-bold text-lg text-white mb-2 flex items-center gap-2">
                <i class="far fa-paper-plane text-sky-400"></i>
                Subscribe to Newsletter
            </h3>
            <p class="text-xs text-slate-400 mb-4 leading-relaxed">
                Stay updated with brand new ChatGPT prompts, resume drops, formula guides, and massive seasonal coupon discounts!
            </p>
            <form id="newsletter-form" class="space-y-3" onsubmit="handleNewsletterSubmit(event)">
                <div class="relative">
                    <input 
                        type="email" 
                        id="newsletter-email" 
                        required 
                        placeholder="yourname@gmail.com" 
                        class="w-full h-11 bg-slate-900 border border-slate-700/80 rounded-2xl px-4 text-xs font-medium text-white placeholder-slate-500 focus:border-sky-500 focus:outline-none transition-all"
                    />
                </div>
                <button 
                    type="submit" 
                    class="w-full h-11 bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs rounded-2xl transition-all shadow-lg active:scale-[0.98] cursor-pointer"
                >
                    Subscribe Now
                </button>
            </form>
            <div id="newsletter-msg" class="mt-2.5 text-[11px] text-center font-medium hidden"></div>
        </div>

        <!-- Links matrix -->
        <div class="grid grid-cols-2 gap-8 text-xs">
            <div class="space-y-3">
                <h4 class="font-bold text-white uppercase tracking-wider text-[10px] text-sky-400">Product Center</h4>
                <ul class="space-y-2">
                    <li><a href="products.php" class="hover:text-white transition-colors">Digital Files Directory</a></li>
                    <li><a href="wishlist.php" class="hover:text-white transition-colors">Wishlist Catalog</a></li>
                    <li><a href="mydownloads.php" class="hover:text-white transition-colors">Purchased Assets</a></li>
                    <li><a href="help.php" class="hover:text-white transition-colors">Help & Live Support</a></li>
                </ul>
            </div>
            <div class="space-y-3">
                <h4 class="font-bold text-white uppercase tracking-wider text-[10px] text-sky-400">Legal & Terms</h4>
                <ul class="space-y-2">
                    <li><a href="refund.php" class="hover:text-white transition-colors font-semibold flex items-center gap-1"><i class="fas fa-shield-halved text-emerald-400 text-[10px]"></i> Refund Policy</a></li>
                    <li><a href="terms.php" class="hover:text-white transition-colors">Terms of Service</a></li>
                    <li><a href="privacy.php" class="hover:text-white transition-colors">Privacy Policy</a></li>
                    <li><a href="help.php" class="hover:text-white transition-colors">FAQ Support Desk</a></li>
                </ul>
            </div>
        </div>

        <!-- Divider -->
        <div class="border-t border-slate-800/80"></div>

        <!-- Branding & Social Media Connect -->
        <div class="flex flex-col items-center space-y-4">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-sky-500 rounded-full"></span>
                <span class="font-display font-black text-white tracking-tight">DigitalMohan Digital Store</span>
            </div>
            <p class="text-[10px] text-slate-500 text-center leading-relaxed">
                Empowering content creators, developers, designers, and students with top-grade layouts, files and sheets.
            </p>
            
            <!-- Social networks icons -->
            <div class="flex justify-center items-center gap-3">
                <a href="https://facebook.com/digitalmohandigital" target="_blank" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-sky-600 hover:text-white flex items-center justify-center text-xs transition-all duration-300" title="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://youtube.com/digitalmohandigital" target="_blank" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-red-650 hover:bg-red-600 hover:text-white flex items-center justify-center text-xs transition-all duration-300" title="YouTube">
                    <i class="fab fa-youtube"></i>
                </a>
                <a href="https://instagram.com/digitalmohandigital" target="_blank" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-gradient-to-tr hover:from-yellow-500 hover:via-pink-500 hover:to-purple-500 hover:text-white flex items-center justify-center text-xs transition-all duration-300" title="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://linkedin.com/company/digitalmohan" target="_blank" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-blue-700 hover:text-white flex items-center justify-center text-xs transition-all duration-300" title="LinkedIn">
                    <i class="fab fa-linkedin-in"></i>
                </a>
                <a href="https://github.com/digitalmohan-inc" target="_blank" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-slate-700 hover:text-white flex items-center justify-center text-xs transition-all duration-300" title="GitHub">
                    <i class="fab fa-github"></i>
                </a>
            </div>
        </div>

        <div class="text-[10px] text-center text-slate-600">
            &copy; 2026 DigitalMohan Inc. All rights reserved. Registered Digital Vendor.
        </div>
    </div>
</footer>

<script>
async function handleNewsletterSubmit(e) {
    e.preventDefault();
    const btn = e.target.querySelector('button[type="submit"]');
    const input = document.getElementById('newsletter-email');
    const msg = document.getElementById('newsletter-msg');
    
    if (navigator.vibrate) navigator.vibrate(30);
    
    const initialText = btn.innerText;
    btn.disabled = true;
    btn.innerText = "Subscribing...";
    
    try {
        const response = await fetch('/api/newsletter/subscribe', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: input.value })
        });
        const d = await response.json();
        
        msg.classList.remove('hidden', 'text-amber-400', 'text-emerald-400');
        if (d.success) {
            msg.innerText = d.message;
            msg.classList.add('text-emerald-400');
            input.value = '';
            if (typeof Toast !== 'undefined') Toast.success("Newsletter Active!");
        } else {
            msg.innerText = d.error || "Subscription failed.";
            msg.classList.add('text-amber-400');
        }
    } catch (err) {
        msg.innerText = "Network issue. Please try again.";
        msg.classList.add('text-amber-400');
    } finally {
        btn.disabled = false;
        btn.innerText = initialText;
    }
}
</script>

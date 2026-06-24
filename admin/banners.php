<?php
// Sellora - Admin Promotion Hero Banners Controller
require_once __DIR__ . '/../common/config.php';
?>
<?php include __DIR__ . '/common/header.php'; ?>

<!-- MAIN BANNERS BASE -->
<main class="max-w-md mx-auto px-4 pt-4 pb-20">
    
    <div class="mb-5 flex items-center justify-between">
         <div>
             <h1 class="text-xl font-display font-black text-slate-850 dark:text-white">Home Banners</h1>
             <p class="text-[10px] text-slate-450 dark:text-slate-500 font-bold uppercase tracking-wider">Configure Hero Carousel Slides</p>
         </div>
         
         <button onclick="triggerNewBannerCreator()" class="px-3.5 py-1.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl text-xs font-bold shadow-md active:scale-95 transition-all outline-none flex items-center gap-1">
             <i class="fas fa-plus text-[10px]"></i><span>Add Slide</span>
         </button>
    </div>

    <!-- ADD/EDIT BANNER MODAL OVERLAY -->
    <div id="banner-modal" class="hidden fixed inset-0 z-50 bg-slate-950/75 backdrop-blur-md flex items-center justify-center p-6 text-slate-800 dark:text-slate-100">
        <div class="max-w-sm w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-2xl relative">
            <h3 id="modal-title" class="text-xs font-black uppercase tracking-wider text-slate-550 dark:text-slate-400 mb-4 pb-2 border-b border-slate-100 dark:border-slate-800">New Banner Slide Setup</h3>
            
            <form id="banner-form" onsubmit="saveBannerSlideDetails(event)" class="space-y-4 text-xs font-semibold">
                <input type="hidden" id="b-id">
                
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1.5">Badge Text (e.g. HOT SALE, % OFF)</label>
                    <input type="text" id="b-badge" required placeholder="HOT SALE" class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1.5">Slide Title Heading</label>
                    <input type="text" id="b-title" required placeholder="All Prompt Packs of ChatGPT" class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1.5">Slide Subtext/Description</label>
                    <textarea id="b-subtitle" required placeholder="Boost production by 10x instantly. Copypasta templates folders library." rows="2" class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none resize-none"></textarea>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1.5 font-sans">Redirect Link URL Path</label>
                    <input type="text" id="b-link" required placeholder="products.php?cat=1" class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none font-mono">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1.5">Aesthetic Color Backdrop</label>
                    <select id="b-gradient" class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none">
                        <option value="from-indigo-900 to-sky-900">Midnight Deep (Indigo-Sky)</option>
                        <option value="from-emerald-950 to-teal-850">Emerald Forest (Green-Teal)</option>
                        <option value="from-indigo-950 to-purple-900">Amethyst Cosmic (Violet-Purple)</option>
                        <option value="from-red-950 to-orange-850">Crimson Fire (Red-Orange)</option>
                        <option value="from-slate-900 to-slate-950">Carbon Slate (Noir Slate)</option>
                        <option value="from-blue-900 to-indigo-950">Ocean Depths (Classic Blue)</option>
                    </select>
                </div>

                <div class="mt-6 flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="dismissBannerCreator()" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 rounded-xl text-xs font-bold text-slate-650 dark:text-slate-300">Discard</button>
                    <button type="submit" class="px-5 py-2 bg-sky-500 hover:bg-sky-600 rounded-xl text-xs font-bold text-white shadow-md">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- BANNERS LIST FEED -->
    <div id="banners-feed" class="space-y-4">
         <!-- Loading skeletons -->
         <div class="h-28 rounded-3xl bg-slate-200 dark:bg-slate-850 animate-pulse"></div>
         <div class="h-28 rounded-3xl bg-slate-200 dark:bg-slate-850 animate-pulse"></div>
    </div>

</main>

<script>
let bannersList = [];

document.addEventListener('DOMContentLoaded', () => {
    const admin = getSessionAdmin();
    if (!admin) { window.location.href="login.php"; return; }
    
    loadAdminBannersInventory();
});

function loadAdminBannersInventory() {
    fetch('/api/banners')
        .then(res => res.json())
        .then(banners => {
            bannersList = banners;
            const container = document.getElementById('banners-feed');
            
            if (banners.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-10 rounded-3xl border border-dashed border-slate-250 dark:border-slate-800 bg-white/40 dark:bg-slate-900/40">
                        <p class="text-xs text-slate-400">No promo slides defined. Setup dynamic banners above.</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = banners.map(b => `
                <div class="p-4 rounded-3xl bg-gradient-to-br ${b.bg_gradient} text-white shadow-md relative overflow-hidden group">
                     <!-- Controls -->
                     <div class="absolute top-4 right-4 z-10 flex gap-2">
                         <button onclick="editBannerSlideRecord(${b.id})" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all outline-none">
                             <i class="fas fa-edit text-[10px]"></i>
                         </button>
                         <button onclick="discardBannerSlideRecord(${b.id})" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-red-500 hover:text-white flex items-center justify-center text-red-300 transition-all outline-none">
                             <i class="fas fa-trash-can text-[10px]"></i>
                         </button>
                     </div>

                     <span class="bg-white/20 text-white text-[8px] font-black uppercase px-2 py-0.5 rounded-full inline-block mb-2 backdrop-blur-md font-sans">${b.badge}</span>
                     <h3 class="text-sm font-bold leading-snug pr-16">${b.title}</h3>
                     <p class="text-[10px] text-white/70 mt-1 line-clamp-2 leading-relaxed font-medium pr-8">${b.subtitle}</p>
                     
                     <div class="mt-3.5 flex items-center gap-1 text-[9px] font-black uppercase text-sky-300 tracking-wider">
                         <span>Maps to: ${b.link_url}</span>
                         <i class="fas fa-link text-[8px]"></i>
                     </div>
                </div>
            `).join('');
        });
}

function triggerNewBannerCreator() {
    triggerVibe(30);
    document.getElementById('b-id').value = '';
    document.getElementById('banner-form').reset();
    document.getElementById('modal-title').textContent = "New Banner Slide Setup";
    document.getElementById('banner-modal').classList.remove('hidden');
}

function editBannerSlideRecord(id) {
    triggerVibe(30);
    const b = bannersList.find(item => item.id === id);
    if (!b) return;
    
    document.getElementById('b-id').value = b.id;
    document.getElementById('b-badge').value = b.badge;
    document.getElementById('b-title').value = b.title;
    document.getElementById('b-subtitle').value = b.subtitle;
    document.getElementById('b-link').value = b.link_url;
    document.getElementById('b-gradient').value = b.bg_gradient;
    
    document.getElementById('modal-title').textContent = "Edit Banner Slide Specifications";
    document.getElementById('banner-modal').classList.remove('hidden');
}

function dismissBannerCreator() {
    triggerVibe(20);
    document.getElementById('banner-modal').classList.add('hidden');
}

function saveBannerSlideDetails(e) {
    if (e) e.preventDefault();
    triggerVibe(50);
    
    const id = document.getElementById('b-id').value;
    const badge = document.getElementById('b-badge').value;
    const title = document.getElementById('b-title').value;
    const subtitle = document.getElementById('b-subtitle').value;
    const link_url = document.getElementById('b-link').value;
    const bg_gradient = document.getElementById('b-gradient').value;
    
    const isEdit = id !== '';
    const endPoint = isEdit ? '/api/banners/update' : '/api/banners/create';
    
    const payload = { badge, title, subtitle, link_url, bg_gradient };
    if (isEdit) payload.id = parseInt(id);
    
    fetch(endPoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Toast.success(isEdit ? "Banner specifications updated successfully!" : "New promotion slide established!");
            dismissBannerCreator();
            loadAdminBannersInventory();
        } else {
            Toast.error(data.error || "Save error occurred.");
        }
    });
}

function discardBannerSlideRecord(id) {
    if (!confirm("Are you sure you want to permanently discard this promo slide?")) {
        return;
    }
    
    triggerVibe(75);
    fetch('/api/banners/delete', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Toast.success("Banner slide deleted.");
            loadAdminBannersInventory();
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

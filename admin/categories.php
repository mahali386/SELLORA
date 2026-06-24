<?php
// Sellora - Admin Categories Management Panel 
require_once __DIR__ . '/../common/config.php';
?>
<?php include __DIR__ . '/common/header.php'; ?>

<!-- MAIN CAT METRICS -->
<main class="max-w-md mx-auto px-4 pt-4 pb-20">
    
    <div class="mb-5 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-display font-black text-slate-850 dark:text-white">Product Categories</h1>
            <p class="text-[10px] text-slate-450 dark:text-slate-500 font-bold uppercase tracking-wider">Classification Matrix</p>
        </div>
        
        <button onclick="triggerNewCategoryModal()" class="px-3.5 py-1.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl text-xs font-bold shadow-md active:scale-95 transition-all outline-none">
            <i class="fas fa-plus mr-1"></i>New Class
        </button>
    </div>

    <!-- ADD/EDIT DIALOG BOX MODAL (Absolute Overlay) -->
    <div id="category-modal" class="hidden fixed inset-0 z-50 bg-slate-950/75 backdrop-blur-md flex items-center justify-center p-6 text-slate-800 dark:text-slate-100">
        <div class="max-w-sm w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-2xl relative">
            <h3 id="modal-title" class="text-sm font-black uppercase tracking-wider text-slate-550 dark:text-slate-400 mb-4 pb-2 border-b border-slate-100 dark:border-slate-800">Add New Category</h3>
            
            <input type="hidden" id="edit-cat-id" value="">
            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 mb-1.5">Category Name</label>
                    <input type="text" id="input-cat-name" placeholder="e.g. Photoshop brushes" class="w-full px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-850 text-xs font-semibold text-slate-800 dark:text-slate-150 border-0 outline-none focus:ring-2 focus:ring-sky-500">
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-2.5">
                <button onclick="dismissCategoryModal()" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:brightness-110 rounded-xl text-xs font-bold text-slate-650 dark:text-slate-300">Cancel</button>
                <button onclick="saveCategoryRow()" class="px-5 py-2 bg-sky-500 hover:bg-sky-600 rounded-xl text-xs font-bold text-white shadow-md">Save Changes</button>
            </div>
        </div>
    </div>

    <!-- LIST OF CLASSES -->
    <div id="categories-feed-rows" class="space-y-3.5">
        <!-- Shimmer Loader -->
        <div class="h-14 rounded-2xl bg-slate-200 dark:bg-slate-850 animate-pulse"></div>
        <div class="h-14 rounded-2xl bg-slate-200 dark:bg-slate-850 animate-pulse"></div>
    </div>

</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const admin = getSessionAdmin();
    if (!admin) { window.location.href="login.php"; return; }
    loadCategoryClassesList();
});

function loadCategoryClassesList() {
    fetch('/api/categories')
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('categories-feed-rows');
            
            if (data.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-10 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800">
                        <p class="text-xs text-slate-400">No product categories parsed. Create one above.</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = data.map(c => `
                <div class="p-3.5 rounded-2xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 shadow-sm backdrop-blur-md flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200">${c.name}</span>
                    <div class="flex items-center gap-1.5">
                        <button onclick="triggerEditCategoryModal(${c.id}, '${c.name}')" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-sky-500 hover:text-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 flex items-center justify-center transition-all outline-none">
                            <i class="fas fa-edit text-[10px]"></i>
                        </button>
                        <button onclick="deleteCategoryClass(${c.id})" class="w-7 h-7 rounded-lg bg-red-100 hover:bg-red-500 hover:text-white dark:bg-red-950/40 text-red-600 dark:text-red-400 flex items-center justify-center transition-all outline-none">
                            <i class="fas fa-trash-can text-[10px]"></i>
                        </button>
                    </div>
                </div>
            `).join('');
        });
}

function triggerNewCategoryModal() {
    triggerVibe(30);
    document.getElementById('edit-cat-id').value = '';
    document.getElementById('input-cat-name').value = '';
    document.getElementById('modal-title').textContent = "Add New Category";
    document.getElementById('category-modal').classList.remove('hidden');
}

function triggerEditCategoryModal(id, currentName) {
    triggerVibe(30);
    document.getElementById('edit-cat-id').value = id;
    document.getElementById('input-cat-name').value = currentName;
    document.getElementById('modal-title').textContent = "Edit Category Name";
    document.getElementById('category-modal').classList.remove('hidden');
}

function dismissCategoryModal() {
    triggerVibe(20);
    document.getElementById('category-modal').classList.add('hidden');
}

function saveCategoryRow() {
    triggerVibe(50);
    const id = document.getElementById('edit-cat-id').value;
    const name = document.getElementById('input-cat-name').value;
    
    if (!name || name.trim().length === 0) {
        Toast.error("Category label can't be blank!");
        return;
    }
    
    const endpoint = id ? '/api/categories/update' : '/api/categories/create';
    const body = id ? { id: parseInt(id), name } : { name };
    
    fetch(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Toast.success("Classification saved dynamically!");
            dismissCategoryModal();
            loadCategoryClassesList();
        } else {
            Toast.error(data.error || "Execution failed.");
        }
    });
}

function deleteCategoryClass(id) {
    if (!confirm("Are you sure you want to discard this category partition? All related products remain but won't hold assignments!")) {
        return;
    }
    
    triggerVibe(70);
    fetch('/api/categories/delete', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Toast.success("Category deleted.");
            loadCategoryClassesList();
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

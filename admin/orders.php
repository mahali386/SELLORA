<?php
// Sellora - Admin Orders list and Exporting CSV reports
require_once __DIR__ . '/../common/config.php';
?>
<?php include __DIR__ . '/common/header.php'; ?>

<!-- MAIN ORDERS HISTORY BOARD -->
<main class="max-w-md mx-auto px-4 pt-4 pb-20">
    
    <div class="mb-5 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-display font-black text-slate-850 dark:text-white">Sales Logs</h1>
            <p class="text-[10px] text-slate-450 dark:text-slate-500 font-bold uppercase tracking-wider">Historical Receipts database</p>
        </div>
        
        <!-- EXPORT DATA TO CSV -->
        <button onclick="exportOrdersToFormattedCSV()" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-md active:scale-95 transition-all outline-none">
            <i class="fas fa-file-csv mr-1"></i>Export CSV
        </button>
    </div>

    <!-- FILTER AND STATS MATRIX -->
    <div class="p-4 rounded-3xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 mb-5 select-none font-semibold text-xs tracking-tight text-slate-600 dark:text-slate-350 space-y-3">
        <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550">Filter gateway states</label>
        <select id="filter-order-status" onchange="loadAdminOrdersHistoryLog()" class="w-full px-3 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-850 text-slate-800 dark:text-slate-150 border-0 outline-none focus:ring-2 focus:ring-sky-500">
            <option value="all">Display All Statuses</option>
            <option value="successful">Successful Payments Only</option>
            <option value="pending">Pending Validation</option>
            <option value="failed">Failed Drops</option>
        </select>
    </div>

    <!-- HISTORIC LIST FEED -->
    <div id="orders-report-feed" class="space-y-3.5">
        <!-- Shimmer Loader -->
        <div class="h-20 rounded-2xl bg-slate-200 dark:bg-slate-850 animate-pulse"></div>
        <div class="h-20 rounded-2xl bg-slate-200 dark:bg-slate-850 animate-pulse"></div>
    </div>

</main>

<script>
let globalOrdersRaw = [];
let usersRawIndex = [];
let productsRawIndex = [];

document.addEventListener('DOMContentLoaded', () => {
    const admin = getSessionAdmin();
    if (!admin) { window.location.href="login.php"; return; }
    
    // Core loads 
    Promise.all([
        fetch('/api/users').then(res => res.json()),
        fetch('/api/products').then(res => res.json())
    ])
    .then(([users, prods]) => {
        usersRawIndex = users;
        productsRawIndex = prods;
        
        loadAdminOrdersHistoryLog();
    });
});

function loadAdminOrdersHistoryLog() {
    fetch('/api/orders')
        .then(res => res.json())
        .then(orders => {
            globalOrdersRaw = orders;
            const statusFilter = document.getElementById('filter-order-status').value;
            const container = document.getElementById('orders-report-feed');
            
            let filtered = [...orders];
            if (statusFilter !== 'all') {
                filtered = filtered.filter(o => o.status === statusFilter);
            }
            
            // Sort recent first 
            filtered.sort((a,b) => new Date(b.created_at) - new Date(a.created_at));
            
            if (filtered.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-10 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800">
                        <p class="text-xs text-slate-400">No order matches search filters.</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = filtered.map(o => {
                const u = usersRawIndex.find(user => user.id === o.user_id);
                const p = productsRawIndex.find(prod => prod.id === o.product_id);
                
                const userEmail = u ? u.email : "Deleted Account";
                const pTitle = p ? p.title : "Scrubbed Catalog Asset";
                
                let badgeClass = "bg-amber-100 text-amber-600 dark:bg-amber-950/40 dark:text-amber-450";
                if (o.status === 'successful') {
                    badgeClass = "bg-emerald-100 text-emerald-650 dark:bg-emerald-950/40 dark:text-emerald-450";
                } else if (o.status === 'failed') {
                    badgeClass = "bg-red-100 text-red-650 dark:bg-red-950/40 dark:text-red-450";
                }

                return `
                    <div class="p-4 rounded-2xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 shadow-sm backdrop-blur-md flex items-center justify-between">
                        <div class="min-w-0 pr-2">
                            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate leading-snug">${pTitle}</h4>
                            <p class="text-[10px] text-slate-450 dark:text-slate-500 font-semibold mt-0.5">${userEmail}</p>
                            <span class="text-[9px] text-slate-400 block mt-1">Transaction Ref: #${o.razorpay_order_id}</span>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="text-xs font-black text-slate-850 dark:text-slate-200 font-mono block">₹${o.amount}</span>
                            <span class="text-[8px] font-black uppercase px-2 py-0.5 rounded-full ${badgeClass} inline-block scale-90 mt-1">${o.status}</span>
                        </div>
                    </div>
                `;
            }).join('');
        });
}

function exportOrdersToFormattedCSV() {
    triggerVibe(60);
    if (globalOrdersRaw.length === 0) {
        Toast.error("Database has no available sales records to output.");
        return;
    }

    Toast.success("Assembling output CSV files sheet...");
    
    // Header Row definition
    let csvRows = [
        ["Order ID", "User Email", "Product Selected", "Amount Paid (INR)", "Gateway Order Hash ID", "Status Profile", "Created Date"]
    ];
    
    globalOrdersRaw.forEach(o => {
        const u = usersRawIndex.find(user => user.id === o.user_id);
        const p = productsRawIndex.find(prod => prod.id === o.product_id);
        
        const userEmail = u ? u.email : "Deleted user";
        const pTitle = p ? p.title.replace(/,/g, " ") : "Scrubbed product";
        
        csvRows.push([
            o.id,
            userEmail,
            pTitle,
            o.amount,
            o.razorpay_order_id,
            o.status,
            new Date(o.created_at).toLocaleString()
        ]);
    });
    
    // Convert 2D arrays to string format
    const csvContent = "data:text/csv;charset=utf-8," 
        + csvRows.map(e => e.join(",")).join("\n");
        
    const encodedUri = encodeURI(csvContent);
    const downloadLink = document.createElement("a");
    downloadLink.setAttribute("href", encodedUri);
    downloadLink.setAttribute("download", `Sellora_Sales_Report_${new Date().toISOString().split('T')[0]}.csv`);
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
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

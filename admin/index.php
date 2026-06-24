<?php
// DigitalMohan - Main Admin Diagnostics Control Board
require_once __DIR__ . '/../common/config.php';
?>
<?php include __DIR__ . '/common/header.php'; ?>

<!-- MAIN ADMIN CONTEXT -->
<main class="max-w-md mx-auto px-4 pt-4 pb-20">
    
    <!-- Title header -->
    <div class="mb-5 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-display font-black text-slate-850 dark:text-white">Admin Control Room</h1>
            <p class="text-[10px] text-slate-450 dark:text-slate-500 font-bold uppercase tracking-wider">Site Metrics & Diagnostics</p>
        </div>
        
        <button onclick="handleAdminSignOut()" class="px-3 py-1.5 bg-red-650/10 text-red-500 hover:bg-red-500 hover:text-white rounded-xl text-[10px] font-black uppercase shadow-sm transition-all outline-none">
            Logout
        </button>
    </div>

    <!-- 1. DIAGNOSTICS ROW (BENTO NUMERIC STATS) -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        
        <!-- Total Revenue -->
        <div class="p-4 rounded-3xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 shadow-sm backdrop-blur-md">
            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 block mb-1">Total Revenue</span>
            <span id="stat-revenue" class="text-2xl font-display font-black text-slate-850 dark:text-slate-100 font-mono">₹---</span>
            <span class="text-[9px] text-emerald-500 font-bold block mt-1"><i class="fas fa-arrow-up-right mr-1"></i>+12.4% MoM</span>
        </div>

        <!-- Total Orders -->
        <div class="p-4 rounded-3xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 shadow-sm backdrop-blur-md">
            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 block mb-1">Sales Count</span>
            <span id="stat-sales" class="text-2xl font-display font-black text-slate-850 dark:text-slate-100 font-mono">---</span>
            <span class="text-[9px] text-emerald-500 font-bold block mt-1"><i class="fas fa-arrow-up-right mr-1"></i>+8.1% MoM</span>
        </div>

        <!-- Users Count -->
        <a href="users.php" class="p-4 rounded-3xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 shadow-sm backdrop-blur-md block focus:outline-none pointer-events-auto">
            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 block mb-1">Total Users</span>
            <span id="stat-users" class="text-2xl font-display font-black text-slate-850 dark:text-sky-400 font-mono">---</span>
            <span class="text-[9px] text-slate-400 mt-1 block">View profiles <i class="fas fa-angle-right ml-0.5"></i></span>
        </a>

        <!-- Total Products -->
        <a href="products.php" class="p-4 rounded-3xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 shadow-sm backdrop-blur-md block focus:outline-none pointer-events-auto">
            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-550 block mb-1">Files Listed</span>
            <span id="stat-products" class="text-2xl font-display font-black text-slate-850 dark:text-indigo-400 font-mono">---</span>
            <span class="text-[9px] text-slate-400 mt-1 block">Edit catalog <i class="fas fa-angle-right ml-0.5"></i></span>
        </a>
    </div>

    <!-- 2. LINE CHART ANALYTICS DIAGNOSTICS (CHART JS AS REQUESTED) -->
    <div class="p-4 rounded-3xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 mb-6 shadow-sm backdrop-blur-md">
        <h4 class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-3">Live Sales Ticker Graph</h4>
        <div class="h-48 w-full">
            <canvas id="stats-line-chart"></canvas>
        </div>
    </div>

    <!-- 3. RECENT ORDERS LIVE FEED (AJAX POLLING EVERY 30S) -->
    <div class="p-4 rounded-3xl bg-white/70 dark:bg-slate-900/60 border border-slate-200/50 dark:border-white/5 mb-6 shadow-sm backdrop-blur-md">
        <div class="flex items-center justify-between mb-4 pb-2.5 border-b border-slate-100 dark:border-slate-800">
            <h4 class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Live Orders Ticker</h4>
            <div class="flex items-center gap-1 text-[9px] text-emerald-500 font-bold">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-ping"></span>
                <span>Auto refresh 30s</span>
            </div>
        </div>
        
        <div id="live-orders-feed" class="space-y-3.5 divide-y divide-slate-100 dark:divide-slate-800">
            <!-- Populated via AJAX -->
            <p class="text-xs text-slate-400 italic text-center py-2">Polling server orders database...</p>
        </div>
    </div>

</main>

<script>
// Check Authentication on Load 
document.addEventListener('DOMContentLoaded', () => {
    const admin = getSessionAdmin();
    if (!admin) {
        window.location.href = "login.php";
        return;
    }
    
    // Initial fetch logs 
    fetchAdminDashboardMetrics();
    
    // Initialize 30-seconds AJAX polling
    setInterval(() => {
        fetchAdminDashboardMetrics(true); // silent refresh
    }, 30000);
});

function handleAdminSignOut() {
    triggerVibe(40);
    localStorage.removeItem("digitalmohan_current_admin");
    localStorage.removeItem("sellora_current_admin");
    window.location.href = "login.php";
}function fetchAdminDashboardMetrics(isSilentObj = false) {
    if (!isSilentObj) {
        Toast.success("Syncing control room tables...");
    }
    
    // Performance Fix: Fetch single fast consolidated metrics stream with server-side pre-indexed queries
    fetch('/api/admin/metrics')
        .then(res => res.json())
        .then(data => {
            // Compute Bento counts
            document.getElementById('stat-users').textContent = data.users_count;
            document.getElementById('stat-products').textContent = data.products_count;
            document.getElementById('stat-sales').textContent = data.sales_count;
            
            document.getElementById('stat-revenue').textContent = `₹${data.revenue}`;

            // Initialize Line Chart stats with pre-computed server metrics
            renderChartDiagnostics(data.salesData, data.days);
            
            // Render Live orders feed with pre-joined records
            renderLiveOrdersFeed(data.live_orders);
        });
}

function renderChartDiagnostics(salesData, days) {
    const ctx = document.getElementById('stats-line-chart').getContext('2d');
    
    // Destroy existing graph to prevent duplication bugs
    if (window.adminSalesChart) {
        window.adminSalesChart.destroy();
    }

    // Chart.js Configuration
    window.adminSalesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: days,
            datasets: [{
                label: 'Inflow Revenue (₹)',
                data: salesData,
                borderColor: '#0284c7',
                backgroundColor: 'rgba(2, 132, 199, 0.1)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.35,
                pointRadius: 4,
                pointBackgroundColor: '#0284c7'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    grid: { color: 'rgba(255,255,255,0.05)' },
                    ticks: { font: { size: 9 }, color: '#94a3b8', fontAlpha: 0.5 },
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 9 }, color: '#94a3b8' }
                }
            }
        }
    });
}

function renderLiveOrdersFeed(live_orders) {
    const feed = document.getElementById('live-orders-feed');
    
    if (!live_orders || live_orders.length === 0) {
        feed.innerHTML = `<p class="text-xs text-slate-400 italic text-center py-2">No orders recorded in system logs yet.</p>`;
        return;
    }

    feed.innerHTML = live_orders.map(o => {
        let statusColor = "bg-amber-100 text-amber-600 dark:bg-amber-950/40 dark:text-amber-450";
        if (o.status === 'successful') {
            statusColor = "bg-emerald-100 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-450";
        } else if (o.status === 'failed') {
            statusColor = "bg-red-100 text-red-650 dark:bg-red-950/40 dark:text-red-450";
        }

        return `
            <div class="pt-3.5 first:pt-0 flex items-start justify-between">
                <div class="min-w-0 pr-2">
                    <h5 class="text-xs font-bold text-slate-800 dark:text-slate-250 truncate leading-snug">${o.product_title}</h5>
                    <p class="text-[10px] text-slate-455 dark:text-slate-500 font-semibold mt-0.5">${o.user_email} • #${o.id}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <span class="text-xs font-black text-slate-850 dark:text-slate-100 font-mono block">₹${o.amount}</span>
                    <span class="text-[8px] font-black uppercase px-2 py-0.5 rounded-full ${statusColor} inline-block scale-90 mt-1">${o.status}</span>
                </div>
            </div>
        `;
    }).join('');
}
</script>

<!-- Toast for Admin controls -->
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

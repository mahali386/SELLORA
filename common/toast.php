<!-- Reusable Glassmorphic Toast Notification System -->
<div id="toast-container" class="fixed top-5 left-1/2 -translate-x-1/2 z-50 flex flex-col gap-3 pointer-events-none max-w-md w-full px-4"></div>

<script>
const Toast = {
    show: function(message, type = 'success', duration = 3500) {
        const container = document.getElementById('toast-container');
        if (!container) return;
        
        // Vibrate for system feedback (Android WebView / PWA spec)
        if (navigator.vibrate) {
            navigator.vibrate(type === 'error' ? [50, 50, 50] : 30);
        }
        
        let bgColor, textColor, iconClass;
        switch(type) {
            case 'error':
                bgColor = 'rgba(239, 68, 68, 0.85)';
                textColor = 'text-white';
                iconClass = 'fas fa-exclamation-circle';
                break;
            case 'info':
                bgColor = 'rgba(2, 132, 199, 0.85)';
                textColor = 'text-white';
                iconClass = 'fas fa-info-circle';
                break;
            default: // success
                bgColor = 'rgba(16, 185, 129, 0.85)';
                textColor = 'text-white';
                iconClass = 'fas fa-check-circle';
        }
        
        const toast = document.createElement('div');
        toast.className = `flex items-center p-4 rounded-xl shadow-lg backdrop-blur-md border border-white/10 ${textColor} transition-all duration-3500 ease-out transform translate-x-20 opacity-0 pointer-events-auto`;
        toast.style.background = bgColor;
        
        toast.innerHTML = `
            <div class="flex-shrink-0 mr-3">
                <i class="${iconClass} text-lg"></i>
            </div>
            <div class="flex-grow text-sm font-medium pr-2">
                ${message}
            </div>
            <button class="ml-auto text-white/70 hover:text-white transition-colors focus:outline-none" onclick="this.parentElement.remove()">
                <i class="fas fa-times text-xs"></i>
            </button>
        `;
        
        container.appendChild(toast);
        
        // Staggered entry animation
        setTimeout(() => {
            toast.classList.remove('translate-x-20', 'opacity-0');
        }, 10);
        
        // Dismiss after duration
        setTimeout(() => {
            toast.classList.add('opacity-0', 'scale-90');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, duration);
    },
    success: function(m, d) { this.show(m, 'success', d); },
    error: function(m, d) { this.show(m, 'error', d); },
    info: function(m, d) { this.show(m, 'info', d); }
};
</script>

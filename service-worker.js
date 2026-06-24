// DigitalMohan Progressive Web App (PWA) Offline Shell Service Worker

const CACHE_NAME = "digitalmohan-offline-shell-v1";
const ASSETS_TO_PRECACHE = [
    "index.php",
    "products.php",
    "product_detail.php",
    "mydownloads.php",
    "wishlist.php",
    "profile.php",
    "help.php",
    "manifest.json",
    "common/header.php",
    "common/bottom.php",
    "common/sidebar.php",
    "common/toast.php",
    "common/config.php"
];

// Installation: Cache baseline assets
self.addEventListener("install", event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log("[PWA Service Worker] Precaching digital files shell.");
                return cache.addAll(ASSETS_TO_PRECACHE);
            })
            .then(() => self.skipWaiting())
    );
});

// Activation: Clean up old indices
self.addEventListener("activate", event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(name => {
                    if (name !== CACHE_NAME) {
                        console.log("[PWA Service Worker] Discarding stale application cache:", name);
                        return caches.delete(name);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Fetching: Network fallback to local cache
self.addEventListener("fetch", event => {
    // Only cache GET queries to local resources
    if (event.request.method !== "GET" || event.request.url.includes("/api/")) {
        return;
    }
    
    event.respondWith(
        fetch(event.request)
            .then(networkResponse => {
                // If response is valid, update local registry cache dynamically
                if (networkResponse && networkResponse.status === 200) {
                    const clonedResponse = networkResponse.clone();
                    caches.open(CACHE_NAME).then(cache => {
                        cache.put(event.request, clonedResponse);
                    });
                }
                return networkResponse;
            })
            .catch(() => {
                console.log("[PWA Service Worker] Client offline. Directing request to offline index cache.");
                return caches.match(event.request);
            })
    );
});

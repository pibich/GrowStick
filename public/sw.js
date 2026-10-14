const VERSION = 'growstick-v2';
const OFFLINE_URL = '/offline';
const PRECACHE = [OFFLINE_URL, '/'];
const PRIVATE_PATHS = ['/notes'];
const PRIVATE_PREFIXES = [
    '/login',
    '/register',
    '/forgot-password',
    '/livewire',
    '/up',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(VERSION)
            .then((cache) => cache.addAll(PRECACHE))
            .then(() => self.skipWaiting()),
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys.filter((key) => key !== VERSION).map((key) => caches.delete(key)),
            ))
            .then(() => self.clients.claim()),
    );
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    if (url.origin !== self.location.origin) {
        return;
    }

    if (isPrivatePath(url.pathname)) {
        event.respondWith(privateNetworkFirst(request));

        return;
    }

    if (
        url.pathname.startsWith('/snapshots/')
        || url.pathname.startsWith('/build/')
        || url.pathname.startsWith('/storage/')
    ) {
        event.respondWith(cacheFirst(request));

        return;
    }

    event.respondWith(networkFirst(request));
});

function isPrivatePath(pathname) {
    if (PRIVATE_PATHS.includes(pathname)) {
        return true;
    }

    return PRIVATE_PREFIXES.some((prefix) => pathname === prefix || pathname.startsWith(`${prefix}/`));
}

async function cacheFirst(request) {
    const cached = await caches.match(request);

    if (cached) {
        return cached;
    }

    const response = await fetch(request);

    if (response.ok) {
        const cache = await caches.open(VERSION);
        cache.put(request, response.clone());
    }

    return response;
}

async function networkFirst(request) {
    try {
        const response = await fetch(request);

        if (response.ok) {
            const cache = await caches.open(VERSION);
            cache.put(request, response.clone());
        }

        return response;
    } catch (error) {
        const cached = await caches.match(request);

        return cached ?? caches.match(OFFLINE_URL);
    }
}

async function privateNetworkFirst(request) {
    try {
        return await fetch(request);
    } catch (error) {
        const cached = await caches.match(request);

        return cached ?? caches.match(OFFLINE_URL);
    }
}

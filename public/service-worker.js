const cacheName = 'adrie-cache-v1';
const filesToCache = [
  '/',
  '/new',
  '/produit',
  '/profil',
  '/css/styles.css',
  '/css/intervention.css',
  '/js/app.js',
  '/js/dashboard.js',
  '/js/sort.js',
  '/js/scan.js',
  '/images/logo-Adrie.png',
  '/offline.html',
  '/templates/produit/index.html.twig',
  '/templates/intervention/new.html.twig',
  '/templates/profil/index.html.twig',
];

self.addEventListener('install', function (event) {
  event.waitUntil(
    caches.open(cacheName).then(function (cache) {
      return cache.addAll(filesToCache);
    })
  );
});

self.addEventListener('fetch', function (event) {
  event.respondWith(
    fetch(event.request).catch(() => caches.match(event.request).then(function (response) {
      return response || caches.match('/offline.html');
    }))
  );
});

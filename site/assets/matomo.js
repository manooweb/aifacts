// Matomo auto-hébergé, sans cookies (pas de bandeau de consentement nécessaire)
var _paq = window._paq = window._paq || [];
_paq.push(['disableCookies']);
_paq.push(['setDoNotTrack', true]);
// URL capturée avant nettoyage : Matomo y lit la campagne éventuelle
_paq.push(['setCustomUrl', location.href]);
_paq.push(['trackPageView']);
_paq.push(['enableLinkTracking']);
(function() {
  var u = 'https://stats.manooweb.fr/';
  _paq.push(['setTrackerUrl', u + 'matomo.php']);
  _paq.push(['setSiteId', '6']);
  var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
  g.async = true; g.src = u + 'matomo.js'; s.parentNode.insertBefore(g, s);
})();

// Retire les paramètres de suivi de la barre d'adresse (l'URL complète a déjà été transmise à Matomo ci-dessus)
(function() {
  var url = new URL(location.href), avant = url.search;
  ['mtm_', 'pk_', 'utm_'].forEach(function(prefixe) {
    Array.from(url.searchParams.keys()).forEach(function(cle) {
      if (cle.indexOf(prefixe) === 0) url.searchParams.delete(cle);
    });
  });
  if (url.search !== avant) history.replaceState(null, '', url.pathname + url.search + url.hash);
})();

// IT Tour: small enhancements. Every page works without this file.
(function () {
  document.addEventListener('click', function (e) {
    // Mobile menu
    var m = e.target.closest('.menu-btn');
    if (m) {
      var open = document.body.classList.toggle('nav-open');
      m.setAttribute('aria-expanded', open);
    }
    // Close the season pop-up
    var x = e.target.closest('.status .x');
    if (x) x.closest('.status').classList.add('closed');
    // Tab groups: [data-tabs] with buttons [data-tab] and panels [data-panel]
    var t = e.target.closest('[data-tabs] [data-tab]');
    if (t) {
      var g = t.closest('[data-tabs]');
      g.querySelectorAll('[data-tab]').forEach(function (b) { b.setAttribute('aria-selected', b === t); });
      g.querySelectorAll('[data-panel]').forEach(function (p) { p.hidden = p.getAttribute('data-panel') !== t.getAttribute('data-tab'); });
    }
  });
})();

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

// Click-to-load embeds (YouTube, Google Maps): [data-embed] becomes an iframe when its
// .embed-play button is clicked. No autoplay: YouTube counts a view only when its own play
// button is pressed. The "watch on YouTube" link is left alone.
document.addEventListener('click', function (e) {
  var a = e.target.closest('[data-embed] .embed-play');
  if (!a) return;
  e.preventDefault();
  var box = a.closest('[data-embed]');
  var f = document.createElement('iframe');
  f.src = box.getAttribute('data-embed');
  f.title = box.getAttribute('data-title');
  f.allow = 'autoplay; encrypted-media; picture-in-picture; fullscreen';
  f.setAttribute('allowfullscreen', '');
  f.loading = 'lazy';
  box.replaceChildren(f);
  box.classList.add('loaded');
  f.focus();
});

// Countdown: [data-countdown="<ISO start>"] with [data-unit=d|h|m], updated every 30 s.
(function () {
  var box = document.querySelector('[data-countdown]');
  if (!box) return;
  var start = new Date(box.getAttribute('data-countdown')).getTime();
  function pad(n) { return (n < 10 ? '0' : '') + n; }
  function tick() {
    var left = Math.max(0, Math.floor((start - Date.now()) / 60000));
    box.querySelector('[data-unit=d]').textContent = Math.floor(left / 1440);
    box.querySelector('[data-unit=h]').textContent = pad(Math.floor(left / 60) % 24);
    box.querySelector('[data-unit=m]').textContent = pad(left % 60);
  }
  setInterval(tick, 30000);
})();

// Forms marked [data-autosubmit] submit as soon as a select changes; their button is then not needed.
document.querySelectorAll('form[data-autosubmit]').forEach(function (f) {
  var b = f.querySelector('button[type=submit]');
  if (b) b.hidden = true;
  f.addEventListener('change', function () { f.submit(); });
});

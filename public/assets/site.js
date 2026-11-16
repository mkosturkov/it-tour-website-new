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

// Instant search on list pages: form[data-live-search] and its results block [data-results].
// The form is a plain GET form that works without this. Here, results are fetched from the
// same address with the header "X-Partial: results" (the server returns only the block) and
// the address bar follows, so Back/Forward work and every search can be shared:
// - typing searches 300 ms after the last key; the first search of a typing run adds a
//   history entry, the following ones replace it (Back skips the in-between words)
// - a filter change or a page link adds a history entry
// - Back/Forward load the results for that address and put its values back in the form
(function () {
  var form = document.querySelector('form[data-live-search]');
  var box = document.querySelector('[data-results]');
  if (!form || !box || !window.fetch || !window.history.pushState) return;
  var status = document.querySelector('[data-results-status]');
  var input = form.querySelector('input[type=search]');
  var button = form.querySelector('button[type=submit]');
  var base = form.action.split('?')[0];
  var timer, request, typing = false;
  if (button) button.hidden = true;

  // The form's address, without empty values: /talks?q=go rather than /talks?q=go&city=&year=
  function formUrl() {
    var params = new URLSearchParams();
    new FormData(form).forEach(function (v, k) {
      v = String(v).trim();
      if (v) params.append(k, v);
    });
    var qs = params.toString();
    return base + (qs ? '?' + qs : '');
  }

  // Put the values of an address back into the form (after Back/Forward).
  function fillForm(url) {
    var params = new URL(url).searchParams;
    Array.prototype.forEach.call(form.elements, function (el) {
      if (!el.name) return;
      if (el.type === 'checkbox') el.checked = params.get(el.name) === el.value;
      else el.value = params.get(el.name) || '';
    });
  }

  // how: 'push' adds a history entry, 'replace' changes the current one, null leaves history alone
  function load(url, how) {
    if (how && new URL(url, location.href).href === location.href) return;
    if (how === 'push') history.pushState(null, '', url);
    if (how === 'replace') history.replaceState(null, '', url);
    if (request) request.abort();
    request = new AbortController();
    box.setAttribute('aria-busy', 'true');
    fetch(url, { headers: { 'X-Partial': 'results' }, signal: request.signal })
      .then(function (res) {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.text();
      })
      .then(function (html) {
        box.innerHTML = html;
        box.removeAttribute('aria-busy');
        var count = box.querySelector('.count-line, .empty');
        if (status && count) status.textContent = count.textContent;
      })
      .catch(function (err) {
        if (err.name !== 'AbortError') location.href = url;   // let the server show the page
      });
  }

  function search() {
    clearTimeout(timer);
    load(formUrl(), typing ? 'replace' : 'push');
    typing = true;
  }

  input.addEventListener('input', function () {
    clearTimeout(timer);
    timer = setTimeout(search, 300);
  });
  input.addEventListener('blur', function () { typing = false; });
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    search();
  });
  form.addEventListener('change', function (e) {
    if (e.target === input) return;   // typing is handled above
    clearTimeout(timer);
    typing = false;
    load(formUrl(), 'push');
  });
  box.addEventListener('click', function (e) {
    var a = e.target.closest('.pager a');
    if (!a || e.ctrlKey || e.metaKey || e.shiftKey || e.button !== 0) return;
    e.preventDefault();
    typing = false;
    load(a.href, 'push');
    form.scrollIntoView({ block: 'start' });
  });
  window.addEventListener('popstate', function () {
    clearTimeout(timer);
    typing = false;
    fillForm(location.href);
    load(location.href, null);
  });
})();

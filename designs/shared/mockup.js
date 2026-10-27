// Mockup-only helper: a floating switch that flips the season banner between its three states.
// Pages style each state with [data-season="upcoming|live|off"] on <html>.
(function () {
  var STATES = [
    ['upcoming', 'Предстои'],
    ['live', 'На живо'],
    ['off', 'Извън сезона'],
  ];
  var KEY = 'ittour-mock-season';
  var root = document.documentElement;

  function read() {
    try { return localStorage.getItem(KEY); } catch (e) { return null; }
  }
  function write(v) {
    try { localStorage.setItem(KEY, v); } catch (e) {}
  }
  function set(state) {
    root.dataset.season = state;
    write(state);
    document.querySelectorAll('.mock-switch button').forEach(function (b) {
      b.setAttribute('aria-pressed', b.dataset.state === state ? 'true' : 'false');
    });
  }

  var fromUrl = new URLSearchParams(location.search).get('season');
  var initial = fromUrl || read();
  root.dataset.season = STATES.some(function (s) { return s[0] === initial; }) ? initial : 'upcoming';

  document.addEventListener('DOMContentLoaded', function () {
    var bar = document.createElement('div');
    bar.className = 'mock-switch';
    bar.setAttribute('role', 'group');
    bar.setAttribute('aria-label', 'Макет: състояние на сезона');
    bar.innerHTML = '<span>Макет · банер:</span>' + STATES.map(function (s) {
      return '<button type="button" data-state="' + s[0] + '">' + s[1] + '</button>';
    }).join('');
    bar.addEventListener('click', function (e) {
      var b = e.target.closest('button');
      if (b) set(b.dataset.state);
    });
    document.body.appendChild(bar);
    set(root.dataset.season);
  });
})();

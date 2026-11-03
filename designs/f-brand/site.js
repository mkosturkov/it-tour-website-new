// Version F: shared page chrome (header, footer, season pop-up) and the mockup accent switch.
// Pages mark where the chrome goes with <div data-slot="header|footer|status"></div>
// and set <body data-page="..."> to highlight the current nav item.
(function () {
  var L = 'img/';

  // ---- Accent switch (mockup only): ?accent=<key>, remembered per browser ----
  // Event pages always use their city colour; the switch changes the hub pages.
  var OPTS = [['navy', 'Синьо', '#375992'], ['teal', 'Тюркоаз', '#019295'], ['mint', 'Мента', '#6ec3aa'], ['coral', 'Корал', '#f18a85'], ['orange', 'Оранжево', '#f38c16'], ['graphite', 'Графит', '#2f2f2f']];
  var KEY = 'ittour-mock-accent-f';
  function read() { try { return localStorage.getItem(KEY); } catch (e) { return null; } }
  function save(v) { try { localStorage.setItem(KEY, v); } catch (e) {} }
  var cur = new URLSearchParams(location.search).get('accent') || read() || 'navy';
  if (!OPTS.some(function (o) { return o[0] === cur; })) cur = 'navy';
  document.documentElement.setAttribute('data-accent', cur);

  function accentSwitch() {
    var bar = document.createElement('div');
    bar.className = 'accent-switch';
    function label() { var o = OPTS.filter(function (o) { return o[0] === cur; })[0]; return '<i style="--sw:' + o[2] + '"></i>Акцент: ' + o[1] + ' ▾'; }
    bar.innerHTML = '<button class="toggle" aria-expanded="false">' + label() + '</button><div class="opts">' + OPTS.map(function (o) {
      return '<button data-a="' + o[0] + '" style="--sw:' + o[2] + '" aria-pressed="' + (o[0] === cur) + '"><i></i>' + o[1] + '</button>';
    }).join('') + '</div>';
    bar.addEventListener('click', function (e) {
      var t = e.target.closest('.toggle');
      if (t) { var open = bar.classList.toggle('open'); t.setAttribute('aria-expanded', open); return; }
      var b = e.target.closest('button[data-a]'); if (!b) return;
      cur = b.dataset.a; save(cur);
      document.documentElement.setAttribute('data-accent', cur);
      bar.querySelectorAll('button[data-a]').forEach(function (x) { x.setAttribute('aria-pressed', x === b); });
      bar.querySelector('.toggle').innerHTML = label();
    });
    document.body.appendChild(bar);
  }

  // ---- Chrome ----
  var NAV = [['about', 'about.html', 'За IT Tour'], ['stops', 'index.html#stops', 'Спирки'], ['news', 'news.html', 'Новини'], ['talks', 'talks.html', 'Лекции'], ['archive', 'archive.html', 'Архив'], ['team', 'team.html', 'Екип'], ['involve', 'index.html#involve', 'Включи се']];

  function header(page) {
    return '<div class="cbar" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i></div>' +
      '<header><div class="wrap nav">' +
      '<a class="logo" href="index.html" aria-label="IT Tour, начало"><span class="mark"></span></a>' +
      '<nav id="main-nav"><ul>' + NAV.map(function (n) {
        return '<li><a href="' + n[1] + '"' + (n[0] === page ? ' aria-current="page"' : '') + '>' + n[2] + '</a></li>';
      }).join('') + '</ul></nav>' +
      '<div class="nav-right"><span class="lang"><b>BG</b> / EN</span>' +
      '<a class="btn btn-primary btn-sm" href="index.html#subscribe">Абонирай се</a>' +
      '<button class="menu-btn" aria-controls="main-nav" aria-expanded="false" aria-label="Меню"><span></span></button></div>' +
      '</div></header>';
  }

  var CITIES = [['plovdiv', 'PlovdivConf', 'event-plovdiv.html'], ['tarnovo', 'TarnovoConf', 'archive.html'], ['burgas', 'BurgasConf', 'event-burgas.html'], ['varna', 'VarnaConf', 'event-varna.html'], ['ruse', 'RuseConf', 'event-ruse.html']];

  function footer() {
    return '<footer><div class="wrap foot">' +
      '<div><a class="logo" href="index.html" aria-label="IT Tour, начало"><span class="mark"></span></a><p>Безплатни еднодневни IT конференции из България от 2012 г.</p></div>' +
      '<div><h4>Конференции</h4><ul>' + CITIES.map(function (c) {
        return '<li><a href="' + c[2] + '"><span class="cdot" style="--c:var(--' + c[0] + ')"></span>' + c[1] + '</a></li>';
      }).join('') + '</ul></div>' +
      '<div><h4>IT Tour</h4><ul><li><a href="about.html">За нас</a></li><li><a href="team.html">Екип</a></li><li><a href="news.html">Новини</a></li><li><a href="archive.html">Архив</a></li><li><a href="talks.html">Лекции</a></li><li><a href="speakers.html">Лектори</a></li><li><a href="code-of-conduct.html">Кодекс на поведение</a></li></ul></div>' +
      '<div><h4>Включи се</h4><ul><li><a href="speak.html">Стани лектор</a></li><li><a href="sponsors.html">Спонсори</a></li><li><a href="volunteer.html">Доброволци</a></li></ul></div>' +
      '</div><div class="cbar" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i></div></footer>';
  }

  // Season pop-up: next stop before an event, live now during one, nothing off-season.
  function status() {
    return '<div data-show="upcoming"><aside class="status" style="--c:var(--burgas)" aria-label="Статус на сезона">' +
      '<div class="top"><span class="kind"><span class="dot"></span>Следваща спирка</span><button class="x" aria-label="Затвори">×</button></div>' +
      '<div class="main"><img src="' + L + 'burgas.svg" alt=""><div><h3>BurgasConf · 25.07</h3><p>След 10 дни · Колеж по туризъм</p></div></div>' +
      '<div class="actions"><a class="btn btn-primary" href="event-burgas.html#schedule">Програмата</a><a class="btn btn-ghost" href="event-burgas.html#rsvp">Идвам</a></div>' +
      '</aside></div>' +
      '<div data-show="live"><aside class="status" style="--c:var(--live)" aria-label="Статус на сезона">' +
      '<div class="top"><span class="kind"><span class="dot live"></span>На живо сега</span><button class="x" aria-label="Затвори">×</button></div>' +
      '<div class="main"><img src="' + L + 'burgas.svg" alt=""><div><h3>BurgasConf 2026</h3><p>Колеж по туризъм, Бургас</p></div></div>' +
      '<div class="now">На сцената: <b>Смарт хоум: Решенията, които не са за подценяване</b>, Иво Гергов</div>' +
      '<div class="actions"><a class="btn btn-primary" href="event-burgas.html#live">▶ Гледай на живо</a><a class="btn btn-ghost" href="#">YouTube</a></div>' +
      '</aside></div>';
  }

  document.addEventListener('DOMContentLoaded', function () {
    var page = document.body.getAttribute('data-page');
    var slots = { header: header(page), footer: footer(), status: status() };
    document.querySelectorAll('[data-slot]').forEach(function (el) { el.outerHTML = slots[el.getAttribute('data-slot')] || ''; });
    accentSwitch();
  });

  document.addEventListener('click', function (e) {
    var x = e.target.closest('.status .x');
    if (x) x.closest('.status').classList.add('closed');
    var m = e.target.closest('.menu-btn');
    if (m) { var open = document.body.classList.toggle('nav-open'); m.setAttribute('aria-expanded', open); }
    // Simple tab groups: <div data-tabs> with buttons[data-tab] and panels[data-panel]
    var t = e.target.closest('[data-tabs] [data-tab]');
    if (t) {
      var g = t.closest('[data-tabs]');
      g.querySelectorAll('[data-tab]').forEach(function (b) { b.setAttribute('aria-selected', b === t); });
      g.querySelectorAll('[data-panel]').forEach(function (p) { p.hidden = p.getAttribute('data-panel') !== t.getAttribute('data-tab'); });
    }
    // "I'm coming" demo: bump the counter once
    var r = e.target.closest('[data-rsvp]');
    if (r) {
      e.preventDefault();
      var box = r.closest('.rsvp'), n = box.querySelector('.count b');
      if (!box.classList.contains('done')) { n.textContent = +n.textContent + 1; box.classList.add('done'); }
    }
  });

  document.addEventListener('submit', function (e) {
    e.preventDefault();
    var f = e.target, ok = f.querySelector('.form-ok');
    if (ok) { ok.hidden = false; }
  });
})();

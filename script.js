// BM2R — navigation mobile
(function () {
  function init() {
    var burger = document.querySelector('.burger');
    var menu = document.getElementById('mobile-menu');
    var closeBtn = document.querySelector('.mobile-menu-close');
    if (!burger || !menu) return;

    function open() {
      menu.classList.add('open');
      burger.classList.add('open');
      document.body.style.overflow = 'hidden';
      burger.setAttribute('aria-expanded', 'true');
    }
    function close() {
      menu.classList.remove('open');
      burger.classList.remove('open');
      document.body.style.overflow = '';
      burger.setAttribute('aria-expanded', 'false');
    }
    burger.addEventListener('click', function () {
      menu.classList.contains('open') ? close() : open();
    });
    if (closeBtn) closeBtn.addEventListener('click', close);
    menu.querySelectorAll('a').forEach(function (a) {
      a.addEventListener('click', close);
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') close();
    });
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // ----- Filtres galerie (page Réalisations) -----
  function initFilters() {
    var btns = document.querySelectorAll('.filter-btn');
    if (!btns.length) return;
    var items = document.querySelectorAll('.ba-item');
    btns.forEach(function (b) {
      b.addEventListener('click', function () {
        btns.forEach(function (x) { x.classList.remove('active'); });
        b.classList.add('active');
        var f = b.getAttribute('data-filter');
        items.forEach(function (it) {
          var show = (f === 'all' || it.getAttribute('data-cat') === f);
          it.classList.toggle('is-hidden', !show);
        });
      });
    });
  }

  // ----- Formulaire contact (démo, sans backend) -----
  function initForm() {
    var form = document.getElementById('contact-form');
    if (!form) return;
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!form.checkValidity()) { form.reportValidity(); return; }
      form.style.display = 'none';
      var ok = document.getElementById('form-success');
      if (ok) ok.classList.add('show');
    });
  }

  // ----- Comparateur avant / après (curseur déplaçable) -----
  function initCompare() {
    var nodes = document.querySelectorAll('.ba-compare');
    if (!nodes.length) return;
    nodes.forEach(function (el) {
      var handle = el.querySelector('.bac-handle');
      function setPos(clientX) {
        var r = el.getBoundingClientRect();
        var p = (clientX - r.left) / r.width * 100;
        p = Math.max(2, Math.min(98, p));
        el.style.setProperty('--pos', p + '%');
        if (handle) handle.setAttribute('aria-valuenow', Math.round(p));
        el.classList.add('is-touched');
      }
      var dragging = false;
      el.addEventListener('pointerdown', function (e) {
        dragging = true;
        try { el.setPointerCapture(e.pointerId); } catch (err) {}
        setPos(e.clientX);
        e.preventDefault();
      });
      el.addEventListener('pointermove', function (e) {
        if (dragging) setPos(e.clientX);
      });
      function stop() { dragging = false; }
      el.addEventListener('pointerup', stop);
      el.addEventListener('pointercancel', stop);
      if (handle) {
        handle.addEventListener('keydown', function (e) {
          var cur = parseFloat(el.style.getPropertyValue('--pos')) || 50;
          if (e.key === 'ArrowLeft') cur -= 4;
          else if (e.key === 'ArrowRight') cur += 4;
          else return;
          cur = Math.max(2, Math.min(98, cur));
          el.style.setProperty('--pos', cur + '%');
          handle.setAttribute('aria-valuenow', Math.round(cur));
          el.classList.add('is-touched');
          e.preventDefault();
        });
      }
    });
  }

  function initExtras() { initFilters(); initForm(); initCompare(); }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initExtras);
  } else {
    initExtras();
  }
})();

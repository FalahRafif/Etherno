<script>
  document.addEventListener('DOMContentLoaded', function () {
    const header = document.querySelector('.public-header');
    const toggle = document.querySelector('.menu-toggle');
    const menu = document.getElementById('public-menu');

    if (!header || !toggle || !menu) {
      return;
    }

    header.classList.add('js-ready');

    const closeMenu = function () {
      header.classList.remove('is-open');
      toggle.setAttribute('aria-expanded', 'false');
    };

    const openMenu = function () {
      header.classList.add('is-open');
      toggle.setAttribute('aria-expanded', 'true');
    };

    toggle.addEventListener('click', function () {
      if (header.classList.contains('is-open')) {
        closeMenu();
      } else {
        openMenu();
      }
    });

    menu.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', closeMenu);
    });

    document.addEventListener('click', function (event) {
      if (!header.contains(event.target)) {
        closeMenu();
      }
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        closeMenu();
      }
    });
  });
</script>
</body>
</html>

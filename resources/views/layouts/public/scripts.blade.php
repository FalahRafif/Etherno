<script>
  document.addEventListener('DOMContentLoaded', function () {
    const header = document.querySelector('.public-header');
    const toggle = document.querySelector('.menu-toggle');
    const menu = document.getElementById('public-menu');

    if (header && toggle && menu) {
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
    }

    const dateCheckInput = document.getElementById('booking_date_check');
    const dateFormInput = document.getElementById('booking_date');
    const summary = document.getElementById('availability_summary');
    const morningCard = document.querySelector('[data-slot-card="morning"]');
    const eveningCard = document.querySelector('[data-slot-card="evening"]');
    const morningStatus = document.querySelector('[data-slot-status="morning"]');
    const eveningStatus = document.querySelector('[data-slot-status="evening"]');
    const previewForm = document.getElementById('booking_form_preview');

    if (!dateCheckInput || !dateFormInput || !summary || !morningCard || !eveningCard || !morningStatus || !eveningStatus) {
      return;
    }

    const sampleAvailability = {
      '2026-06-12': { morning: 'full', evening: 'limited' },
      '2026-06-13': { morning: 'limited', evening: 'available' },
      '2026-06-14': { morning: 'full', evening: 'full' },
      '2026-06-20': { morning: 'available', evening: 'limited' },
      '2026-06-21': { morning: 'limited', evening: 'full' }
    };

    const statusLabel = {
      available: 'Tersedia',
      limited: 'Tersisa 1 slot',
      full: 'Penuh',
      unknown: 'Belum dipilih'
    };

    function renderSlot(card, statusEl, statusValue) {
      card.classList.remove('status-available', 'status-limited', 'status-full', 'status-unknown');
      card.classList.add('status-' + statusValue);
      statusEl.textContent = statusLabel[statusValue] || statusLabel.unknown;
    }

    function resolveAvailability(dateValue) {
      if (!dateValue) {
        return { morning: 'unknown', evening: 'unknown' };
      }

      if (sampleAvailability[dateValue]) {
        return sampleAvailability[dateValue];
      }

      const dayOfWeek = new Date(dateValue + 'T00:00:00').getDay();
      if (dayOfWeek === 0 || dayOfWeek === 6) {
        return { morning: 'limited', evening: 'available' };
      }

      return { morning: 'available', evening: 'available' };
    }

    function renderAvailability(dateValue) {
      const status = resolveAvailability(dateValue);

      renderSlot(morningCard, morningStatus, status.morning);
      renderSlot(eveningCard, eveningStatus, status.evening);

      if (!dateValue) {
        summary.textContent = 'Pilih tanggal untuk melihat status slot.';
        return;
      }

      if (status.morning === 'full' && status.evening === 'full') {
        summary.textContent = 'Tanggal dipilih sudah penuh. Silakan pilih tanggal lain.';
        return;
      }

      if (status.morning === 'limited' || status.evening === 'limited') {
        summary.textContent = 'Tanggal dipilih masih tersedia terbatas. Segera kirim request untuk diproses admin.';
        return;
      }

      summary.textContent = 'Tanggal dipilih tersedia. Slot akan fix setelah DP berhasil diverifikasi.';
    }

    dateCheckInput.addEventListener('change', function () {
      dateFormInput.value = dateCheckInput.value;
      renderAvailability(dateCheckInput.value);
    });

    dateFormInput.addEventListener('change', function () {
      dateCheckInput.value = dateFormInput.value;
      renderAvailability(dateFormInput.value);
    });

    if (previewForm) {
      previewForm.addEventListener('submit', function (event) {
        event.preventDefault();
      });
    }

    renderAvailability(dateCheckInput.value || dateFormInput.value);
  });
</script>
</body>
</html>

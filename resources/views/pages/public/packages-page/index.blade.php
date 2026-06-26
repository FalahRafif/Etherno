@extends('layouts.guest.guest')

@section('content')
<section class="section-block container packages-page-section" id="all-packages">
  <div class="section-heading">
    <p class="eyebrow">Semua Produk</p>
    <h2>Daftar lengkap paket Etherno</h2>
    <p class="section-lead">Temukan paket wedding maupun non wedding yang paling sesuai dengan momen istimewa Anda.</p>
  </div>

  {{-- Tab nav --}}
  <div class="pkg-tabs" role="tablist" aria-label="Kategori paket">
    <button class="pkg-tab is-active" role="tab" aria-selected="true" aria-controls="pkg-panel-wedding" data-tab="wedding" id="tab-wedding">
      <span class="pkg-tab-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
      </span>
      Paket Wedding
    </button>
    <button class="pkg-tab" role="tab" aria-selected="false" aria-controls="pkg-panel-nonwedding" data-tab="non_wedding" id="tab-nonwedding">
      <span class="pkg-tab-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      </span>
      Paket Non Wedding
    </button>
  </div>

  {{-- Package grid area --}}
  <div class="pkg-panel" id="pkg-panel-wedding" role="tabpanel" aria-labelledby="tab-wedding">
    <div class="package-grid" id="pkg-grid-wedding"></div>
    <div class="pkg-pagination" id="pkg-pagination-wedding"></div>
  </div>
  <div class="pkg-panel" id="pkg-panel-nonwedding" role="tabpanel" aria-labelledby="tab-nonwedding" hidden>
    <div class="package-grid" id="pkg-grid-nonwedding"></div>
    <div class="pkg-pagination" id="pkg-pagination-nonwedding"></div>
  </div>

  <div class="packages-page-actions">
    <a class="cta cta-outline" href="{{ route('home') }}#packages">Kembali ke Halaman Utama</a>
    <a class="cta" href="{{ route('booking.page') }}">Ajukan Booking</a>
  </div>
</section>
@endsection

@push('scripts')
<script>
(function () {
  "use strict";

  var API_URL = "{{ route('api.public.packages.index') }}";

  var state = {
    wedding: { page: 1, lastPage: 1, loading: false, loaded: false },
    non_wedding: { page: 1, lastPage: 1, loading: false, loaded: false },
  };

  var activeTab = "wedding";

  function pkgTagLabel(index) {
    if (index === 0) return "Best Seller";
    if (index === 1) return "Paling Direkomendasikan";
    return "Value Terbaik";
  }

  function renderSkeleton(gridEl) {
    var html = "";
    for (var i = 0; i < 3; i++) {
      html += '<article class="package pkg-skeleton" aria-hidden="true">'
        + '<div class="pkg-skeleton-img"></div>'
        + '<div class="pkg-skeleton-line pkg-skeleton-line-short"></div>'
        + '<div class="pkg-skeleton-line pkg-skeleton-line-title"></div>'
        + '<div class="pkg-skeleton-line"></div>'
        + '<div class="pkg-skeleton-line pkg-skeleton-line-short"></div>'
        + '</article>';
    }
    gridEl.innerHTML = html;
  }

  function renderPackages(gridEl, items) {
    if (!items || items.length === 0) {
      gridEl.innerHTML = '<article class="package-empty"><p>Belum ada paket aktif di kategori ini. Hubungi kami untuk rekomendasi.</p></article>';
      return;
    }
    var html = "";
    items.forEach(function (pkg, index) {
      var tag = pkgTagLabel(index);
      var featuredClass = index === 1 ? " package-featured" : " package-soft";
      var thumb = pkg.thumbnail_url
        ? '<figure class="package-image"><img src="' + escHtml(pkg.thumbnail_url) + '" alt="' + escHtml(pkg.name) + '" loading="lazy"></figure>'
        : "";
      var benefits = (pkg.benefits || []).map(function (b) { return "<li>" + escHtml(b) + "</li>"; }).join("");
      var desc = pkg.description || "Ideal untuk Anda yang ingin hasil dokumentasi rapi, emosional, dan siap dibagikan.";
      html += '<article class="package' + featuredClass + '">'
        + thumb
        + '<p class="package-tag">' + escHtml(tag) + '</p>'
        + '<h3>' + escHtml(pkg.name) + '</h3>'
        + '<div class="price">' + escHtml(pkg.price_formatted) + '</div>'
        + '<p class="package-copy">' + escHtml(desc) + '</p>'
        + '<ul class="package-list">' + (benefits || '<li>Benefit detail sedang diperbarui.</li>') + '</ul>'
        + '</article>';
    });
    gridEl.innerHTML = html;
  }

  function renderPagination(paginEl, currentPage, lastPage, tabKey) {
    if (lastPage <= 1) {
      paginEl.innerHTML = "";
      return;
    }
    var html = '<div class="pkg-pagin-inner">';
    html += '<button class="pkg-pagin-btn" data-tab="' + tabKey + '" data-page="' + (currentPage - 1) + '" aria-label="Halaman sebelumnya"'
      + (currentPage <= 1 ? ' disabled' : '') + '>'
      + '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>'
      + '</button>';
    for (var p = 1; p <= lastPage; p++) {
      html += '<button class="pkg-pagin-page' + (p === currentPage ? ' is-active' : '') + '" data-tab="' + tabKey + '" data-page="' + p + '">' + p + '</button>';
    }
    html += '<button class="pkg-pagin-btn" data-tab="' + tabKey + '" data-page="' + (currentPage + 1) + '" aria-label="Halaman berikutnya"'
      + (currentPage >= lastPage ? ' disabled' : '') + '>'
      + '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>'
      + '</button>';
    html += '</div>';
    paginEl.innerHTML = html;

    paginEl.querySelectorAll("button[data-page]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        if (btn.disabled) return;
        var page = parseInt(btn.getAttribute("data-page"), 10);
        var tab = btn.getAttribute("data-tab");
        loadTab(tab, page, true);
      });
    });
  }

  function loadTab(tabKey, page, scrollToTop) {
    var s = state[tabKey];
    if (s.loading) return;
    s.loading = true;
    s.page = page;

    var gridId = tabKey === "wedding" ? "pkg-grid-wedding" : "pkg-grid-nonwedding";
    var paginId = tabKey === "wedding" ? "pkg-pagination-wedding" : "pkg-pagination-nonwedding";
    var gridEl = document.getElementById(gridId);
    var paginEl = document.getElementById(paginId);

    renderSkeleton(gridEl);
    if (paginEl) paginEl.innerHTML = "";

    var url = API_URL + "?type=" + encodeURIComponent(tabKey) + "&page=" + page;

    fetch(url, { headers: { "Accept": "application/json", "X-Requested-With": "XMLHttpRequest" } })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        s.loading = false;
        s.loaded = true;
        s.lastPage = data.last_page || 1;
        s.page = data.current_page || page;
        renderPackages(gridEl, data.items || []);
        renderPagination(paginEl, s.page, s.lastPage, tabKey);
        if (scrollToTop) {
          var section = document.getElementById("all-packages");
          if (section) section.scrollIntoView({ behavior: "smooth", block: "start" });
        }
      })
      .catch(function () {
        s.loading = false;
        if (gridEl) gridEl.innerHTML = '<article class="package-empty"><p>Gagal memuat paket. Silakan coba lagi.</p></article>';
      });
  }

  function switchTab(tabKey) {
    if (tabKey === activeTab) return;
    activeTab = tabKey;

    document.querySelectorAll(".pkg-tab").forEach(function (btn) {
      var isActive = btn.getAttribute("data-tab") === tabKey;
      btn.classList.toggle("is-active", isActive);
      btn.setAttribute("aria-selected", isActive ? "true" : "false");
    });

    document.querySelectorAll(".pkg-panel").forEach(function (panel) {
      var panelTab = panel.id === "pkg-panel-wedding" ? "wedding" : "non_wedding";
      if (panelTab === tabKey) {
        panel.removeAttribute("hidden");
      } else {
        panel.setAttribute("hidden", "");
      }
    });

    if (!state[tabKey].loaded) {
      loadTab(tabKey, 1, false);
    }
  }

  function escHtml(str) {
    if (str == null) return "";
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;");
  }

  // Init
  document.querySelectorAll(".pkg-tab").forEach(function (btn) {
    btn.addEventListener("click", function () {
      switchTab(btn.getAttribute("data-tab"));
    });
  });

  // Load initial tab
  loadTab("wedding", 1, false);
})();
</script>
@endpush
